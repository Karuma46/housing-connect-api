<?php

header('Content-Type: application/json');



$method = $_SERVER['REQUEST_METHOD'];

require_once('./classes/get.php');
require_once('./classes/post.php');
require_once('./classes/logs.php');


$Getter = new Getter();
$Poster = new Poster();
$Logger = new Logger();


//HANDLING POST REQUESTS
if($method == 'POST') {
  $data = json_decode(file_get_contents('php://input'));
  echo $Poster->post($data);
  
  $log_arr = array(
    "content" => json_encode($data),
    "source" => json_encode($_SERVER)
  );
    
  echo $Logger->mkLog($log_arr);
  
} 


// HANDLING GET REQUESTS
elseif ($method == 'GET') {
  $data = $_SERVER['QUERY_STRING'];
  echo $Getter->get($data);
  
  $log_arr = array(
    "content" => json_encode($data),
    "source" => json_encode($_SERVER)
  );
    
  echo $Logger->mkLog($log_arr);
}