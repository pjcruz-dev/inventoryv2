<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 30px;
            text-align: center;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-5px);
            }
            75% {
                transform: translateX(5px);
            }
        }
        .help-section {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .contact-info {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .icon {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="card">
            <div class="card-header">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2>Confirmation Error</h2>
                <p class="mb-0">{{ $message ?? 'Unable to process your confirmation' }}</p>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-2"></i>
                    <strong>Sorry!</strong> We couldn't process your asset confirmation request.
                </div>

                <div class="help-section">
                    <h5><i class="fas fa-question-circle icon"></i>What might have happened?</h5>
                    <ul class="mb-0">
                        <li>The confirmation link may have expired</li>
                        <li>The asset has already been confirmed</li>
                        <li>The confirmation token is invalid or corrupted</li>
                        <li>The asset assignment may have been cancelled</li>
                    </ul>
                </div>

                <div class="contact-info">
                    <h5><i class="fas fa-headset icon"></i>Need Assistance?</h5>
                    <p>If you believe this is an error or if you need help with your asset assignment, please contact the IT department:</p>
                    <ul class="mb-0">
                        <li><i class="fas fa-envelope icon"></i>Email: it-support@company.com</li>
                        <li><i class="fas fa-phone icon"></i>Phone: (555) 123-4567</li>
                        <li><i class="fas fa-clock icon"></i>Hours: Monday - Friday, 8:00 AM - 5:00 PM</li>
                    </ul>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Tip:</strong> When contacting support, please provide the confirmation link you clicked 
                    and any error messages you received.
                </div>

                @if(isset($token))
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-key me-1"></i>
                        Token: {{ substr($token, 0, 8) }}...
                    </small>
                </div>
                @endif

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Error occurred on {{ now()->format('F j, Y \\a\\t g:i A') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>