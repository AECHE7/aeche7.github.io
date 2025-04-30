<?php

require_once __DIR__ . '/Session.php';

// app/utilities/CSRF.php
class CSRF {
    private $session;
    private $tokenName = 'csrf_token';
    
    public function __construct() {
        $this->session = new Session();
    }
    
    // Generate CSRF token
    public function generate() {
        $token = bin2hex(random_bytes(32));
        $this->session->set($this->tokenName, $token);
        return $token;
    }
    
    // Verify CSRF token
    public function verify($token) {
        $storedToken = $this->session->get($this->tokenName);
        
        if (!$storedToken || $token !== $storedToken) {
            return false;
        }
        
        // Remove token after verification for single use
        $this->session->remove($this->tokenName);
        return true;
    }
    
    // Get current token (or generate if none exists)
    public function getToken() {
        $token = $this->session->get($this->tokenName);
        
        if (!$token) {
            $token = $this->generate();
        }
        
        return $token;
    }
    
    // Create hidden input field with CSRF token
    public function getTokenField() {
        $token = $this->getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}


