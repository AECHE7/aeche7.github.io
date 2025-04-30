<?php

// app/utilities/PDFGenerator.php
class PDFGenerator {
    private $fpdf;
    
    public function __construct() {
        // Require FPDF library
        require_once __DIR__ . 'fpdf.php';
        $this->fpdf = new FPDF();
    }
    
    // Generate book list PDF
    public function generateBookList($books) {
        $this->fpdf->AddPage();
        $this->fpdf->SetFont('Arial', 'B', 16);
        $this->fpdf->Cell(0, 10, 'Library Book List', 0, 1, 'C');
        $this->fpdf->Ln(10);
        
        // Table header
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(10, 10, 'ID', 1);
        $this->fpdf->Cell(60, 10, 'Title', 1);
        $this->fpdf->Cell(40, 10, 'Author', 1);
        $this->fpdf->Cell(30, 10, 'Category', 1);
        $this->fpdf->Cell(20, 10, 'Year', 1);
        $this->fpdf->Cell(30, 10, 'Available', 1);
        $this->fpdf->Ln();
        
        // Table data
        $this->fpdf->SetFont('Arial', '', 11);
        foreach ($books as $book) {
            $this->fpdf->Cell(10, 10, $book['book_id'], 1);
            $this->fpdf->Cell(60, 10, $book['title'], 1);
            $this->fpdf->Cell(40, 10, $book['author'], 1);
            $this->fpdf->Cell(30, 10, $book['category_name'], 1);
            $this->fpdf->Cell(20, 10, $book['published_year'], 1);
            $this->fpdf->Cell(30, 10, $book['available_copies'] . '/' . $book['total_copies'], 1);
            $this->fpdf->Ln();
        }
        
        // Output the PDF
        $this->fpdf->Output('books_list.pdf', 'D');
    }
    
    // Generate borrower list PDF
    public function generateBorrowerList($borrowers) {
        $this->fpdf->AddPage();
        $this->fpdf->SetFont('Arial', 'B', 16);
        $this->fpdf->Cell(0, 10, 'Library Borrowers List', 0, 1, 'C');
        $this->fpdf->Ln(10);
        
        // Table header
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(10, 10, 'ID', 1);
        $this->fpdf->Cell(60, 10, 'Name', 1);
        $this->fpdf->Cell(60, 10, 'Email', 1);
        $this->fpdf->Cell(30, 10, 'Role', 1);
        $this->fpdf->Cell(30, 10, 'Status', 1);
        $this->fpdf->Ln();
        
        // Table data
        $this->fpdf->SetFont('Arial', '', 11);
        foreach ($borrowers as $borrower) {
            $this->fpdf->Cell(10, 10, $borrower['user_id'], 1);
            $this->fpdf->Cell(60, 10, $borrower['name'], 1);
            $this->fpdf->Cell(60, 10, $borrower['email'], 1);
            $this->fpdf->Cell(30, 10, $borrower['role'], 1);
            $this->fpdf->Cell(30, 10, $borrower['account_status'], 1);
            $this->fpdf->Ln();
        }
        
        // Output the PDF
        $this->fpdf->Output('borrowers_list.pdf', 'D');
    }
    
    // Generate transaction report PDF
    public function generateTransactionReport($transactions) {
        $this->fpdf->AddPage('L'); // Landscape
        $this->fpdf->SetFont('Arial', 'B', 16);
        $this->fpdf->Cell(0, 10, 'Library Transactions Report', 0, 1, 'C');
        $this->fpdf->Ln(10);
        
        // Table header
        $this->fpdf->SetFont('Arial', 'B', 10);
        $this->fpdf->Cell(15, 10, 'ID', 1);
        $this->fpdf->Cell(50, 10, 'Book Title', 1);
        $this->fpdf->Cell(40, 10, 'Borrower', 1);
        $this->fpdf->Cell(30, 10, 'Borrow Date', 1);
        $this->fpdf->Cell(30, 10, 'Due Date', 1);
        $this->fpdf->Cell(30, 10, 'Return Date', 1);
        $this->fpdf->Cell(25, 10, 'Status', 1);
        $this->fpdf->Cell(30, 10, 'Penalty', 1);
        $this->fpdf->Ln();
        
        // Table data
        $this->fpdf->SetFont('Arial', '', 9);
        foreach ($transactions as $transaction) {
            $this->fpdf->Cell(15, 10, $transaction['transaction_id'], 1);
            $this->fpdf->Cell(50, 10, $transaction['book_title'], 1);
            $this->fpdf->Cell(40, 10, $transaction['borrower_name'], 1);
            $this->fpdf->Cell(30, 10, $transaction['borrow_date'], 1);
            $this->fpdf->Cell(30, 10, $transaction['due_date'], 1);
            $this->fpdf->Cell(30, 10, $transaction['return_date'] ?? 'Not Returned', 1);
            $this->fpdf->Cell(25, 10, $transaction['status'], 1);
            
            // Get penalty if any
            $penaltyModel = new PenaltyModel();
            $penalties = $penaltyModel->findAll(
                'transaction_id = :tid', 
                ['tid' => $transaction['transaction_id']]
            );
            
            $penaltyAmount = 'N/A';
            if (!empty($penalties)) {
                $penaltyAmount = '₱' . number_format($penalties[0]['penalty_amount'], 2);
            }
            
            $this->fpdf->Cell(30, 10, $penaltyAmount, 1);
            $this->fpdf->Ln();
        }
        
        // Output the PDF
        $this->fpdf->Output('transactions_report.pdf', 'D');
    }
    
    // Generate admin profile PDF
    public function generateAdminProfile($admin) {
        $this->fpdf->AddPage();
        $this->fpdf->SetFont('Arial', 'B', 16);
        $this->fpdf->Cell(0, 10, 'Admin Profile', 0, 1, 'C');
        $this->fpdf->Ln(10);
        
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(40, 10, 'Admin ID:', 0);
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, $admin['user_id'], 0, 1);
        
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(40, 10, 'Name:', 0);
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, $admin['name'], 0, 1);
        
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(40, 10, 'Email:', 0);
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, $admin['email'], 0, 1);
        
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(40, 10, 'Phone:', 0);
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, $admin['phone_number'], 0, 1);
        
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(40, 10, 'Status:', 0);
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, $admin['account_status'], 0, 1);
        
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(40, 10, 'Created At:', 0);
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, $admin['created_at'], 0, 1);
        
        // Output the PDF
        $this->fpdf->Output('admin_profile.pdf', 'D');
    }
    
    // Generate book borrowers PDF
    public function generateBookBorrowers($book, $borrowers) {
        $this->fpdf->AddPage();
        $this->fpdf->SetFont('Arial', 'B', 16);
        $this->fpdf->Cell(0, 10, 'Book Borrowers Report', 0, 1, 'C');
        $this->fpdf->Ln(5);
        
        // Book details
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(30, 10, 'Book ID:', 0);
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, $book['book_id'], 0, 1);
        
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(30, 10, 'Title:', 0);
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, $book['title'], 0, 1);
        
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(30, 10, 'Title:', 0);
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, $book['title'], 0, 1);

        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(30, 10, 'Author:', 0);
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, $book['author'], 0, 1);

        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(30, 10, 'Category:', 0);
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, $book['category_name'], 0, 1);

        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(30, 10, 'Year:', 0);
        $this->fpdf->SetFont('Arial', '', 12);
        $this->fpdf->Cell(0, 10, $book['published_year'], 0, 1);

        $this->fpdf->Ln(10);

        // Borrowers table
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->Cell(10, 10, 'ID', 1);
        $this->fpdf->Cell(60, 10, 'Name', 1);
        $this->fpdf->Cell(60, 10, 'Email', 1);
        $this->fpdf->Cell(30, 10, 'Borrow Date', 1);
        $this->fpdf->Cell(30, 10, 'Return Date', 1);
        $this->fpdf->Cell(30, 10, 'Status', 1);
        $this->fpdf->Ln();

        $this->fpdf->SetFont('Arial', '', 11);
        foreach ($borrowers as $borrower) {
            $this->fpdf->Cell(10, 10, $borrower['user_id'], 1);
            $this->fpdf->Cell(60, 10, $borrower['name'], 1);
            $this->fpdf->Cell(60, 10, $borrower['email'], 1);
            $this->fpdf->Cell(30, 10, $borrower['borrow_date'], 1);
            $this->fpdf->Cell(30, 10, $borrower['return_date'] ?? 'Not Returned', 1);
            $this->fpdf->Cell(30, 10, $borrower['status'], 1);
            $this->fpdf->Ln();
        }

        // Output the PDF
        $this->fpdf->Output('book_borrowers_report.pdf', 'D');
    }
}
