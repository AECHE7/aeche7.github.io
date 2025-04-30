<?php

require_once __DIR__ . '/../core/BaseModel.php';

// app/models/TransactionModel.php
class TransactionModel extends BaseModel {
    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';
    protected $timestamps = true;
    
    protected $fillable = [
        'user_id', 
        'book_id', 
        'borrow_date', 
        'due_date', 
        'return_date', 
        'status'
    ];
    
    // Get transactions with book and user details
    public function getDetailedTransactions($conditions = '', $params = []) {
        $sql = "SELECT t.*, b.title as book_title, b.author, 
                u.name as borrower_name, u.email as borrower_email 
                FROM {$this->table} t 
                JOIN books b ON t.book_id = b.book_id 
                JOIN users u ON t.user_id = u.user_id";
                
        if (!empty($conditions)) {
            $sql .= " WHERE $conditions";
        }
        
        $sql .= " ORDER BY t.borrow_date DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Get overdue transactions
    public function getOverdue() {
        $today = date('Y-m-d');
        $sql = "SELECT t.*, b.title as book_title, u.name as borrower_name 
                FROM {$this->table} t 
                JOIN books b ON t.book_id = b.book_id 
                JOIN users u ON t.user_id = u.user_id 
                WHERE t.due_date < :today AND t.status = 'borrowed'";
                
        return $this->db->fetchAll($sql, ['today' => $today]);
    }
    
    // Record book borrow
    public function borrowBook($userId, $bookId, $dueDate) {
        $borrowDate = date('Y-m-d');
        
        $data = [
            'user_id' => $userId,
            'book_id' => $bookId,
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate,
            'status' => 'borrowed'
        ];
        
        return $this->create($data);
    }
    
    // Record book return
    public function returnBook($transactionId) {
        $returnDate = date('Y-m-d');
        $data = [
            'return_date' => $returnDate,
            'status' => 'returned'
        ];
        
        return $this->update($transactionId, $data);
    }
}
