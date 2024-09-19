<?php


abstract class Model
{
    protected $table = '';
    protected $primaryKey = '';

    protected $db;

    public function __construct()
    {
        $this->db = new Database;
    }


    public function getAll()
    {
        $this->db->query("SELECT * FROM $this->table");
        return $this->db->resultSet();
    }

    public function get($condition)
    {
        if (is_array($condition) && count($condition) === 2) {
            // Case for key-value pair: ($column, $value)
            $column = $condition[0];
            $value = $condition[1];
            $this->db->query("SELECT * FROM $this->table WHERE $column = :value");
            $this->db->bind(':value', $value, PDO::PARAM_INT);
            return $this->db->resultSet(); // Returns an array of results

        } else {
            // Case for single value (primary key)
            $this->db->query("SELECT * FROM $this->table WHERE $this->primaryKey = :value");
            $this->db->bind(':value', $condition);
            return $this->db->single();

        }

    }
}
