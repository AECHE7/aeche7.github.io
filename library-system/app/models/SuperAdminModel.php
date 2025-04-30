<?php

// app/models/SuperAdminModel.php
class SuperAdminModel extends UserModel {
    // This model inherits all functionality from UserModel
    // but can have specialized methods for Super Admin operations
    
    // Get list of all admins
    public function getAllAdmins() {
        return $this->findAll('role = :role', ['role' => 'admin']);
    }
    
    // Create admin account
    public function createAdmin($adminData) {
        $adminData['role'] = 'admin';
        return $this->create($adminData);
    }
}
