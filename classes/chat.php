<?php

  require_once 'db.php';
  require_once 'general.php';
  
  class Chat extends General{
    
    public $table_name = 'messages';
    public $db;
    
    public function __construct(){
      $this->db = new Database();
    }
    
    public function mkThread($listing_id){
      
      $req['table_name'] = 'threads';
      $req['params'] = 'listing_id=:listing_id';
      $req['array'] = array(
        ":listing_id" => $listing_id
      );
      
      $result = $this->db->insert($req);
      
      if($result){
        return $this->response('success','thread made',$result);
      } else {
        return $this->response('fail','try again','');
      }
    }
    
    public function send($obj){
      
      $req['table_name'] = $this->table_name;
      $req['params'] = 'thread_id=:thread_id,sender_id=:sender_id,receiver_id=:receiver_id,message_text=:text,date_added=:date';
      $req['array'] = array(
        ":thread_id" => $obj->thread_id,
        ":sender_id" => $obj->sender_id,
        ":receiver_id" => $obj->receiver_id,
        ":text" => $obj->message_text,
        ":date" => $this->getDate()
      );
      
      $result = $this->db->insert($req);
      
      if($result){
        return $this->response('success','message sent',$result);
      } else {
        return $this->response('fail','try again','');
      }
      
    }
    
    public function getThreads(){
      
      $req['cols'] = '*';
      $req['table_name'] = $this->table_name;
      $req['params'] = 'LEFT JOIN threads on '.$this->table_name.'.thread_id = threads.thread_id';
      $req['array'] = array();
      
      $result = $this->db->fetcher($req);
      
      if($result){
        return json_encode($result);
      } else {
        return $this->response('fail','try again','');
      }
    }
  
  }