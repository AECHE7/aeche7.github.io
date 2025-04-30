<?php

// app/core/BaseModel.php
class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    
    // Indicates if the model should use timestamps
    protected $timestamps = false;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // CRUD Operations
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    public function findAll($conditions = '', $params = [], $orderBy = '') {
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($conditions)) {
            $sql .= " WHERE $conditions";
        }
        
        if (!empty($orderBy)) {
            $sql .= " ORDER BY $orderBy";
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function create($data) {
        // Filter only fillable fields
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        
        // Add timestamps if model uses them
        if (property_exists($this, 'timestamps') && $this->timestamps) {
            $filteredData['created_at'] = date('Y-m-d H:i:s');
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->insert($this->table, $filteredData);
    }
    
    public function update($id, $data) {
        // Filter only fillable fields
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        
        // Add updated timestamp if model uses them
        if (property_exists($this, 'timestamps') && $this->timestamps) {
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->update(
            $this->table, 
            $filteredData, 
            "{$this->primaryKey} = :id", 
            ['id' => $id]
        );
    }
    
    public function delete($id) {
        return $this->db->delete($this->table, "{$this->primaryKey} = :id", ['id' => $id]);
    }
    
    // Useful for soft deletes or status changes
    public function updateStatus($id, $status) {
        return $this->db->update(
            $this->table, 
            ['account_status' => $status], 
            "{$this->primaryKey} = :id", 
            ['id' => $id]
        );
    }
    
    // Count records
    public function count($conditions = '', $params = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($conditions)) {
            $sql .= " WHERE $conditions";
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }
}

