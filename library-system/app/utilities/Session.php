<?php

// app/utilities/Session.php
class Session {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    // Set session variable
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    // Get session variable
    public function get($key) {
        return $_SESSION[$key] ?? null;
    }
    
    // Remove session variable
    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    // Check if session variable exists
    public function exists($key) {
        return isset($_SESSION[$key]);
    }
    
    // Destroy session
    public function destroy() {
        session_unset();
        session_destroy();
    }
    
    // Set flash message (available only for next request)
    public function setFlash($key, $message) {
        $_SESSION['_flash'][$key] = $message;
    }
    
    // Get flash message and remove it
    public function getFlash($key) {
        $message = $_SESSION['_flash'][$key] ?? null;
        
        if (isset($_SESSION['_flash'][$key])) {
            unset($_SESSION['_flash'][$key]);
        }
        
        return $message;
    }
    
    // Check if flash message exists
    public function hasFlash($key) {
        return isset($_SESSION['_flash'][$key]);
    }
}

