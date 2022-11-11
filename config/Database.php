<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'font_group_system';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
      $this->disconnect();
      try { 
        $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        echo 'Connection Error: ' . $e->getMessage();
      }
      return $this->conn;
    }

    public function disconnect() {
      $this->conn = null;
    }
  }
