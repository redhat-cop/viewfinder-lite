<?php
/**
 * Logger Wrapper Class
 *
 * Simple logging wrapper that uses PHP's built-in error_log() function
 * Logs are sent to the web server's error log (OpenShift/container-friendly)
 */
class Logger {

    /**
     * Whether debug logging is enabled
     *
     * @var bool
     */
    private static bool $debugEnabled = false;

    /**
     * Configure logger settings
     *
     * @param string $logPath Ignored - kept for backward compatibility
     * @param string $logLevel Minimum log level (DEBUG, INFO, WARNING, ERROR, CRITICAL)
     * @return void
     */
    public static function configure(string $logPath = '', string $logLevel = 'INFO'): void {
        self::$debugEnabled = (strtoupper($logLevel) === 'DEBUG');
    }

    /**
     * Log INFO level message
     *
     * @param string $message Log message
     * @param array $context Additional context data (ignored)
     * @return void
     */
    public static function info(string $message, array $context = []): void {
        // INFO messages are not logged to reduce noise
    }

    /**
     * Log WARNING level message
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public static function warning(string $message, array $context = []): void {
        self::log('WARNING', $message, $context);
    }

    /**
     * Log ERROR level message
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public static function error(string $message, array $context = []): void {
        self::log('ERROR', $message, $context);
    }

    /**
     * Log DEBUG level message
     *
     * @param string $message Log message
     * @param array $context Additional context data (only logged if debug enabled)
     * @return void
     */
    public static function debug(string $message, array $context = []): void {
        if (self::$debugEnabled) {
            self::log('DEBUG', $message, $context);
        }
    }

    /**
     * Log CRITICAL level message
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public static function critical(string $message, array $context = []): void {
        self::log('CRITICAL', $message, $context);
    }

    /**
     * Log exception with full context
     *
     * @param \Throwable $exception Exception to log
     * @return void
     */
    public static function logException(\Throwable $exception): void {
        $message = sprintf(
            '%s: %s in %s:%d',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        self::log('ERROR', $message);
    }

    /**
     * Internal logging method
     *
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    private static function log(string $level, string $message, array $context = []): void {
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        error_log("[$level] $message$contextStr");
    }
}
