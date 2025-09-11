<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-container {
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
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 30px;
            text-align: center;
        }
        .success-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: bounce 1s ease-in-out;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        .asset-info {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
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
    <div class="success-container">
        <div class="card">
            <div class="card-header">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>Confirmation Successful!</h2>
                <p class="mb-0">Thank you for confirming receipt of your asset</p>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-success">
                    <i class="fas fa-thumbs-up me-2"></i>
                    <strong>Great!</strong> Your asset assignment has been successfully confirmed.
                </div>

                @if(isset($asset))
                <div class="asset-info">
                    <h5><i class="fas fa-laptop icon"></i>Confirmed Asset</h5>
                    <p><strong>Asset Tag:</strong> {{ $asset->asset_tag }}</p>
                    <p><strong>Asset Name:</strong> {{ $asset->asset_name }}</p>
                    <p><strong>Status:</strong> <span class="badge bg-success">Deployed Tagged</span></p>
                    <p class="mb-0"><strong>Confirmed on:</strong> {{ now()->format('F j, Y \\a\\t g:i A') }}</p>
                </div>
                @endif

                <div class="next-steps">
                    <h5><i class="fas fa-list-check icon"></i>What's Next?</h5>
                    <ul class="mb-0">
                        <li>Your asset is now officially assigned to you</li>
                        <li>Please take good care of the asset and follow company policies</li>
                        <li>Contact IT support if you experience any issues with the asset</li>
                        <li>Remember to return the asset when it's no longer needed</li>
                    </ul>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Need Help?</strong> If you have any questions about your asset or need technical support, 
                    please contact the IT department.
                </div>

                <div class="text-center">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        This confirmation was processed on {{ now()->format('F j, Y \\a\\t g:i A') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>