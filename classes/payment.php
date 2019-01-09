<?php
  
  class Payment extends general{

    PUBLIC $pupil_id;
    PUBLIC $mode;
    PUBLIC $amount;
    PUBLIC $paid_by;
    PUBLIC $date_added;
    PUBLIC $payment_id;
    PUBLIC $table_name = 'payments';
    PUBLIC $db;

    public function __construct(){
      $this->db = new Database;
    }

    public function 
  }