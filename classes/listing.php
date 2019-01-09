<?php

  require_once 'db.php';
  require_once 'general.php';
  
  class Listing extends General{
    
    public $table_name = 'listings';
    public $db;
    
    public function __construct(){
      $this->db = new Database();
    }
    
    public function createListing($obj){
      
      //check user $exists
      $check['cols'] = '*';
      $check['table_name'] = 'users';
      $check['params'] = 'WHERE user_id=:user_id';
      $check['array'] = array(
        ":user_id" => $obj->user_id
      );

      $exists = $this->db->selector($check);
      
      if($exists === false){
        echo $this->response('fail','That user existn\'t','');
      } else {
        
        $req['table_name'] = $this->table_name;
        $req['params'] = '
          user_id = :user_id,
          category = :category,
          title = :title,
          location = :location,
          amount = :amount,
          mode = :mode,
          period = :period,
          description = :description,
          features = :features,
          amenities = :amenities,
          size = :size,
          units = :units,
          date_added = :date,
          date_modified = :date
        ';
        
        if($obj->category === 'Land'){
          $features = 'nil';
          $amenities = 'nil';
          $size = $obj->size;
          $units = $obj->units;
        } else {
          $features = json_encode($obj->features);
          $amenities = $obj->amenities;
          $size = 'nil';
          $units = 'nil';
        }
        
        
        $req['array'] = array(
          ":user_id" => $obj->user_id,
          ":category" => $obj->category,
          ":title" => $obj->title,
          ":location" => $obj->location,
          ":amount" => $obj->amount,
          ":mode" => $obj->mode,
          ":period" => $obj->period,
          ":description" => $obj->description,
          ":features" => $features,
          ":amenities" => $amenities,
          ":size" => $size,
          ":units" => $units,
          ":date" => $this->getDate()
        );
        
        $create = $this->db->insert($req);
        
        if($create){
          echo $this->response('success','Listing created!',$create);
        } else {
          echo $this->response('fail','Listing not created. Try again Later','');
        }
      }
    }
    
    public function editListing($obj){
      
      //check user $exists
      $check['cols'] = '*';
      $check['table_name'] = 'users';
      $check['params'] = 'WHERE user_id=:user_id';
      $check['array'] = array(
        ":user_id" => $obj->user_id
      );

      $exists = $this->db->selector($check);
      
      if($exists === false){
        echo $this->response('fail','That user existn\'t','');
      } else {
        
        $req['table_name'] = $this->table_name;
        $req['params'] = '
          category = :category,
          title = :title,
          location = :location,
          amount = :amount,
          mode = :mode,
          period = :period,
          description = :description,
          features = :features,
          amenities = :amenities,
          size = :size,
          units = :units,
          date_modified = :date
          WHERE listing_id =:listing_id && user_id=:user_id
        ';
        
        if($obj->category === 'Land'){
          $features = 'nil';
          $amenities = 'nil';
          $size = $obj->size;
          $units = $obj->units;
        } else {
          $features = json_encode($obj->features);
          $amenities = $obj->amenities;
          $size = 'nil';
          $units = 'nil';
        }
        
        
        $req['array'] = array(
          ":listing_id" => $obj->listing_id,
          ":user_id" => $obj->user_id,
          ":category" => $obj->category,
          ":title" => $obj->title,
          ":location" => $obj->location,
          ":amount" => $obj->amount,
          ":mode" => $obj->mode,
          ":period" => $obj->period,
          ":description" => $obj->description,
          ":features" => $features,
          ":amenities" => $amenities,
          ":size" => $size,
          ":units" => $units,
          ":date" => $this->getDate()
        );
        
        $create = $this->db->updator($req);
        
        if($create){
          echo $this->response('success','Listing edited!',$create);
        } else {
          echo $this->response('fail','Listing not edited. Try again Later','');
        }
      }
    }
    
    public function getCategories(){
      
      $req['cols'] = 'category';
      $req['table_name'] = $this->table_name;
      $req['params'] = '';
      $req['array'] = [];
      
      $cats = $this->db->fetcher($req);
      $group = array(
        "Houses" => 0,
        "Land" => 0,
        "Offices" => 0,
        "Apartments" => 0,
        "Hotels" => 0
      );
      if($cats){
        foreach($cats as $cat){
          
          $group[$cat['category']] +=1;
        } 
        
        return json_encode($group);
      } 
    }
    
    public function getFeatured(){
      $req['cols'] = '*';
      $req['table_name'] = $this->table_name;
      $req['params'] = 'LIMIT 12';
      $req['array'] = [];
      
      $listings = $this->db->fetcher($req);
      
      if($listings){
        
        $list = [];
        foreach ($listings as $obj){
          $item['title'] = $obj['title'];
          $item['listing_id'] = $obj['listing_id'];
          $item['owner'] = $obj['user_id'];
          $item['category'] = $obj['category'];
          $item['location'] = $obj['location'];
          $item['amount'] = $this->moneyFormatter($obj['amount']);
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
    
    public function getFeaturedwithSaved($id){
      $req['cols'] = '*';
      $req['table_name'] = $this->table_name;
      $req['params'] = 'LIMIT 12';
      $req['array'] = array();
      
      $listings = $this->db->fetcher($req);
      
      if($listings){
        
        $list = [];
          
        foreach ($listings as $obj){
          $item['title'] = $obj['title'];
          $item['listing_id'] = $obj['listing_id'];
          $item['owner'] = $obj['user_id'];
          $item['category'] = $obj['category'];
          $item['location'] = $obj['location'];
          $item['amount'] = $this->moneyFormatter($obj['amount']);
          $item['description'] = $obj['description'];
          $item['features'] = json_decode($obj['features']);
          $item['amenities'] = $obj['amenities'];
          $item['saved'] = $this->checkSaved($obj['listing_id'], $id);
                  
          array_push($list,$item);
        }
      
        return json_encode($list);
      } else {
        return $this->response('fail', 'no listings', '');
        
      }
    }
    
    public function checkSaved($listing_id, $user_id){
      
      $req['cols'] = 'saved_listings';
      $req['table_name'] = 'saved_listings';
      $req['params'] = 'WHERE user_id=:user_id';
      $req['array'] = array(
        ":user_id" => $user_id
      );
      
      $listings = $this->db->selector($req);
      $arr = json_decode($listings->saved_listings);
      
      if(in_array($listing_id, $arr)){
        return true;
      } else {
        return false;
      }
    }
    
    public function getById($id){
      
      $req['cols'] = '*';
      $req['table_name'] = $this->table_name;
      $req['params'] = 'LEFT JOIN users on users.user_id = '.$this->table_name.'.user_id WHERE listing_id=:id';
      $req['array'] = array(
        ":id" => $id
      );
      
      $result = $this->db->selector($req);
      
      if(!$result){
        return $this->response('fail', 'Listing not Found!', '');
      } else {
        
        $location = explode(',',$result->location);
        $features = json_decode($result->features);
        
        $owner_details['first_name'] = $result->first_name;
        $owner_details['last_name'] = $result->last_name;
        $owner_details['email'] = $result->email;
        
        $listing['id'] = $result->listing_id;
        $listing['title'] = ucwords($result->title);
        $listing['category'] = $result->category;
        $listing['price'] = number_format($result->amount);
        $listing['amount'] = $result->amount;
        $listing['owner_id'] = $result->user_id;
        $listing['owner'] = ucwords($result->first_name.' '.$result->last_name);
        $listing['owner_details'] = $owner_details; 
        $listing['description'] = $result->description;
        $listing['payPeriod'] = $result->period;
        $listing['payMode'] = $result->mode;
        $listing['location'] = $result->location;
        $listing['amenities'] = explode(',',$result->amenities);
        $listing['town'] = $location[0];
        $listing['county'] = $location[1];
        
        
        if(!isset($features->parking)){
          $listing['parking'] = '0';
        } else {
          $listing['parking'] = $features->parking;
        }
        
        if(!isset($features->bedrooms)){
          $listing['beds'] = '0';
        } else {
          $listing['beds'] = $features->bedrooms;
        }
        
        if(!isset($features->bathrooms)){
          $listing['baths'] = '0';
        } else {
          $listing['baths'] = $features->bathrooms;
        }
        
        $listing['listingId'] = $result->listing_id;
        
        return $this->response('success', '', $listing);
      }
      
    }
    
    public function userListings($id){
      
      $req['cols'] = '*';
      $req['table_name'] = $this->table_name;
      $req['params'] = 'WHERE user_id=:user_id';
      $req['array'] = array(
        ":user_id" => $id
      );
      
      $listings = $this->db->fetcher($req);
      
      if($listings){
        
        $list = [];
        foreach ($listings as $obj){
          $item['title'] = $obj['title'];
          $item['listing_id'] = $obj['listing_id'];
          $item['owner'] = $obj['user_id'];
          $item['category'] = $obj['category'];
          $item['location'] = $obj['location'];
          $item['amount'] = $this->moneyFormatter($obj['amount']);
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