<?php

  require_once 'db.php';
  require_once 'general.php';

  class User extends General{

    PUBLIC $first_name;
    PUBLIC $last_name;
    PUBLIC $email;
    PUBLIC $token;
    PUBLIC $id;
    PUBLIC $user_id;
    PROTECTED $db;
    PUBLIC $table_name = 'users';

    public function __construct(){
      $this->db = new Database();
    }

    public function getUser($user_id){
      $req = [];
      $req['cols'] = '*';
      $req['table_name'] = $this->table_name;
      $req['params'] = 'WHERE user_id=:user_id';
      $req['array'] = array(
        ":user_id" => $user_id
      ); 

      $user = $this->db->selector($req);
      
      return $user;
    }

    public function me($user_id){
      $user = $this->getUser($user_id);

      if(!$user){
        return false;
      } else {
        foreach($user as $key => $value){
          $this->$key = $value;
        } 
      }
    }

    public function checkUserExists($email){

      $check['cols'] = '*';
      $check['table_name'] = $this->table_name;
      $check['params'] = 'WHERE email=:email';
      $check['array'] = array(
        ":email" => $email
      );

      $exists = $this->db->selector($check);

      if($exists === false){
        return false;
      } else {
        return true;
      }

    }
    
    public function login($obj){

      $req['cols'] = 'password,user_id';
      $req['table_name'] = $this->table_name;
      $req['params'] = 'WHERE email=:email';
      $req['array'] = array(
        ":email" => $obj->email
      );

      $user_details = $this->db->selector($req);

      if(!$user_details){
        echo $this->response('fail','Account not found. Login failed!','');
      } else {
        $pass = json_decode($user_details->password);
        
        $hash = $pass->encrypted;
        $salt = $pass->salt;

        if(password_verify($obj->password.$salt,$hash)){
          return $this->response('success','Login Successful',$user_details->user_id);
        } else {
          return $this->response('fail','Wrong username or password. Try again!','');
        }
      }
      
      
    }

    public function register($obj){
      
      //check if user exists using email
      $exists = $this->checkUserExists($obj->email);

      if($exists !== false){
        echo $this->response('fail','That email address is already in use','');
      } else {
        // get password hash
        $hash = $this->getHash($obj->password);
        $req['table_name'] = $this->table_name;
        $req['params'] = "first_name=:first_name,last_name=:last_name,email=:email,password=:hash,date_added=:date,date_modified=:date";
        $req['array'] = array(
          ":first_name"=>$obj->first_name,
          ":last_name"=>$obj->last_name,
          ":email"=>$obj->email,
          ":date"=>$this->getDate(),
          ":hash"=>$hash
        );
          
        $register = $this->db->insert($req);

        if($register){
          echo $this->response('success','Registration Successful. Check your email to activate your account', $register);
        } else {
          echo $this->response('fail','Reqistration Unsuccessful. Try again Later','');
        }

      } 
    }
    
    public function saveListing($obj){
      
      // check if saved list exists
      $check['cols'] = '*';
      $check['table_name'] = 'saved_listings';
      $check['params'] = 'WHERE user_id=:user_id';
      $check['array'] = array(
        ":user_id" => $obj->user_id
      );

      $exists = $this->db->selector($check);
      
      $list = array();
      array_push($list,$obj->listing_id);
      
      if($exists === false){
        //insert
        
        $req['table_name'] = 'saved_listings';
        $req['params'] = 'user_id=:user_id, saved_listings=:list, date_added=:date, date_modified=:date';
        $req['array'] = array(
          ":user_id" => $obj->user_id,
          ":list" => json_encode($list),
          ":date" => $this->getDate()
        );
        
        $save = $this->db->insert($req);
        
        if($save){
          return $this->response('success', 'Listing Saved', $save);
        } else {
          return $this->response('fail', 'Save Listing Unsuccessful', '');
        }
        
      } else {
        //update
        $list = json_decode($exists->saved_listings);
        
        if(in_array($obj->listing_id, $list)){
          $key = array_search($obj->listing_id,$list);
          array_splice($list,$key,1);
        } else {
          array_push($list,$obj->listing_id);
        }
        
        $req['table_name'] = 'saved_listings';
        $req['params'] = 'saved_listings=:list, date_modified=:date WHERE user_id=:user_id';
        $req['array'] = array(
          ":user_id" => $obj->user_id,
          ":list" => json_encode($list),
          ":date" => $this->getDate()
        );
        
        $update = $this->db->updator($req);
        
        if($update){
          return $this->response('success', 'Listing Saved', '');
        } else {
          return $this->response('fail', 'Save Listing Unsuccessful', '');
        }
      }
    }
    
    public function profile($id){
      
      
      $user = $this->getUser($id);
      
      $profile['first_name'] = $user->first_name;
      $profile['last_name'] = $user->last_name;
      $profile['email'] = $user->email;
      $profile['gender'] = $user->gender;
      
      return json_encode($profile);
    }
    
    public function editProfile($obj){
      
    }

    public function forgotPass($email){
      // check if user exists
      $exists = $this->checkUserExists($email);

      if($exists === false){
        $this->response('fail', 'Sorry, this account doesn\'t exist');
      } else {
        // generate random password
        $resetCode = sha1(rand());
        $resetCode = substr($resetCode, 0, 15);

        $obj['email'] = $email;
        $obj['subject'] = "Password Reset";
        $obj['body'] = "Your temporary code is <b>". $resetCode ."</b>. It's only active for the next ten minutes.";
        $obj['altBody'] = "Your temporary code is ". $resetCode .". It's only active for the next ten minutes.";

        $send = $this->mailer($obj);

        if($send){
          $req['table_name'] = 'pass_reset';
          $req['params'] = 'email=:email, code=:code, date_added=:date';
          $req['array'] = array(
            ":email" => $email,
            ":code" => $resetCode,
            ":date" => $this->getDate()
          );

          $insert = $this->db->insert($req);

          return $this->response('success', '', $insert);
        } else {
          return $this->response('fail', '', $send);
        }

      }
    }
  }