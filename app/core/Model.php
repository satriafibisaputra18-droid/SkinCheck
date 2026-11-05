<?php
/**
 * SkinCheck - Base Model
 * All models inherit from this class.
 * Provides direct access to the Database layer.
 */

class Model {
    protected $db;

    public function __construct() {
        // Instantiate the Database connection wrapper
        $this->db = new Database();
    }
}
