<?php 

require_once('classLoader.php');

class Poster{

  public $user;
  public $listing;
  public $chat;
  
  
  public function __construct(){
    $this->user = new User();
    $this->listing = new Listing();
    $this->chat = new Chat();
  }

  public function post($data){
    
    $operation = $data->operation;

    switch ($operation) {
      
      // incase of Register
      case 'register': 
        echo $this->user->register($data->user);
        break;
      
      // incase of Login  
      case 'login':
        echo $this->user->login($data->user);  
        break;
      
      case 'forgot_pass':
        echo $this->user->forgotPass($data->email);
        break;

      case 'create_listing':
        echo $this->listing->createListing($data->listing);  
        break;
        
      case 'edit_listing':
        echo $this->listing->editListing($data->listing);  
        break;  
      
      case 'save_listing':
        echo $this->user->saveListing($data->listing);  
        break;
      
      case 'checkSaved':
        echo $this->listing->checkSaved($data->listing->listing_id, $data->listing->user_id);
        break;
        
      case 'makeThread':
        echo $this->chat->mkThread($data->listing_id);
        break;    
      
      case 'sendMessage':
        echo $this->chat->send($data->message);
      default:
        # code...
        break;
    }
  }
}

