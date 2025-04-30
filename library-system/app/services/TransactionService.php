<?php
require_once __DIR__ . '/../core/BaseService.php';

// app/services/TransactionService.php
class TransactionService extends BaseService {
    private $bookModel;
    private $userModel;
    private $penaltyModel;
    
    public function __construct(TransactionModel $transactionModel) {
        parent::__construct($transactionModel);
        $this->bookModel = new BookModel();
        $this->userModel = new UserModel();
        $this->penaltyModel = new PenaltyModel();
    }
    
    // Borrow a book
    public function borrowBook($userId, $bookId, $dueDate) {
        // Check if user exists and is active
        $user = $this->userModel->find($userId);
        if (!$user || $user['account_status'] !== 'active') {
            return [
                'success' => false, 
                'message' => 'User is not active or does not exist'
            ];
        }
        
        // Check if book exists and is available
        $book = $this->bookModel->find($bookId);
        if (!$book) {
            return [
                'success' => false, 
                'message' => 'Book does not exist'
            ];
        }
        
        if ($book['available_copies'] <= 0) {
            return [
                'success' => false, 
                'message' => 'No copies available for borrowing'
            ];
        }
        
        // Start transaction
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $conn->beginTransaction();
        
        try {
            // Create transaction record
            $transactionId = $this->model->borrowBook($userId, $bookId, $dueDate);
            
            if (!$transactionId) {
                throw new Exception('Failed to create transaction record');
            }
            
            // Update book availability
            $result = $this->bookModel->updateAvailability($bookId, -1);
            
            if (!$result) {
                throw new Exception('Failed to update book availability');
            }
            
            $conn->commit();
            
            return [
                'success' => true, 
                'transaction_id' => $transactionId
            ];
        } catch (Exception $e) {
            $conn->rollBack();
            return [
                'success' => false, 
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Return a book
    public function returnBook($transactionId) {
        // Get transaction details
        $transaction = $this->model->find($transactionId);
        
        if (!$transaction) {
            return [
                'success' => false, 
                'message' => 'Transaction not found'
            ];
        }
        
        if ($transaction['status'] !== 'borrowed') {
            return [
                'success' => false, 
                'message' => 'Book has already been returned'
            ];
        }
        
        // Start transaction
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $conn->beginTransaction();
        
        try {
            // Update transaction status
            $result = $this->model->returnBook($transactionId);
            
            if (!$result) {
                throw new Exception('Failed to update transaction record');
            }
            
            // Update book availability
            $result = $this->bookModel->updateAvailability($transaction['book_id'], 1);
            
            if (!$result) {
                throw new Exception('Failed to update book availability');
            }
            
            // Check for overdue and create penalty if needed
            $dueDate = new DateTime($transaction['due_date']);
            $today = new DateTime();
            
            if ($today > $dueDate) {
                // Calculate penalty
                $penaltyAmount = $this->penaltyModel->calculateAmount($transaction['due_date']);
                
                // Record penalty
                $recorded = $this->penaltyModel->recordPenalty(
                    $transactionId, 
                    $transaction['user_id'], 
                    $penaltyAmount
                );
                
                if (!$recorded) {
                    throw new Exception('Failed to record penalty');
                }
            }
            
            $conn->commit();
            
            return [
                'success' => true,
                'overdue' => ($today > $dueDate),
                'penalty_amount' => ($today > $dueDate) ? $penaltyAmount : 0
            ];
        } catch (Exception $e) {
            $conn->rollBack();
            return [
                'success' => false, 
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Get overdue transactions
    public function getOverdueTransactions() {
        return $this->model->getOverdue();
    }
    
    // Get user's active transactions
    public function getUserActiveTransactions($userId) {
        return $this->model->findAll(
            'user_id = :user_id AND status = :status', 
            ['user_id' => $userId, 'status' => 'borrowed']
        );
    }
    
    // Get transactions with full details
    public function getTransactionsWithDetails($status = null) {
        $conditions = '';
        $params = [];
        
        if ($status) {
            $conditions = 't.status = :status';
            $params = ['status' => $status];
        }
        
        return $this->model->getDetailedTransactions($conditions, $params);
    }
    
    // Get transactions by date range
    public function getTransactionsByDateRange($startDate, $endDate) {
        $conditions = 't.borrow_date BETWEEN :start_date AND :end_date';
        $params = [
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        return $this->model->getDetailedTransactions($conditions, $params);
    }
}

