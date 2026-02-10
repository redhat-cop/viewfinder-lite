<?php
require_once __DIR__ . '/../includes/Exceptions/ViewfinderException.php';
require_once __DIR__ . '/../includes/Exceptions/FileSystemException.php';
require_once __DIR__ . '/../includes/Exceptions/DataValidationException.php';
require_once __DIR__ . '/../includes/Exceptions/ViewfinderJsonException.php';
require_once __DIR__ . '/../includes/Exceptions/ConfigurationException.php';
require_once __DIR__ . '/../includes/Logger.php';

/**
 * Error Handler Class
 *
 * Handles uncaught exceptions and fatal errors, displaying user-friendly error pages
 */
class ErrorHandler {

    /**
     * Register exception and shutdown handlers
     *
     * @return void
     */
    public static function register(): void {
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Handle uncaught exceptions
     *
     * @param \Throwable $exception The uncaught exception
     * @return void
     */
    public static function handleException(\Throwable $exception): void {
        // Log the exception
        Logger::logException($exception);

        // Get template for this exception type
        $template = self::getTemplateForException($exception);

        // Generate unique error ID for support reference
        $errorId = self::getErrorId();

        // Get user-friendly message
        $userMessage = self::getUserMessage($exception);

        // Prepare template data
        $data = [
            'error_id' => $errorId,
            'user_message' => $userMessage,
            'exception' => $exception,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Render error page
        self::renderErrorPage($template, $data);

        // Exit with error code
        exit(1);
    }

    /**
     * Handle fatal errors on shutdown
     *
     * @return void
     */
    public static function handleShutdown(): void {
        $error = error_get_last();

        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            // Log fatal error
            Logger::critical('Fatal error occurred', [
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
                'type' => $error['type']
            ]);

            // Display system error page
            $errorId = self::getErrorId();
            $data = [
                'error_id' => $errorId,
                'user_message' => 'A critical system error occurred. Our team has been notified.',
                'exception' => null,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            self::renderErrorPage('system-error', $data);
        }
    }

    /**
     * Get template name for exception type
     *
     * @param \Throwable $exception The exception
     * @return string Template name (without .php extension)
     */
    private static function getTemplateForException(\Throwable $exception): string {
        if ($exception instanceof FileSystemException) {
            return 'file-not-found';
        }

        if ($exception instanceof DataValidationException) {
            return 'validation-error';
        }

        if ($exception instanceof ViewfinderJsonException) {
            return 'json-error';
        }

        if ($exception instanceof ConfigurationException) {
            return 'json-error'; // Configuration errors use JSON error template
        }

        // Default to system error for all other exceptions
        return 'system-error';
    }

    /**
     * Render error page template
     *
     * @param string $template Template name (without .php extension)
     * @param array $data Data to pass to template
     * @return void
     */
    private static function renderErrorPage(string $template, array $data): void {
        $templatePath = __DIR__ . '/templates/' . $template . '.php';

        if (!file_exists($templatePath)) {
            // Fallback to system error if template not found
            $templatePath = __DIR__ . '/templates/system-error.php';
        }

        // Set HTTP response code
        http_response_code(500);

        // Extract data for template
        extract($data);

        // Include template
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            // Ultimate fallback - plain text error
            echo '<html><body style="font-family:sans-serif;padding:50px;background:#151515;color:#fff;">';
            echo '<h1 style="color:#CC0000;">System Error</h1>';
            echo '<p>An unexpected error occurred. Error ID: ' . htmlspecialchars($data['error_id'] ?? 'unknown') . '</p>';
            echo '<p>Please contact your administrator.</p>';
            echo '</body></html>';
        }
    }

    /**
     * Generate unique error ID
     *
     * @return string Unique error ID (e.g., ERR-20251217-ABCD1234)
     */
    private static function getErrorId(): string {
        return sprintf(
            'ERR-%s-%s',
            date('Ymd'),
            strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8))
        );
    }

    /**
     * Get user-friendly message from exception
     *
     * @param \Throwable $exception The exception
     * @return string User-friendly message
     */
    private static function getUserMessage(\Throwable $exception): string {
        if ($exception instanceof ViewfinderException) {
            return $exception->getUserMessage();
        }

        // Default message for non-ViewfinderException exceptions
        return 'An unexpected error occurred. Please try again or contact support.';
    }
}
