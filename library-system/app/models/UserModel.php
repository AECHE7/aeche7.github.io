<?php
// app/models/UserModel.php
require_once __DIR__ . '/../core/BaseModel.php';

class UserModel extends BaseModel {
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $timestamps = true;
    
    protected $fillable = [
        'name', 
        'email', 
        'phone_number', 
        'password', 
        'role', 
        'account_status'
    ];
    
    // Get users by role
    public function findByRole($role) {
        return $this->findAll('role = :role', ['role' => $role]);
    }
    
    // Check if email exists
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $params = ['email' => $email];
        
        if ($excludeId !== null) {
            $sql .= " AND {$this->primaryKey} != :id";
            $params['id'] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }
    
    // Check if phone number exists
    public function phoneExists($phone, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE phone_number = :phone";
        $params = ['phone' => $phone];
        
        if ($excludeId !== null) {
            $sql .= " AND {$this->primaryKey} != :id";
            $params['id'] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }
    
    // Authenticate user
    public function authenticate($email, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email AND account_status = 'active' LIMIT 1";
        $user = $this->db->fetch($sql, ['email' => $email]);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
}

