<?php

class BaseModel {

    protected $conn;
    protected $table = "model"; // to be defined in subclasses

    public function __construct($db=null)
    {
        $this->conn = $db;
    }

}

?>