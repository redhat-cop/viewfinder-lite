<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Error - Viewfinder</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Red Hat Display', 'Overpass', 'Helvetica Neue', Arial, sans-serif;
            background-color: #151515;
            color: #FFFFFF;
            line-height: 1.6;
            padding: 20px;
        }

        .error-container {
            max-width: 800px;
            margin: 60px auto;
            text-align: center;
        }

        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
            color: #C9190B;
        }

        h1 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #FFFFFF;
        }

        .error-message {
            font-size: 18px;
            margin-bottom: 30px;
            color: #D2D2D2;
        }

        .error-details {
            background-color: #212121;
            border-left: 4px solid #C9190B;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
            border-radius: 4px;
        }

        .error-details p {
            margin: 8px 0;
            font-size: 14px;
            color: #D2D2D2;
        }

        .error-details strong {
            color: #FFFFFF;
            font-weight: 600;
        }

        .error-id {
            font-family: 'Courier New', monospace;
            background-color: #2A2A2A;
            padding: 4px 8px;
            border-radius: 3px;
            color: #F0AB00;
        }

        .support-info {
            background-color: #2A2A2A;
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
        }

        .support-info p {
            margin: 5px 0;
            font-size: 13px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #CC0000;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #A30000;
        }

        .footer {
            margin-top: 60px;
            font-size: 14px;
            color: #6A6E73;
        }

        @media (max-width: 768px) {
            .error-container {
                margin: 30px auto;
            }

            h1 {
                font-size: 24px;
            }

            .error-icon {
                font-size: 60px;
            }

            .error-message {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">❌</div>

        <h1>System Error</h1>

        <p class="error-message">
            <?php echo htmlspecialchars($user_message); ?>
        </p>

        <div class="error-details">
            <p><strong>Error ID:</strong> <span class="error-id"><?php echo htmlspecialchars($error_id); ?></span></p>
            <p><strong>Timestamp:</strong> <?php echo htmlspecialchars($timestamp); ?></p>
            <p><strong>What happened:</strong> An unexpected system error has occurred.</p>
            <p><strong>Status:</strong> Our team has been automatically notified and is investigating the issue.</p>

            <div class="support-info">
                <p><strong>Need immediate assistance?</strong></p>
                <p>Please contact your system administrator with the Error ID displayed above.</p>
                <p>This ID will help us quickly identify and resolve the issue.</p>
            </div>

            <p style="margin-top: 15px;"><strong>What you can do:</strong></p>
            <ul style="list-style-position: inside; padding-left: 20px; margin-top: 8px;">
                <li>Try refreshing the page or returning to the home page</li>
                <li>Wait a few minutes and try again</li>
                <li>If the problem persists, contact support with the Error ID</li>
            </ul>
        </div>

        <a href="/" class="btn">Return to Home</a>

        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> Red Hat - Viewfinder Maturity Assessment Tool</p>
        </div>
    </div>
</body>
</html>
