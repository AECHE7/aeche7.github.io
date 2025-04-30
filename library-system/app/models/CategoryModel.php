<?php

// app/models/CategoryModel.php
class CategoryModel extends BaseModel {
    protected $table = 'book_categories';
    protected $primaryKey = 'category_id';
    
    protected $fillable = ['category_name'];
    
    // Get all categories with book counts
    public function getAllWithBookCount() {
        $sql = "SELECT c.*, COUNT(b.book_id) as book_count 
                FROM {$this->table} c 
                LEFT JOIN books b ON c.category_id = b.category 
                GROUP BY c.category_id";
                
        return $this->db->fetchAll($sql);
    }
}

