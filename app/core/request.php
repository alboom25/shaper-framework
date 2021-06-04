<?php
$no_direct_script  = array('Status'=> 403,'Data'=> null,'Message'=> 'No direct script access allowed');
defined('BASE_DIR') OR exit(json_encode($no_direct_script));

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,HEAD,POST,OPTIONS');
header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept, Authorization, token');

class Request{
    public $body = [];
    public $query = [];
    public $headers = [];
    public $path ='/';
    public $method ='get';
    public $current_url;
    public $full_url;

    function __construct(){
        $this->path = explode('?', $_SERVER['REQUEST_URI'], 2)[0];       
        $this->current_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST']. explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        $this->full_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
        $this->query = $_GET;
        $this->body = $_POST;        
        if(file_get_contents('php://input')){            
            $this->body = json_decode(file_get_contents('php://input'), true);
        }  
        $this->headers = getallheaders();       
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);      
    }  

    public function redirect($url){
        header('Location:'.$url); exit;
    }
}

