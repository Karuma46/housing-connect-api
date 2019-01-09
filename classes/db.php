<?php

  class Database{
    // Database Credentials

    private $host = 'localhost';
    private $dbname = 'housing';//housingc_db
    private $dbuser = 'root';//housingc_admin
    private $dbpass = '';//vsO0[ayICFR
    public $conn;

    public function __construct(){
        $this->conn = new PDO('mysql:host='.$this->host.';dbname='.$this->dbname,$this->dbuser,$this->dbpass);
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

  }
?>