<?php
ob_start('ob_gzhandler'); //compress output

header('Content-Type: application/json; charset=UTF-8'); //make output JSON

$no_direct_script  = array('Status'=> 403,'Data'=> null,'Message'=> 'No direct script access allowed');
defined('BASE_DIR') OR exit(json_encode($no_direct_script));

require_once BASE_DIR.'/config/app.config.php';
require_once BASE_DIR.'/config/autoloads.php';
require_once BASE_DIR. '/core/request.php';
require_once BASE_DIR.'/core/database.php';

require_once 'vendor/autoload.php';

$time_zone = $config['timezone'] OR 'Africa/Nairobi';
$base_url = $config['base_url'];

date_default_timezone_set($time_zone);

class App { 
    public $res;
    public $req;
    private $base_url;
    public $group;

    function __construct(){
        $this->res = new Response();
        $this->req = new Request();       
        global $base_url;  
        $this->base_url =$base_url;    
    }  

    function load_dependencies(){       
        global $autoload;
        $libraries = $autoload['libraries'];
        foreach ($libraries as $lib){
            $this->autoload(BASE_DIR.'/libs/'.$lib.'.php');
        }

        $models = $autoload['models'];
        if(count($models)==1){
            if($models[0]=='*'){
                $folder =   BASE_DIR.'/models/';
                $files = glob($folder.'*.php'); // return array files       
                foreach($files as $file){
                   $this-> autoload($file); 
                }
            }else{
                $this->autoload(BASE_DIR.'/models/'.$models[0].'.php');
            }

        }else{
            foreach ($models as $mod){
                $this->autoload(BASE_DIR.'/models/'.$mod.'.php');
            }  
        }           
    }

    function run(){   
        global $app;
        
        $this->load_dependencies(); 

        $folder =   BASE_DIR."/routes/";
        
        $files = glob($folder.'*.php'); 
        
        foreach($files as $file){           
           $this-> autoload($file); 
        }
        $this->use(function($req, $res) {           
            $res->not_found('Requested content cannot be located - '. $req->full_url);
        });
    }

    function autoload($location) {        
        if (file_exists($location)) {                   
            require_once $location;               
        }
    }

    public function get($url, callable $callback){      
        if(strcmp($this->req->method, 'get') == 0){
            if(strcmp($this->base_url.$this->group.$url, $this->req->current_url) == 0){
                $callback($this->req, $this->res);
            }elseif(strcmp($url, '*') == 0){
            $callback($this->req, $this->res);
        }            
        }
    }  

    public function all($url, callable $callback){              
        if(strcmp($this->base_url.$this->group.$url, $this->req->current_url) == 0){
            $callback($this->req, $this->res);
        }elseif(strcmp($url, '*') == 0){
            $callback($this->req, $this->res);
        }
    }  
    
    public function post($url, callable $callback){         
        if(strcmp($this->req->method, 'post') == 0){
            if(strcmp($this->base_url.$this->group.$url, $this->req->current_url) == 0){
                $callback($this->req, $this->res);
            }elseif(strcmp($url, '*') == 0){
                $callback($this->req, $this->res);
            }            
        }       
    }   
    
    public function use(callable $callback){
        $callback($this->req, $this->res);
    }

    public function group($prefix, callable $callback){ 
        if (strncmp($this->req->current_url, $this->base_url.$prefix, strlen($this->base_url.$prefix)) === 0){           
            $this->group .= $prefix;            
            $callback($this->req, $this->res);
        }
    }  
    
    public function redirect($url){
        header('Location:'.$url); exit;
    }
}

?>