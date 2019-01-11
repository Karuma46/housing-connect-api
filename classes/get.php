<?php 

require_once('classLoader.php');

class Getter{

  public $user;
  public $listing;
  public $chat;
  public $search;
  public $location;
  
  
  public function __construct(){
    $this->user = new User();
    $this->listing = new Listing();
    $this->chat = new Chat();
    $this->search = new Search();
    $this->location = new Location();
  }

  public function get($data){
    $data = explode('/', $data);

    switch ($data[0]) {
      case 'user':
        $this->user->me($data[1]);
        echo json_encode($this->user);
        break;
        
      case 'profile':
        echo $this->user->profile($data[1]);
        break;
        
      case 'categories':
        echo $this->listing->getCategories();
        break;
        
      case 'getFeatured':
        if(isset($data[1])){
          echo $this->listing->getFeaturedwithSaved($data[1]);
        } else {
          echo $this->listing->getFeatured();
        }      
        break;
      
      case 'getThreads':
        echo $this->chat->getThreads();
        break;  
        
      case 'getListing':
        echo $this->listing->getById($data[1]);
        break;
      
      case 'userListings':
        echo $this->listing->userListings($data[1]);
        break;
        
      case 'search':
        echo $this->search->SearchController($data[1]);
        break;
      
      case 'getCounties':
        echo $this->location->getCounties();
        break;
      
      case 'delete':
        echo $this->listing->deleteListing($data[1]);
        break;
        
      default:
        # code...
        break;
    }

    
    
    //$setPass = $user->setPass();
    //return json_encode($data);
  }

}

