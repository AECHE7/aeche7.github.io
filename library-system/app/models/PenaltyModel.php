<?php

// app/models/PenaltyModel.php
class PenaltyModel extends BaseModel {
    protected $table = 'penalties';
    protected $primaryKey = 'penalty_id';
    
    protected $fillable = [
        'user_id', 
        'transaction_id', 
        'penalty_amount', 
        'penalty_date'
    ];
    
    // Calculate penalty amount (₱50 base + daily increase)
    public function calculateAmount($dueDate) {
        $today = new DateTime();
        $due = new DateTime($dueDate);
        $daysLate = $today->diff($due)->days;
        
        // Base penalty is ₱50, increase daily
        return 50 + ($daysLate * 10); // Additional ₱10 per day
    }
    
    // Record penalty for overdue book
    public function recordPenalty($transactionId, $userId, $amount) {
        $data = [
            'user_id' => $userId,
            'transaction_id' => $transactionId,
            'penalty_amount' => $amount,
            'penalty_date' => date('Y-m-d H:i:s')
        ];
        
        return $this->create($data);
    }
    
    // Get user penalties
    public function getUserPenalties($userId) {
        $sql = "SELECT p.*, t.borrow_date, t.due_date, t.return_date,
                b.title as book_title 
                FROM {$this->table} p 
                JOIN transactions t ON p.transaction_id = t.transaction_id 
                JOIN books b ON t.book_id = b.book_id 
                WHERE p.user_id = :user_id";
                
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }
}