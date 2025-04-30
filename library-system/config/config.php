<?php
// config/Config.php

class Config
{
    // Database settings
    public static function dbHost() { return 'localhost:3307'; }
    public static function dbName() { return 'library_sys'; }
    public static function dbUser() { return 'root'; }
    public static function dbPass() { return ''; }

    // Application settings
    public static function appName() { return 'Library Management System'; }
    public static function baseUrl() { return 'http://localhost/library-management-system'; }

    // Session timeout in seconds
    public static function sessionTimeout() { return 1800; } // 30 minutes
}
