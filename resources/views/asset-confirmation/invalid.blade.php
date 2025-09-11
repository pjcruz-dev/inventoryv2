<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Confirmation Link</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .error-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-top: 10vh;
        }
        
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        
        .help-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .help-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .help-item:last-child {
            margin-bottom: 0;
        }
        
        .help-icon {
            color: #6c757d;
            margin-right: 0.75rem;
            margin-top: 0.25rem;
        }
        
        .btn-home {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }
        
        .btn-contact {
            background: #6c757d;
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-left: 1rem;
        }
        
        .btn-contact:hover {
            background: #5a6268;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container text-center">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h2 class="text-danger mb-3">Invalid Confirmation Link</h2>
            
            <div class="alert alert-danger">
                <i class="fas fa-times-circle me-2"></i>
                {{ $message }}
            </div>

            <div class="help-section">
                <h5 class="mb-3"><i class="fas fa-question-circle me-2"></i>What might have happened?</h5>
                
                <div class="help-item">
                    <i class="fas fa-clock help-icon"></i>
                    <div class="text-start">
                        <strong>Link Expired:</strong> Confirmation links are valid for a limited time for security reasons.
                    </div>
                </div>
                
                <div class="help-item">
                    <i class="fas fa-check-circle help-icon"></i>
                    <div class="text-start">
                        <strong>Already Used:</strong> This confirmation link may have already been used to confirm or decline the asset assignment.
                    </div>
                </div>
                
                <div class="help-item">
                    <i class="fas fa-link help-icon"></i>
                    <div class="text-start">
                        <strong>Broken Link:</strong> The link may have been copied incorrectly or corrupted during transmission.
                    </div>
                </div>
                
                <div class="help-item">
                    <i class="fas fa-trash help-icon"></i>
                    <div class="text-start">
                        <strong>Request Cancelled:</strong> The asset assignment request may have been cancelled by the administrator.
                    </div>
                </div>
            </div>

            <div class="help-section">
                <h5 class="mb-3"><i class="fas fa-lightbulb me-2"></i>What can you do?</h5>
                
                <div class="help-item">
                    <i class="fas fa-envelope help-icon"></i>
                    <div class="text-start">
                        <strong>Check Your Email:</strong> Look for a more recent confirmation email in your inbox.
                    </div>
                </div>
                
                <div class="help-item">
                    <i class="fas fa-phone help-icon"></i>
                    <div class="text-start">
                        <strong>Contact Support:</strong> Reach out to your IT administrator for assistance.
                    </div>
                </div>
                
                <div class="help-item">
                    <i class="fas fa-redo help-icon"></i>
                    <div class="text-start">
                        <strong>Request New Link:</strong> Ask your administrator to resend the confirmation email.
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <p class="text-muted mb-3">
                    <i class="fas fa-shield-alt me-1"></i>
                    For security reasons, confirmation links have limited validity.
                </p>
                
                <div>
                    <a href="{{ url('/') }}" class="btn-home">
                        <i class="fas fa-home me-2"></i>Return to Home
                    </a>
                    
                    <a href="mailto:support@company.com" class="btn-contact">
                        <i class="fas fa-envelope me-2"></i>Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>