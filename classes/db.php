<?php
require_once('dbc.php');

  class Database {
    public $db;
    public $dbc;
    public $conn;

    public function __construct(){
        $this->dbc = new dbc();
        $this->db = $this->dbc->localdb();
        $this->conn = new PDO('mysql:host='.$this->db['host'].';dbname='.$this->db['name'],$this->db['user'],$this->db['pass']);
    }

    // DATABASE OPERATIONS
    public function selector($req){
      
      $sql = 'SELECT '.$req['cols'].' FROM '.$req['table_name'].' '.$req['params'];
      $query = $this->conn->prepare($sql);
      $query->execute($req['array']);
      
      $data = $query->fetch(PDO::FETCH_OBJ);

      if($data === null){
        return false;
      } else {
        return $data;
     }
     
    }
    
    public function fetcher($req){
      $sql = 'SELECT '.$req['cols'].' FROM '.$req['table_name'].' '.$req['params'];
      $query = $this->conn->prepare($sql);
      $query->execute($req['array']);
      
      $data = $query->fetchAll();

      if($data == null){
        return false;
      } else {
        return $data;
     }
    }

    public function insert($req){
      
      $sql = 'INSERT INTO '.$req['table_name'].' SET '.$req['params'];
      $query = $this->conn->prepare($sql);
      $query -> execute($req['array']);

      $last_id = $this->conn->lastInsertId();

      if($query == false){
        return false;
      } else {
        return $last_id;
      }
      
    }

    public function updator($req){
      $sql = 'UPDATE '. $req['table_name'] .' SET '.$req['params'];
      $query = $this->conn->prepare($sql);
      $query -> execute($req['array']);

      if($query == false){
        return false;
      } else {
        return true;
      }
    }
    
    public function finder($req){
      $sql = 'SELECT '. $req['cols'] .' FROM '. $req['table_name'] .''.$req['params'];
      $query = $this->conn->prepare($sql);
      $query -> execute($req['array']);
      
      $data = $query->fetchAll();
      
      if($data === null){
        return false;
      } else {
        return $data;
      }
    }

    public function thanos($req){
      $sql = 'DELETE FROM '. $req['table_name'].' '.$req['params'];
      $query = $this->conn->prepare($sql);
      $query -> execute($req['array']);

      if($query){
        return true;
      } else {
        return false;
      }
    }
  

  }
?>