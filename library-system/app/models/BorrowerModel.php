<?php


// app/models/BorrowerModel.php
class BorrowerModel extends UserModel {
    // Get borrower's current borrowed books
    public function getBorrowedBooks($userId) {
        $sql = "SELECT b.*, t.borrow_date, t.due_date, t.return_date, t.status 
                FROM books b 
                JOIN transactions t ON b.book_id = t.book_id 
                WHERE t.user_id = :user_id 
                ORDER BY t.borrow_date DESC";
                
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }
}

