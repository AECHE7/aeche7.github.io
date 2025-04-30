<?php

class ReportService {
    private $userModel;
    private $bookModel;
    private $transactionModel;
    private $penaltyModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->bookModel = new BookModel();
        $this->transactionModel = new TransactionModel();
        $this->penaltyModel = new PenaltyModel();
    }
    
    // Get dashboard statistics
    public function getDashboardStats($role = 'admin') {
        $stats = [
            'total_books' => $this->bookModel->count(),
            'borrowed_books' => $this->transactionModel->count('status = :status', ['status' => 'borrowed']),
            'overdue_books' => count($this->transactionModel->getOverdue())
        ];
        
        if ($role === 'super-admin') {
            // Additional stats for super admin
            $stats['total_students'] = $this->userModel->count('role = :role', ['role' => 'students']);
            $stats['total_staff'] = $this->userModel->count('role = :role', ['role' => 'staff']);
            $stats['total_admins'] = $this->userModel->count('role = :role', ['role' => 'admin']);
            $stats['total_others'] = $this->userModel->count('role = :role', ['role' => 'others']);
        } else if ($role === 'admin') {
            // Stats for admin dashboard
            $stats['total_borrowers'] = $this->userModel->count(
                "role IN ('students', 'staff', 'others')"
            );
        }
        
        return $stats;
    }
    
    // Generate book report
    public function generateBookReport() {
        $bookService = new BookService(new BookModel(), new CategoryModel());
        return $bookService->getBooksWithCategories();
    }
    
    // Generate user report
    public function generateUserReport($role = null) {
        $conditions = '';
        $params = [];
        
        if ($role) {
            $conditions = 'role = :role';
            $params = ['role' => $role];
        }
        
        return $this->userModel->findAll($conditions, $params);
    }
    
    // Generate transaction report
    public function generateTransactionReport($status = null, $startDate = null, $endDate = null) {
        $transactionService = new TransactionService(new TransactionModel());
        
        if ($startDate && $endDate) {
            return $transactionService->getTransactionsByDateRange($startDate, $endDate);
        }
        
        return $transactionService->getTransactionsWithDetails($status);
    }
    
    // Generate penalty report
    public function generatePenaltyReport() {
        $penaltyService = new PenaltyService(new PenaltyModel());
        return $penaltyService->getAllPenaltiesWithDetails();
    }
}