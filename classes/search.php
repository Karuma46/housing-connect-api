<?php
  
  require_once 'db.php';
  require_once 'general.php';
  
  
  class Search extends General{
    
    public $table_name = 'listings';
    
    public function __construct(){
      $this->db = new Database();
    }
    
    public function SearchController($q){
      
      if($q === 'apartments' || $q === 'houses' || $q === 'offices' || $q === 'land' || $q='hotels'){
        return $this->findCategories($q);
      } else {
        return $this->response('fail', 'no results found', $q);
      }
      
    }
    
    public function findCategories($q){
      
      $req['cols'] = '*';
      $req['table_name'] = $this->table_name;
      $req['params'] = "WHERE category =:q";
      $req['array'] = array(
        ":q" => $q
      );
      
      $search = $this->db->fetcher($req);
      
      if($search){
        
        $list = [];
        foreach ($search as $obj){
          
          $item['title'] = $obj['title'];
          $item['listing_id'] = $obj['listing_id'];
          $item['owner'] = $obj['user_id'];
          $item['category'] = $obj['category'];
          $item['location'] = $obj['location'];
          $item['amount'] = number_format($obj['amount']);
          $item['description'] = $obj['description'];
          $item['features'] = json_decode($obj['features']);
          $item['amenities'] = $obj['amenities'];
          $item['saved'] = 'false';
          array_push($list,$item);
        }
        return json_encode($list);
      
      } else {
        return $this->response('fail', 'no listings', '');
        
      }
    }
    
  }