<?php
/**
 * Base Exception for Viewfinder Application
 *
 * All custom exceptions extend this class to provide consistent error handling
 * with context data, user-friendly messages, and application error codes.
 */
class ViewfinderException extends Exception {

    /**
     * Additional context data for logging and debugging
     *
     * @var array
     */
    protected array $context = [];

    /**
     * User-friendly error message (safe to display to end users)
     *
     * @var string
     */
    protected string $userMessage = '';

    /**
     * Application-specific error code
     *
     * @var string
     */
    protected string $errorCode = 'VIEWFINDER_ERROR';

    /**
     * Constructor
     *
     * @param string $message Technical error message (for logging)
     * @param string $userMessage User-friendly message (for display)
     * @param array $context Additional context data
     * @param int $code Numeric error code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message,
        string $userMessage = '',
        array $context = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->userMessage = $userMessage ?: 'An unexpected error occurred. Please try again.';
        $this->context = $context;
    }

    /**
     * Get additional context data
     *
     * @return array Context data for logging
     */
    public function getContext(): array {
        return $this->context;
    }

    /**
     * Get user-friendly error message
     *
     * @return string Safe message for end users
     */
    public function getUserMessage(): string {
        return $this->userMessage;
    }

    /**
     * Get application error code
     *
     * @return string Application-specific error code
     */
    public function getErrorCode(): string {
        return $this->errorCode;
    }

    /**
     * Convert exception to array for logging
     *
     * @return array Exception data for structured logging
     */
    public function toArray(): array {
        return [
            'error_code' => $this->errorCode,
            'message' => $this->getMessage(),
            'user_message' => $this->userMessage,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'context' => $this->context,
            'trace' => $this->getTraceAsString()
        ];
    }
}
