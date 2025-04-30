<?php

require_once __DIR__ . '/../core/BaseService.php';

// app/services/BookService.php
class BookService extends BaseService {
    private $bookModel;
    private $categoryModel;

    public function __construct(BookModel $bookModel, CategoryModel $categoryModel) {
        parent::__construct($bookModel);
        $this->bookModel = $bookModel;
        $this->categoryModel = $categoryModel;
    }

    public function getBook($id) {
        return $this->bookModel->findById($id);
    }

    
    // Add new book
    public function addBook($data) {
        // Validate data
        if (empty($data['title']) || empty($data['author']) || 
            empty($data['category']) || empty($data['published_year']) || 
            empty($data['total_copies'])) {
            return false;
        }
        
        // Initially, available copies equals total copies
        $data['available_copies'] = $data['total_copies'];
        
        return $this->model->create($data);
    }
    
    // Update book
    public function updateBook($id, $data) {
        $book = $this->model->find($id);
        if (!$book) {
            return false;
        }
        
        // Calculate difference in total copies to adjust available copies
        if (isset($data['total_copies']) && $data['total_copies'] != $book['total_copies']) {
            $difference = $data['total_copies'] - $book['total_copies'];
            $data['available_copies'] = $book['available_copies'] + $difference;
        }
        
        return $this->model->update($id, $data);
    }
    
    // Delete book (check for active borrowings first)
    public function deleteBook($id) {
        // Check if book has active borrowings
        $transactionModel = new TransactionModel();
        $activeTransactions = $transactionModel->findAll(
            'book_id = :book_id AND status = :status', 
            ['book_id' => $id, 'status' => 'borrowed']
        );
        
        if (count($activeTransactions) > 0) {
            return false; // Cannot delete - active borrowings exist
        }
        
        return $this->model->delete($id);
    }
    
    // Get books with category names
    public function getBooksWithCategories() {
        $books = $this->model->findAll();
        $categories = [];
        
        // Get all categories and index by ID
        $allCategories = $this->categoryModel->findAll();
        foreach ($allCategories as $category) {
            $categories[$category['category_id']] = $category['category_name'];
        }
        
        // Add category name to each book
        foreach ($books as &$book) {
            $book['category_name'] = $categories[$book['category']] ?? 'Unknown';
        }
        
        return $books;
    }
    
    // Get book details with borrowers
    public function getBookWithBorrowers($id) {
        $book = $this->model->find($id);
        
        if ($book) {
            $book['borrowers'] = $this->model->getBorrowers($id);
            $book['category_name'] = $this->categoryModel->find($book['category'])['category_name'] ?? 'Unknown';
        }
        
        return $book;
    }
    
    // Search for books
    public function searchBooks($query) {
        return $this->model->search($query);
    }
    
    // Get all categories for select dropdown
    public function getAllCategories() {
        return $this->categoryModel->findAll();
    }
}

