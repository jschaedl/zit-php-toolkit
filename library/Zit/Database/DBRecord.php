<?php
class DBRecord
{
    private $db; // This is an instance of Class_DB to be injected in the functions.
    private $tableName = NULL;
    
    public function __construct($database, $tableName) {
        $this->db = $database;
        $this->tableName = $tableName;
    }

    public function connect() {
        return $this->db->createConnexion();
    }

    function checkRecordExists($recordIdentifier, $tableColName) {
        $connection = $this->connect();
        $query = $connection->prepare("SELECT COUNT(*) FROM" . $this->tableName . " WHERE " . $tableColName . " = :recordIdentifier");
        $query->bindParam(":recordIdentifier", $recordIdentifier);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result["COUNT(*)"] > 0 ? true : false;
    }
    
    public function getAllRecords($attributes = array(), $where, $order, $limit) {
        $connection = $this->connect();
        $query = $connection->prepare("SELECT " . implode(",", attributes) . " FROM " . $this->tableName);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    public function readRecord($id, $attributes = array()) {
        $connection = $this->connect();
        $query = $connection->prepare("SELECT " . implode(",", attributes) . " FROM " . $this->tableName . " WHERE " . $this->tableName . "id = " . $id);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    public function deleteRecord($id, $hardDelete = false) {
        $connection = $this->connect();
        if ($hardDelete) {
            $query = $connection->prepare("DELETE FROM " . $this->tableName . " WHERE " . $this->tableName . "id = " . $id);
        } else {
            $query = $connection->prepare("UPDATE " . $this->tableName . " SET deleted=1 WHERE " . $this->tableName . "id = " . $id);
        }
            
        $query->execute();
    }
    
    abstract public function createRecord();
    abstract public function updateRecord();
}