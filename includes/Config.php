<?php
/**
 * Application Configuration
 * Centralized configuration management for Viewfinder Lite
 */
class Config {

    /**
     * Application settings
     */
    const APP_NAME = 'Viewfinder Lite';
    const APP_VERSION = '1.0.0';

    /**
     * File paths
     */
    private static $basePath = null;

    /**
     * Get base application path
     *
     * @return string
     */
    public static function getBasePath() {
        if (self::$basePath === null) {
            self::$basePath = dirname(__DIR__);
        }
        return self::$basePath;
    }

    /**
     * Error handling configuration
     */
    const ERROR_REPORTING_ENABLED = true;
    const LOG_ERRORS = true;
    const DISPLAY_ERRORS = false; // Set to true only in development

    /**
     * Security configuration
     */
    const ENABLE_CSRF_PROTECTION = true;
    const SESSION_TIMEOUT = 3600; // 1 hour in seconds
}
