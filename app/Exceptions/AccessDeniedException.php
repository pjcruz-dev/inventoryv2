<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AccessDeniedException extends Exception
{
    /**
     * The error code for this exception.
     *
     * @var string
     */
    protected $errorCode;

    /**
     * Additional context data.
     *
     * @var array
     */
    protected $context;

    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param string $errorCode
     * @param array $context
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(
        string $message = 'Access denied',
        string $errorCode = 'ACCESS_DENIED',
        array $context = [],
        int $code = 403,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    /**
     * Get the error code.
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Get the context data.
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param Request $request
     * @return Response
     */
    public function render(Request $request): Response
    {
        // Log the access denied attempt
        Log::warning('Access denied exception thrown', [
            'message' => $this->getMessage(),
            'error_code' => $this->errorCode,
            'context' => $this->context,
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);

        // Handle AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
                'error_code' => $this->errorCode,
                'context' => $this->context
            ], $this->getCode());
        }

        // Handle regular web requests
        $redirectUrl = $this->determineRedirectUrl();
        
        return redirect($redirectUrl)
            ->with('error', $this->getMessage())
            ->with('error_code', $this->errorCode)
            ->with('error_context', $this->context);
    }

    /**
     * Determine the appropriate redirect URL based on user context.
     *
     * @return string
     */
    protected function determineRedirectUrl(): string
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Try to use RedirectionService if available
            if (app()->bound('App\Services\RedirectionService')) {
                $redirectionService = app('App\Services\RedirectionService');
                return $redirectionService->getRedirectUrl($user);
            }
            
            // Fallback based on user role
            if ($user->role) {
                switch ($user->role->name) {
                    case 'IT Administrator':
                        return route('dashboard');
                    case 'IT Staff':
                        return route('assets.index');
                    case 'Department Manager':
                        return route('assets.index');
                    case 'Employee':
                        return route('assets.index');
                    default:
                        return route('dashboard');
                }
            }
            
            return route('dashboard');
        }
        
        return route('login');
    }

    /**
     * Create an access denied exception for insufficient permissions.
     *
     * @param string $permission
     * @param array $context
     * @return static
     */
    public static function insufficientPermissions(string $permission, array $context = []): static
    {
        return new static(
            "You do not have the required permission: {$permission}",
            'INSUFFICIENT_PERMISSIONS',
            array_merge($context, ['required_permission' => $permission])
        );
    }

    /**
     * Create an access denied exception for inactive account.
     *
     * @param array $context
     * @return static
     */
    public static function accountInactive(array $context = []): static
    {
        return new static(
            'Your account has been deactivated. Please contact the administrator.',
            'ACCOUNT_INACTIVE',
            $context
        );
    }

    /**
     * Create an access denied exception for role restrictions.
     *
     * @param string $requiredRole
     * @param array $context
     * @return static
     */
    public static function roleRestricted(string $requiredRole, array $context = []): static
    {
        return new static(
            "Access restricted to users with role: {$requiredRole}",
            'ROLE_RESTRICTED',
            array_merge($context, ['required_role' => $requiredRole])
        );
    }
}