<?php

// app/utilities/PasswordHash.php
class PasswordHash {
    // Hash password
    public function hash($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    // Verify password
    public function verify($password, $hash) {
        return password_verify($password, $hash);
    }
    
    // Check if password needs rehash
    public function needsRehash($hash) {
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }
}

