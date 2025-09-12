<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\RedirectionService;
use App\Services\SecurityAuditService;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * The redirection service instance.
     *
     * @var RedirectionService
     */
    protected $redirectionService;

    /**
     * The security audit service instance.
     *
     * @var SecurityAuditService
     */
    protected $securityAuditService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RedirectionService $redirectionService, SecurityAuditService $securityAuditService)
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
        $this->redirectionService = $redirectionService;
        $this->securityAuditService = $securityAuditService;
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);
        $email = $request->input('email');

        // Check for brute force attempts
        if ($this->securityAuditService->checkBruteForce($email, $request)) {
            $this->securityAuditService->logSuspiciousActivity(null, $request, 'Brute force attempt detected', [
                'email' => $email,
                'attempts_detected' => true
            ]);
            
            return $this->sendLockoutResponse($request);
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            
            $this->securityAuditService->logSuspiciousActivity(null, $request, 'Rate limit exceeded', [
                'email' => $email
            ]);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            // Get the authenticated user
            $user = Auth::user();
            
            // Check if user account is active (1 = active, 0 = inactive, 2 = suspended)
            if ($user->status !== 1) {
                Auth::logout();
                
                $statusMessage = $user->status === 0 ? 'Account inactive' : 'Account suspended';
                $this->securityAuditService->logFailedLogin($user->email, $request, $statusMessage);
                
                throw ValidationException::withMessages([
                    $this->username() => ['Your account is not active. Please contact the administrator.'],
                ]);
            }
            
            // Log successful authentication
            $this->securityAuditService->logSuccessfulLogin($user, $request, null);

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
        
        $this->securityAuditService->logFailedLogin($email, $request, 'Invalid credentials');

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Use RedirectionService to determine appropriate redirect URL
        $redirectUrl = $this->redirectionService->getRedirectUrl($user);
        
        // Log successful login with comprehensive details
        $this->securityAuditService->logSuccessfulLogin($user, $request, $redirectUrl);
        
        return redirect()->intended($redirectUrl);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout event with comprehensive details
        if ($user) {
            $this->securityAuditService->logLogout($user, $request);
        }

        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 204)
            : redirect('/');
    }
}
