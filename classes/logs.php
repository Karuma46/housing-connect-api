<?php

  require_once 'db.php';
  require_once 'general.php';
  
  class Logger extends General{
    
    public $table_name = 'logs';
    public $db;
    
    public function __construct(){
      $this->db = new Database();
    }
    
    public function mkLog($obj){
      
      $req['table_name'] = $this->table_name;
      $req['params'] = 'content=:content, date_added=:date, source=:source';
      $req['array'] = array(
        ":content" => $obj['content'],
        ":date" => $this->getDate(),
        ":source" => $obj['source']
      );
      
      $result = $this->db->insert($req);
      
      if($result){
        //return $result;
      } else {
        return false;
      }
      
    }
    
    
  }
