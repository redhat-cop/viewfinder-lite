<?php
require_once __DIR__ . '/ViewfinderException.php';

/**
 * Data Validation Exception
 *
 * Thrown when user-provided data fails validation (invalid profile, LOB, frameworks)
 */
class DataValidationException extends ViewfinderException {

    /**
     * Application error code for validation errors
     *
     * @var string
     */
    protected string $errorCode = 'VALIDATION_ERROR';

    /**
     * Create exception for invalid profile
     *
     * @param string $provided The invalid profile provided
     * @param array $validProfiles List of valid profile names
     * @return self
     */
    public static function invalidProfile(string $provided, array $validProfiles): self {
        return new self(
            sprintf('Invalid profile: %s', $provided),
            'Invalid profile selected. Please choose from the available assessment profiles.',
            [
                'field' => 'profile',
                'provided_value' => $provided,
                'valid_values' => $validProfiles,
                'error_type' => 'invalid_profile'
            ]
        );
    }

    /**
     * Create exception for invalid Line of Business
     *
     * @param string $provided The invalid LOB provided
     * @param array $validLOBs List of valid LOB names
     * @return self
     */
    public static function invalidLOB(string $provided, array $validLOBs): self {
        return new self(
            sprintf('Invalid LOB: %s', $provided),
            'Invalid line of business selected. Please choose from the available options.',
            [
                'field' => 'lob',
                'provided_value' => $provided,
                'valid_values' => $validLOBs,
                'error_type' => 'invalid_lob'
            ]
        );
    }

    /**
     * Create exception for invalid framework
     *
     * @param string $provided The invalid framework provided
     * @param array $validFrameworks List of valid framework names
     * @return self
     */
    public static function invalidFramework(string $provided, array $validFrameworks): self {
        return new self(
            sprintf('Invalid framework: %s', $provided),
            'Invalid compliance framework selected. Please choose from the available options.',
            [
                'field' => 'framework',
                'provided_value' => $provided,
                'valid_values' => $validFrameworks,
                'error_type' => 'invalid_framework'
            ]
        );
    }
}
