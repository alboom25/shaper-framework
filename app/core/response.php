<?php
$no_direct_script  = array("Status"=> 403,"Data"=> null,"Message"=> "No direct script access allowed");
defined("BASE_DIR") OR exit(json_encode($no_direct_script));

class Response{
    function error($message){
        global $start_time;       
        $data = array(
            "Status"=> 500,
            "Data"=> null,
            "Message"=> $message,           
          );
          exit(json_encode($data)); 
    }

    function success($message, $data=null){
        global $start_time;        
        $data = array(
            "Status"=> 200,
            "Data"=> $data,
            "Message"=> $message,           
          );
          exit(json_encode($data)); 
    }

    function not_found($message, $data=null){       
        global $start_time;        
        $data = array(
            "Status"=> 404,
            "Data"=> $data,
            "Message"=> $message,           
          );
          exit(json_encode($data)); 
    }

    function unauthorized($message){
        global $start_time;        
        $data = array(
            "Status"=> 401,
            "Data"=> null,
            "Message"=> $message,           
          );
          exit(json_encode($data)); 
    }

    function bad_request($message){
        global $start_time;        
        $data = array(
            "Status"=> 400,
            "Data"=> null,
            "Message"=> $message,           
          );
          exit(json_encode($data));         
    }
}
