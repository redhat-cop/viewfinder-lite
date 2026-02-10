<?php
/**
 * Profile-specific Exception
 *
 * Handles errors during profile creation, validation, and management
 */

require_once __DIR__ . '/ViewfinderException.php';

class ProfileException extends ViewfinderException {

    /**
     * Application-specific error code for profile operations
     *
     * @var string
     */
    protected string $errorCode = 'PROFILE_ERROR';

    /**
     * Create exception for when a profile already exists
     *
     * @param string $profileName The name of the existing profile
     * @return ProfileException
     */
    public static function profileExists(string $profileName): ProfileException {
        return new self(
            "Profile '{$profileName}' already exists",
            "A profile with this name already exists. Please choose a different name.",
            ['profile' => $profileName]
        );
    }

    /**
     * Create exception for invalid profile name
     *
     * @param string $profileName The invalid profile name
     * @param string $reason Why the name is invalid
     * @return ProfileException
     */
    public static function invalidName(string $profileName, string $reason): ProfileException {
        return new self(
            "Invalid profile name: {$reason}",
            "Profile name is invalid. Use only letters, numbers, and underscores.",
            ['profile' => $profileName, 'reason' => $reason]
        );
    }

    /**
     * Create exception for profile generation failure
     *
     * @param string $profileName The profile being generated
     * @param string $step The step where generation failed
     * @return ProfileException
     */
    public static function generationFailed(string $profileName, string $step): ProfileException {
        return new self(
            "Profile generation failed at step: {$step}",
            "Failed to create profile. Changes have been rolled back.",
            ['profile' => $profileName, 'step' => $step]
        );
    }

    /**
     * Create exception for file write failures
     *
     * @param string $filePath The file that couldn't be written
     * @param string $reason The reason for failure
     * @return ProfileException
     */
    public static function fileWriteFailed(string $filePath, string $reason): ProfileException {
        return new self(
            "Failed to write file '{$filePath}': {$reason}",
            "Could not create profile file. Please check permissions.",
            ['file' => $filePath, 'reason' => $reason]
        );
    }

    /**
     * Create exception for backup operation failures
     *
     * @param string $filePath The file that couldn't be backed up
     * @return ProfileException
     */
    public static function backupFailed(string $filePath): ProfileException {
        return new self(
            "Failed to create backup of '{$filePath}'",
            "Could not backup system files. Operation aborted.",
            ['file' => $filePath]
        );
    }
}
