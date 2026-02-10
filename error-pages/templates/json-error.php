<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Error - Viewfinder</title>
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
            color: #EC7A08;
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
            border-left: 4px solid #EC7A08;
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

        .technical-info {
            background-color: #2A2A2A;
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }

        .admin-note {
            background-color: #3C2415;
            border: 1px solid #EC7A08;
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
        }

        .admin-note h3 {
            font-size: 16px;
            color: #EC7A08;
            margin-bottom: 10px;
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
        <div class="error-icon">🔧</div>

        <h1>Configuration Error</h1>

        <p class="error-message">
            <?php echo htmlspecialchars($user_message); ?>
        </p>

        <div class="error-details">
            <p><strong>Error ID:</strong> <span class="error-id"><?php echo htmlspecialchars($error_id); ?></span></p>
            <p><strong>Timestamp:</strong> <?php echo htmlspecialchars($timestamp); ?></p>
            <p><strong>What happened:</strong> A configuration file has an error that prevents the system from loading properly.</p>

            <?php if ($exception instanceof ViewfinderJsonException || $exception instanceof ConfigurationException): ?>
                <?php $context = $exception->getContext(); ?>
                <?php if (!empty($context['file_path'])): ?>
                    <div class="technical-info">
                        <p><strong>File:</strong> <?php echo htmlspecialchars(basename($context['file_path'])); ?></p>
                        <?php if (!empty($context['json_error_message'])): ?>
                            <p><strong>Issue:</strong> <?php echo htmlspecialchars($context['json_error_message']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="admin-note">
                <h3>For Administrators:</h3>
                <p>This error indicates a problem with the JSON configuration files.</p>
                <p>Please check the server logs for detailed information using the Error ID above.</p>
                <p>Common causes:</p>
                <ul style="list-style-position: inside; padding-left: 20px; margin-top: 8px;">
                    <li>Syntax errors in JSON files (missing commas, brackets, quotes)</li>
                    <li>Corrupted or incomplete file uploads</li>
                    <li>File permissions preventing read access</li>
                </ul>
            </div>

            <p style="margin-top: 15px;"><strong>What you can do:</strong> Contact your system administrator to resolve this configuration issue.</p>
        </div>

        <a href="/" class="btn">Return to Home</a>

        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> Red Hat - Viewfinder Maturity Assessment Tool</p>
        </div>
    </div>
</body>
</html>
