<?php
$no_direct_script  = array("Status"=> 403,"Data"=> null,"Message"=> "No direct script access allowed");
defined("BASE_DIR") OR exit(json_encode($no_direct_script));

class MysqlDumper{
    private $conn;
    private $db_name;
    private $username;
    private $hostname;
    private $definer;

    public function __construct($hostname, $database, $port, $username, $password){
        $con = new PDO("mysql:host=". $hostname.";dbname=". $database.";port=".$port, $username, $password);          
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn=$con; 
        $this->db_name = $database;
        $this->username = $username;
        $this->hostname = $hostname;
        $this->definer = "DEFINER=`" . $this->username . "`@`" . $this->hostname . "`";
    }

    public function schema($dump_data=false, $skip_tables=null){
        $return_string = "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";

        $return_string .= "START TRANSACTION; SET FOREIGN_KEY_CHECKS=0;\n\n";

        $return_string .= "\n--\n-- Procedures\n--\n";

        $procedures = $this->getSchemaProcedures();

        foreach($procedures as $procedure){       
            $definition = $this->getProcedureDefinition($procedure["ROUTINE_NAME"]);           
            $return_string .= "\nDROP PROCEDURE IF EXISTS `" .$procedure['ROUTINE_NAME']. "`;\n" .$this->no_definer($definition['Create Procedure']). ";\n-- --------------------------------------------------------";
        }

        $return_string .= "\n--\n-- Functions\n--\n";

        $functions = $this->getSchemaFunctions();
        foreach($functions as $function){       
            $definition = $this->getFunctionDefinition($function["ROUTINE_NAME"]);           
            $return_string .= "\nDROP FUNCTION IF EXISTS `" .$function['ROUTINE_NAME']. "`;\n" .$this->no_definer($definition['Create Function']). ";\n-- --------------------------------------------------------";
        }

        $return_string .= "--\n-- Tables\n--\n";

        $tables = $this->getSchemaTables();
        foreach ($tables as $table) {
            if (!in_array($table['TABLE_NAME'], $skip_tables)) {
                $definition = $this->getTableDefinition($table["TABLE_NAME"]);           
                $return_string .= "\nDROP TABLE IF EXISTS `" .$table['TABLE_NAME']. "`;\n" .$this->no_definer($definition['Create Table']). ";\n-- --------------------------------------------------------";
            }
        }

        $return_string .= "\n--\n-- Stand-in structure for views\n--\n";

        $views = $this->getSchemaViews();
        foreach ($views as $view) {
            if (!in_array($view['TABLE_NAME'], $skip_tables)) {
                $definition = $this->getViewStructure($view["TABLE_NAME"]);
                $view_structure = "CREATE TABLE `" . $view["TABLE_NAME"] . "` (\n";
                foreach ($definition as $def) {                 
                    $view_structure .= "`" . $def['Field'] . "` " . $def['Type'] . "\n,";
                }
                $view_structure = substr($view_structure, 0, strlen($view_structure) - 1) . ");";               
                $return_string .= "\n--\n-- Stand-in structure for view `" . $view["TABLE_NAME"] . "`\n--\n" . $view_structure . "\n\n-- --------------------------------------------------------";
            }
        }

        $return_string .= "\n--\n-- Views\n--\n";

        foreach ($views as $view) {
            if (!in_array($view['TABLE_NAME'], $skip_tables)) {
                $definition = $this->getTableDefinition($view["TABLE_NAME"]);
                $return_string .= "\n--\n-- Structure for view `" . $view["TABLE_NAME"] . "`\n--\nDROP TABLE IF EXISTS `" . $view["TABLE_NAME"] . "`;\n\n" .$this->no_definer($definition['Create View']) . ";\n\n-- --------------------------------------------------------";                
            }
        }

        $return_string .= "\n--\n-- AUTO_INCREMENTS\n--\n";

        $increments = $this->getSchemaAutoincrements();

        foreach ($increments as $increment) {
            if (!in_array($increment['TABLE_NAME'], $skip_tables)) {
                $return_string .= $increment['field_data'] . "\n\n";
            }           
        }

        $return_string .= "\n\n-- --------------------------------------------------------";

        $return_string .= "\nCOMMIT;\nSET FOREIGN_KEY_CHECKS=1;";

        return $return_string;

    }

    private function getSchemaProcedures(){
        $sql= "SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_SCHEMA = :db_name AND ROUTINE_TYPE = 'PROCEDURE'";
        $stmt= $this->conn->prepare($sql);
        $stmt->execute(array(':db_name'=>$this->db_name));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);      
    }

    private function getProcedureDefinition($procedure){
        $sql = "SHOW CREATE PROCEDURE ". $procedure;
        $stmt= $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    private function getSchemaFunctions(){
        $sql= "SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_SCHEMA = :db_name AND ROUTINE_TYPE = 'FUNCTION'";
        $stmt= $this->conn->prepare($sql);
        $stmt->execute(array(':db_name'=>$this->db_name));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);   
    }

    private function getFunctionDefinition($procedure){
        $sql = "SHOW CREATE FUNCTION ". $procedure;
        $stmt= $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    private function getSchemaTables(){
        $sql= "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = :db_name AND TABLE_TYPE='BASE TABLE'";
        $stmt= $this->conn->prepare($sql);
        $stmt->execute(array(':db_name'=>$this->db_name));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    private function getTableDefinition($tables){
        $sql = "SHOW CREATE TABLE ". $tables;
        $stmt= $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getSchemaViews(){
        $sql= "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = :db_name";
        $stmt= $this->conn->prepare($sql);
        $stmt->execute(array(':db_name'=>$this->db_name));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    private function getViewStructure($view){
        $sql = "DESCRIBE ". $view;
        $stmt= $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getSchemaAutoincrements(){
        $sql= "SELECT TABLE_NAME, CONCAT( 'ALTER TABLE `', TABLE_NAME, '` MODIFY `', COLUMN_NAME, '` ', DATA_TYPE, IF ( IS_NULLABLE = 'NO', ' NOT NULL ', ' ' ), 'AUTO_INCREMENT;' ) AS field_data FROM `information_schema`.`COLUMNS` WHERE `EXTRA` = 'auto_increment' AND `TABLE_SCHEMA` = :db_name";
        $stmt= $this->conn->prepare($sql);
        $stmt->execute(array(':db_name'=>$this->db_name));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    private function no_definer($orig=''){      
       return str_replace('ALGORITHM=UNDEFINED  SQL SECURITY DEFINER', 'OR REPLACE', str_replace($this->definer,'',$orig));
    }
}

