<?php
require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/Logger.php';
require_once __DIR__ . '/Exceptions/FileSystemException.php';
require_once __DIR__ . '/Exceptions/ViewfinderJsonException.php';
require_once __DIR__ . '/Exceptions/DataValidationException.php';

/**
 * Security Helper Class
 * Provides input validation and output sanitization functions
 */
class Security {

    /**
     * Validate and sanitize profile parameter
     *
     * @param string $profile User-provided profile value
     * @return string Validated profile or default 'Security'
     */
    public static function validateProfile($profile) {
        Logger::debug('Validating profile', ['input' => $profile]);

        if (empty($profile) || !Config::isValidProfile($profile)) {
            Logger::info('Invalid or empty profile provided, using default', [
                'provided' => $profile,
                'default' => 'Security'
            ]);
            return 'Security'; // Safe default
        }

        Logger::debug('Profile validated successfully', ['profile' => $profile]);
        return $profile;
    }

    /**
     * Validate line of business parameter
     *
     * @param string $lob User-provided LOB value
     * @return string|null Validated LOB or null if invalid
     */
    public static function validateLOB($lob) {
        Logger::debug('Validating LOB', ['input' => $lob]);

        if (empty($lob) || !Config::isValidLOB($lob)) {
            Logger::info('Invalid or empty LOB provided', ['provided' => $lob]);
            return null;
        }

        Logger::debug('LOB validated successfully', ['lob' => $lob]);
        return $lob;
    }

    /**
     * Sanitize output for HTML display (prevents XSS)
     *
     * @param string $string Input string
     * @return string Escaped string safe for HTML output
     */
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Validate framework names against whitelist from JSON
     *
     * @param array $frameworks User-provided framework array
     * @param array $validFrameworks Valid framework names from compliance.json
     * @return array Validated frameworks
     */
    public static function validateFrameworks($frameworks, $validFrameworks) {
        Logger::debug('Validating frameworks', [
            'provided_count' => is_array($frameworks) ? count($frameworks) : 0,
            'valid_count' => count($validFrameworks)
        ]);

        if (!is_array($frameworks)) {
            Logger::warning('Frameworks parameter is not an array', [
                'provided_type' => gettype($frameworks)
            ]);
            return [];
        }

        // Filter to only allowed frameworks
        $validatedFrameworks = array_filter($frameworks, function($framework) use ($validFrameworks) {
            return in_array($framework, $validFrameworks, true);
        });

        if (count($validatedFrameworks) !== count($frameworks)) {
            Logger::info('Some frameworks filtered out during validation', [
                'provided_count' => count($frameworks),
                'validated_count' => count($validatedFrameworks)
            ]);
        }

        Logger::debug('Frameworks validated successfully', [
            'validated_frameworks' => $validatedFrameworks
        ]);

        return $validatedFrameworks;
    }

    /**
     * Build safe file path for LOB includes
     *
     * @param string $lob Validated LOB value
     * @param string $profile Validated profile value
     * @return string|null Safe file path or null if file doesn't exist
     */
    public static function getLOBFilePath($lob, $profile) {
        Logger::debug('Resolving LOB file path', ['lob' => $lob, 'profile' => $profile]);

        // Validate inputs first
        //$lob = self::validateLOB($lob);
        $profile = self::validateProfile($profile);

        if ($lob === null) {
            Logger::debug('LOB is null, returning null');
            return null;
        }

        // Build safe path using Config
        $baseDir = Config::getLOBContentPath();

        if ($profile == "Security") {
            $fileName = "lob-{$lob}.html";
        } else {
            $fileName = "lob-{$lob}-{$profile}.html";
        }
        $fullPath = $baseDir . $fileName;

        Logger::debug('Built LOB file path', [
            'base_dir' => $baseDir,
            'file_name' => $fileName,
            'full_path' => $fullPath
        ]);

        // Verify file exists and is within allowed directory
        if (file_exists($fullPath) && realpath($fullPath) !== false &&
            strpos(realpath($fullPath), realpath($baseDir)) === 0) {
            Logger::info('LOB file found', ['file_path' => $fullPath]);
            return $fullPath;
        }

        Logger::info('LOB file not found or path validation failed', [
            'full_path' => $fullPath,
            'exists' => file_exists($fullPath),
            'realpath' => realpath($fullPath)
        ]);

        return null;
    }

    /**
     * Build safe file path for compliance framework includes
     *
     * @param string $linkFile Framework link from JSON
     * @return string|null Safe file path or null if file doesn't exist
     */
    public static function getFrameworkFilePath($linkFile) {
        Logger::debug('Resolving framework file path', ['link_file' => $linkFile]);

        $baseDir = Config::getComplianceContentPath();

        // Only allow files from compliance directory
        $fileName = basename($linkFile);
        $fullPath = $baseDir . $fileName;

        Logger::debug('Built framework file path', [
            'base_dir' => $baseDir,
            'file_name' => $fileName,
            'full_path' => $fullPath
        ]);

        // Verify file exists and is within allowed directory
        if (file_exists($fullPath) && realpath($fullPath) !== false &&
            strpos(realpath($fullPath), realpath($baseDir)) === 0) {
            Logger::info('Framework file found', ['file_path' => $fullPath]);
            return $fullPath;
        }

        Logger::info('Framework file not found or path validation failed', [
            'full_path' => $fullPath,
            'exists' => file_exists($fullPath)
        ]);

        return null;
    }

    /**
     * Safely load and decode JSON file
     *
     * @param string $filePath Path to JSON file
     * @return array Decoded JSON data
     * @throws FileSystemException If file not found or cannot be read
     * @throws JsonException If JSON parsing fails
     */
    public static function loadJSON($filePath) {
        Logger::info('Loading JSON file', ['file_path' => $filePath]);

        if (!file_exists($filePath)) {
            Logger::error('JSON file not found', ['file_path' => $filePath]);
            throw FileSystemException::fileNotFound($filePath);
        }

        $content = @file_get_contents($filePath);
        if ($content === false) {
            Logger::error('Failed to read JSON file', ['file_path' => $filePath]);
            throw FileSystemException::readFailed($filePath, 'Permission denied or file unreadable');
        }

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Logger::error('JSON decode failed', [
                'file_path' => $filePath,
                'error' => json_last_error_msg(),
                'error_code' => json_last_error()
            ]);
            throw ViewfinderJsonException::decodeFailed($filePath, json_last_error(), json_last_error_msg());
        }

        Logger::info('JSON file loaded successfully', [
            'file_path' => $filePath,
            'keys' => is_array($data) ? array_keys($data) : [],
            'item_count' => is_array($data) ? count($data) : 0
        ]);

        return $data;
    }

    /**
     * Get safe controls file path
     *
     * @param string $profile Validated profile name
     * @return string Safe controls file path
     */
    public static function getControlsFilePath($profile) {
        $profile = self::validateProfile($profile);
        return Config::getControlsPath($profile);
    }
}
