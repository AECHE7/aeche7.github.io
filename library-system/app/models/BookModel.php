<?php

require_once __DIR__ . '/../core/BaseModel.php';

// app/models/BookModel.php
class BookModel extends BaseModel {
    protected $table = 'books';
    protected $primaryKey = 'book_id';
    protected $timestamps = true;
    
    protected $fillable = [
        'title', 
        'author', 
        'category', 
        'published_year', 
        'total_copies', 
        'available_copies'
    ];
    
    public function findById($id) {
        return $this->find($id); // Assuming 'find' is a method in BaseModel
    }
    // Get books by category
    public function getByCategory($categoryId) {
        return $this->findAll('category = :category', ['category' => $categoryId]);
    }
    
    // Search books by title or author
    public function search($query) {
        $searchParam = "%$query%";
        $sql = "SELECT * FROM {$this->table} WHERE title LIKE :query OR author LIKE :query";
        return $this->db->fetchAll($sql, ['query' => $searchParam]);
    }
    
    // Update book availability when borrowed/returned
    public function updateAvailability($bookId, $change) {
        $sql = "UPDATE {$this->table} SET available_copies = available_copies + :change 
                WHERE {$this->primaryKey} = :id";
        return $this->db->query($sql, ['change' => $change, 'id' => $bookId]);
    }
    
    // Get book borrowers history
    public function getBorrowers($bookId) {
        $sql = "SELECT u.name, t.borrow_date, t.due_date, t.return_date, t.status 
                FROM transactions t 
                JOIN users u ON t.user_id = u.user_id 
                WHERE t.book_id = :book_id 
                ORDER BY t.borrow_date DESC";
                
        return $this->db->fetchAll($sql, ['book_id' => $bookId]);
    }
}


