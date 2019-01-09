<?php

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  
  require 'vendor/autoload.php';

  require_once('db.php');

  class General{
    
    // GENERAL FUNCTIONS
    public function getHash($password) {
      $salt = sha1(rand());
      $salt = substr($salt, 0, 10);
      $encrypted = password_hash($password.$salt, PASSWORD_DEFAULT);
      $hash = array("salt" => $salt, "encrypted" => $encrypted);
      return json_encode($hash);
    }
    
    public function getDate() {
      $tz = 'Africa/Nairobi';
      $timestamp = time();
      $dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
      $dt->setTimestamp($timestamp); //adjust the object to correct timestamp
      $date = $dt->format('Y-m-d H:i:s');
      return $date;
    }
    
    public function moneyFormatter($amount){
      if($amount > 999999){
        $new = bcdiv($amount, 1000000 , 1);
        $new = $new.'M';
        return $new;
        
      } else if ($amount > 9999){
        $new = floor($amount/1000);
        $new = $new.'K';
        return $new;
        
      } else {
        return $amount;
      }
    }
    
    public function response($status,$message,$data){
      $response['result'] = $status;
      $response['message'] = $message;
      if(empty($data)){
      } else {
        $response['data'] = $data;  
      }
      return json_encode($response);
    }

    public function mailer($obj){

      $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
      
      try {
          //Server settings
          $mail->SMTPDebug = 2;                                 // Enable verbose debug output
          $mail->isSMTP();                                      // Set mailer to use SMTP
          $mail->Host = 'mail.housingconnect.co.ke';  // Specify main and backup SMTP servers
          $mail->SMTPAuth = true;                               // Enable SMTP authentication
          $mail->Username = 'info@housingconnect.co.ke';                 // SMTP username
          $mail->Password = 'cfw]AQiJ@hpY';                           // SMTP password
          $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
          $mail->Port = 465;                                    // TCP port to connect to

          //Recipients
          $mail->setFrom('info@housingconnect.co.ke', 'Mailer');
          //$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
          $mail->addAddress($obj['email']);               // Name is optional
          //$mail->addReplyTo('info@example.com', 'Information');
          //$mail->addCC('cc@example.com');
          //$mail->addBCC('bcc@example.com');

          //Attachments
          //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
          //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

          //Content
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = $obj['subject'];
          $mail->Body    = $obj['body'];
          $mail->AltBody = $obj['altBody'];

          $mail->send();
          return true;
      } catch (Exception $e) {
          return $mail->ErrorInfo;
      }
    }

  }