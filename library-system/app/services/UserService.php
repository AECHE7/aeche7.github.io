<?php

require_once __DIR__ . '/../core/BaseService.php';
require_once __DIR__ . '/../models/TransactionModel.php';

// app/services/UserService.php
class UserService extends BaseService {
    private $passwordHasher;
    private $transactionModel;
    
    public function __construct(UserModel $userModel) {
        parent::__construct($userModel);
        $this->passwordHasher = new PasswordHash();
        $this->transactionModel = new TransactionModel();
    }
    
    // List all users
    public function listUsers() {
        return $this->model->findAll();
    }
    
    // Create new user/borrower
    public function createUser($data) {
        // Validate required fields
        if (empty($data['name']) || empty($data['email']) || 
            empty($data['phone_number']) || empty($data['password']) || 
            empty($data['role'])) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        // Check if email exists
        if ($this->model->emailExists($data['email'])) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        // Check if phone exists
        if ($this->model->phoneExists($data['phone_number'])) {
            return ['success' => false, 'message' => 'Phone number already exists'];
        }
        
        // Hash password
        $data['password'] = $this->passwordHasher->hash($data['password']);
        
        // Set default status if not provided
        if (!isset($data['account_status'])) {
            $data['account_status'] = 'active';
        }
        
        $userId = $this->model->create($data);
        
        if ($userId) {
            return ['success' => true, 'user_id' => $userId];
        }
        
        return ['success' => false, 'message' => 'Failed to create user'];
    }
    
    // Update user
    public function updateUser($id, $data) {
        $user = $this->model->find($id);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Check email uniqueness if changing
        if (isset($data['email']) && $data['email'] !== $user['email']) {
            if ($this->model->emailExists($data['email'], $id)) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
        }
        
        // Check phone uniqueness if changing
        if (isset($data['phone_number']) && $data['phone_number'] !== $user['phone_number']) {
            if ($this->model->phoneExists($data['phone_number'], $id)) {
                return ['success' => false, 'message' => 'Phone number already exists'];
            }
        }
        
        // Handle password update if provided
        if (!empty($data['password'])) {
            $data['password'] = $this->passwordHasher->hash($data['password']);
        } else {
            // Don't update password if not provided
            unset($data['password']);
        }
        
        $result = $this->model->update($id, $data);
        
        if ($result) {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to update user'];
    }
    
    // Deactivate user (safer than delete)
    public function deactivateUser($id) {
        $user = $this->model->find($id);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Check for active borrowings
        $activeTransactions = $this->transactionModel->findAll(
            'user_id = :user_id AND status = :status', 
            ['user_id' => $id, 'status' => 'borrowed']
        );
        
        if (count($activeTransactions) > 0) {
            return [
                'success' => false, 
                'message' => 'User has active borrowings and cannot be deactivated'
            ];
        }
        
        $result = $this->model->updateStatus($id, 'inactive');
        
        if ($result) {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to deactivate user'];
    }
    
    // Reactivate user
    public function activateUser($id) {
        $user = $this->model->find($id);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        $result = $this->model->updateStatus($id, 'active');
        
        if ($result) {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to activate user'];
    }
    
    // Get user with borrowed books
    public function getUserWithBooks($id) {
        $user = $this->model->find($id);
        
        if (!$user) {
            return null;
        }
        
        if (in_array($user['role'], ['students', 'staff', 'others'])) {
            $borrowerModel = new BorrowerModel();
            $user['borrowed_books'] = $borrowerModel->getBorrowedBooks($id);
        }
        
        return $user;
    }
    
    // Generate new random password
    public function resetPassword($id) {
        $user = $this->model->find($id);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Generate random password
        $newPassword = $this->generateRandomPassword();
        
        // Hash and update
        $hashedPassword = $this->passwordHasher->hash($newPassword);
        $result = $this->model->update($id, ['password' => $hashedPassword]);
        
        if ($result) {
            return [
                'success' => true, 
                'password' => $newPassword
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to reset password'];
    }
    
    // Generate random password
    private function generateRandomPassword($length = 10) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        
        return $password;
    }
}