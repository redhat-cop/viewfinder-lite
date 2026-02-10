<?php
require_once __DIR__ . '/ViewfinderException.php';

/**
 * Viewfinder JSON Exception
 *
 * Thrown when JSON parsing or validation fails
 */
class ViewfinderJsonException extends ViewfinderException {

    /**
     * Application error code for JSON errors
     *
     * @var string
     */
    protected string $errorCode = 'JSON_ERROR';

    /**
     * Create exception for JSON decode failure
     *
     * @param string $filePath Path to the JSON file
     * @param int $jsonError JSON error code from json_last_error()
     * @param string $jsonErrorMsg JSON error message from json_last_error_msg()
     * @return self
     */
    public static function decodeFailed(string $filePath, int $jsonError, string $jsonErrorMsg): self {
        return new self(
            sprintf('JSON decode failed for file %s: %s', $filePath, $jsonErrorMsg),
            'Configuration error. The system configuration file is malformed. Please contact your administrator.',
            [
                'file_path' => $filePath,
                'json_error_code' => $jsonError,
                'json_error_message' => $jsonErrorMsg,
                'error_type' => 'decode_failed'
            ]
        );
    }

    /**
     * Create exception for invalid JSON structure
     *
     * @param string $filePath Path to the JSON file
     * @param string $reason Description of what's wrong with the structure
     * @return self
     */
    public static function invalidStructure(string $filePath, string $reason): self {
        return new self(
            sprintf('Invalid JSON structure in file %s: %s', $filePath, $reason),
            'Configuration error. The system configuration file has an invalid structure. Please contact your administrator.',
            [
                'file_path' => $filePath,
                'reason' => $reason,
                'error_type' => 'invalid_structure'
            ]
        );
    }
}
