<?php

// app/core/BaseService.php
class BaseService {
    protected $model;
    
    public function __construct($model) {
        $this->model = $model;
    }
    
    // Basic service operations that use the model
    public function getById($id) {
        return $this->model->find($id);
    }
    
    public function getAll($conditions = '', $params = [], $orderBy = '') {
        return $this->model->findAll($conditions, $params, $orderBy);
    }
    
    public function create($data) {
        return $this->model->create($data);
    }
    
    public function update($id, $data) {
        return $this->model->update($id, $data);
    }
    
    public function delete($id) {
        return $this->model->delete($id);
    }
    
    public function count($conditions = '', $params = []) {
        return $this->model->count($conditions, $params);
    }
}