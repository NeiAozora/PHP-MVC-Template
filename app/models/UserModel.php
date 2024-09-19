<?php

class IndexModel extends Model{
    protected $table = 'users';
    protected $primaryKey = 'userId';

    protected $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getUserById(int $id): array
    {
        $this->db->query("SELECT * FROM $this->table WHERE $this->primaryKey = :value");
        $this->db->bind(':value', $id);
        return $this->db->single();
    }

}