<?php
require_once __DIR__ . '/ViewfinderException.php';

/**
 * File System Exception
 *
 * Thrown when file system operations fail (file not found, read failures, invalid paths)
 */
class FileSystemException extends ViewfinderException {

    /**
     * Application error code for file system errors
     *
     * @var string
     */
    protected string $errorCode = 'FILE_SYSTEM_ERROR';

    /**
     * Create exception for file not found
     *
     * @param string $filePath Path to the missing file
     * @return self
     */
    public static function fileNotFound(string $filePath): self {
        return new self(
            sprintf('File not found: %s', $filePath),
            'The requested resource could not be found. Please contact your administrator.',
            ['file_path' => $filePath, 'error_type' => 'file_not_found']
        );
    }

    /**
     * Create exception for file read failure
     *
     * @param string $filePath Path to the file that couldn't be read
     * @param string $reason Optional reason for failure
     * @return self
     */
    public static function readFailed(string $filePath, string $reason = ''): self {
        $message = sprintf('Failed to read file: %s', $filePath);
        if ($reason) {
            $message .= sprintf(' (%s)', $reason);
        }

        return new self(
            $message,
            'Unable to read the requested file. Please contact your administrator.',
            [
                'file_path' => $filePath,
                'error_type' => 'read_failed',
                'reason' => $reason
            ]
        );
    }

    /**
     * Create exception for invalid file path
     *
     * @param string $filePath The invalid path
     * @return self
     */
    public static function invalidPath(string $filePath): self {
        return new self(
            sprintf('Invalid file path: %s', $filePath),
            'The requested file path is invalid. Please contact your administrator.',
            [
                'file_path' => $filePath,
                'error_type' => 'invalid_path'
            ]
        );
    }
}
