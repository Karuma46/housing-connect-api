<?php
  
  require_once 'db.php';
  require_once 'general.php';

  class Location extends General{
    
    public $table_name = '';
    public $db;
    
    public function __construct(){
      $this->db = new Database();
    }
    
    public function getCounties(){
      
      $req['cols'] = '*';
      $req['table_name'] = 'counties';
      $req['params'] = 'ORDER BY county ASC';
      $req['array'] = array();
      
      $list = $this->db->fetcher($req);
      
      if($list){
        
        $counties = array();
        
        foreach($list as $obj){
          $county['code'] = $obj['county_code'];
          $county['name'] = $obj['county'];
          array_push($counties,$county);
        }
        
        echo json_encode($counties);
      } else {
        echo $this->response('failed', 'nil', '');
      }  
      
    }
  } 