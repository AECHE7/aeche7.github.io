<?php


// app/models/AdminModel.php
class AdminModel extends UserModel {
    // Admin-specific methods
    
    // Get count of borrowers
    public function countBorrowers() {
        return $this->count("role IN ('students', 'staff', 'others')");
    }
}
