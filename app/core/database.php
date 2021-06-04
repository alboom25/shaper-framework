<?php
$no_direct_script  = array('Status'=> 403,'Data'=> null,'Message'=> 'No direct script access allowed');
defined('BASE_DIR') OR exit(json_encode($no_direct_script));

require_once BASE_DIR.'/config/db.config.php';

class DBModel{
    public $conn;
    function __construct(){
       
        $config = new DBConfig();
        $pars = $config->default;      
        if($config->defaultGroup =='production'){           
            $pars = $config->production;
        }elseif($config->defaultGroup =='sandbox'){   
            $pars = $config->sandbox;
        }elseif($config->defaultGroup =='stagging'){   
            $pars = $config->stagging;
        }
        $con = new PDO('mysql:host='. $pars['hostname'].';dbname='.$pars['DBPrefix']. $pars['database'].';port='.$pars['port'].';charset='.$pars['charset'], $pars['username'], $pars['password']);          
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn=$con;       
    }

    function column_names($data){
        $column_string =[];
        foreach( array_keys($data) as $key ) { 
             array_push($column_string, $key);               
        }   
        return $column_string;
    }

    function b_array($data){
        return array_combine(
            array_map(function($k){ return ':'.$k; }, array_keys($data)),
            $data
        );
    }

    function get($table){      
        
    }

    function get_where($table_name, $data=null){
        $bind_array = $this->b_array($data);

        $column_string =[];
       foreach( array_keys($data) as $key ) { 
            array_push($column_string, ':'. $key.'='. $key);               
        }         

        $sql = 'SELECT * FROM '. $table_name .' WHERE '.implode(' AND ', $column_string) ;      
        $stmt = $this->conn->prepare($sql);      
        $stmt->execute($bind_array);
        $result = $stmt->fetch();
        $this->conn=null;         
        if(isset($result)){
            return $result; 
        }else{
            return null; 
        }
    }

    function insert($table, $data){
        $column_string =$this->column_names($data);
        $bind_array = $this->b_array($data);
        $sql = 'INSERT INTO '. $table .' ('.implode(', ', $column_string) .') VALUES (' . implode(', ', array_keys($bind_array)) . ')';
        $stmt = $this->conn->prepare($sql);      
        $stmt->execute($bind_array); 
        $this->conn=null;              
        return true;
    }

    function replace($table, $data){
        $column_string =$this->column_names($data);
        $bind_array = $this->b_array($data);
        $sql = 'REPLACE INTO '. $table .' ('.implode(', ', $column_string) .') VALUES (' . implode(', ', array_keys($bind_array)) . ')';
        $stmt = $this->conn->prepare($sql);      
        $stmt->execute($bind_array);  
        $this->conn=null;             
        return true;
    }

    function update($table, $data, $where_data) {
        $bind_array = $this->b_array(array_merge($data, $where_data));

        $column_string =[];
        foreach( array_keys($data) as $key ) { 
            array_push($column_string, $key.'=:'. $key);               
        } 
        
        $where_columns =[];
        foreach( array_keys($where_data) as $key ) { 
            array_push($where_columns, $key.'=:'. $key);               
        } 

        $sql = 'UPDATE '. $table .' SET '.implode(', ', $column_string) .' WHERE ' . implode(' AND ', $where_columns);
        $stmt = $this->conn->prepare($sql);      
        $stmt->execute($bind_array);    
        $this->conn=null;         
        return true;
    }

    function sql($sql){
        $stmt = $this->conn->prepare($sql);      
        $stmt->execute();  
        $this->conn=null;             
        return true;
    }

    
}