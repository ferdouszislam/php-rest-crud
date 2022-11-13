<?php

class BaseModel {

    protected $conn;
    protected $table = "model"; // to be defined in subclasses

    public function __construct($db=null)
    {
        $this->conn = $db;
    }

    public function delete($id) 
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) return true;
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    public function getLastInsertedId() 
    {
        if ($this->conn != null) return $this->conn->lastInsertId();
        else return null;
    }

}

?>