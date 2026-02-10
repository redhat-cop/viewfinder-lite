<?php
require_once __DIR__ . '/ViewfinderException.php';

/**
 * Configuration Exception
 *
 * Thrown when configuration is missing or invalid
 */
class ConfigurationException extends ViewfinderException {

    /**
     * Application error code for configuration errors
     *
     * @var string
     */
    protected string $errorCode = 'CONFIG_ERROR';

    /**
     * Create exception for missing configuration key
     *
     * @param string $key The missing configuration key
     * @return self
     */
    public static function missingKey(string $key): self {
        return new self(
            sprintf('Missing configuration key: %s', $key),
            'System configuration error. Please contact your administrator.',
            [
                'config_key' => $key,
                'error_type' => 'missing_key'
            ]
        );
    }

    /**
     * Create exception for invalid configuration type
     *
     * @param string $key The configuration key
     * @param string $expected Expected type
     * @param string $actual Actual type received
     * @return self
     */
    public static function invalidType(string $key, string $expected, string $actual): self {
        return new self(
            sprintf('Invalid type for configuration key %s: expected %s, got %s', $key, $expected, $actual),
            'System configuration error. Please contact your administrator.',
            [
                'config_key' => $key,
                'expected_type' => $expected,
                'actual_type' => $actual,
                'error_type' => 'invalid_type'
            ]
        );
    }
}
