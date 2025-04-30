<?php

// app/services/PenaltyService.php
class PenaltyService extends BaseService {
    public function __construct(PenaltyModel $penaltyModel) {
        parent::__construct($penaltyModel);
    }
    
    // Get user's penalties
    public function getUserPenalties($userId) {
        return $this->model->getUserPenalties($userId);
    }
    
    // Calculate penalty for transaction
    public function calculatePenaltyForTransaction($transactionId) {
        $transactionModel = new TransactionModel();
        $transaction = $transactionModel->find($transactionId);
        
        if (!$transaction) {
            return [
                'success' => false, 
                'message' => 'Transaction not found'
            ];
        }
        
        if ($transaction['status'] !== 'borrowed') {
            return [
                'success' => false, 
                'message' => 'Transaction is not in borrowed status'
            ];
        }
        
        $dueDate = new DateTime($transaction['due_date']);
        $today = new DateTime();
        
        // Check if book is overdue
        if ($today <= $dueDate) {
            return [
                'success' => true,
                'is_overdue' => false,
                'penalty_amount' => 0
            ];
        }
        
        // Calculate penalty amount
        $penaltyAmount = $this->model->calculateAmount($transaction['due_date']);
        
        return [
            'success' => true,
            'is_overdue' => true,
            'penalty_amount' => $penaltyAmount,
            'days_overdue' => $today->diff($dueDate)->days
        ];
    }
    
    // Get all penalties with details
    public function getAllPenaltiesWithDetails() {
        $sql = "SELECT p.*, t.borrow_date, t.due_date, t.return_date, 
                b.title as book_title, u.name as borrower_name 
                FROM penalties p 
                JOIN transactions t ON p.transaction_id = t.transaction_id 
                JOIN books b ON t.book_id = b.book_id 
                JOIN users u ON p.user_id = u.user_id 
                ORDER BY p.penalty_date DESC";
                
        return $this->model->db->fetchAll($sql);
    }
}

// app/services/ReportService.php
