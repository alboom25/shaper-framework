<?php

define('ENVIROMENT', 'development');

define('LOG_ERRORS', 1);

$start_time = microtime(true);

define('BASE_DIR', __DIR__.'/app');

require_once BASE_DIR. '/core/response.php';

$res = new Response();

function error_handler($errno, $errstr, $error_doc, $err_line) {   
    $res = new Response();  
    if(LOG_ERRORS){
        $log_file = BASE_DIR.'/logs/errors_'. Date('d_m_Y').'.log';      
        file_put_contents($log_file, Date('d-m-Y H:i:s') .': Warning Error: '.$errstr. ' at '.$error_doc.' on line '.$err_line. "\n", FILE_APPEND | LOCK_EX);
    }   
  
   if(ENVIROMENT=='production'){
    $res->error('A technical error may have occurred while processing the request');
   }else{
    $res->error($errstr .' at '. $error_doc . ', line:' . $err_line);
   }    
}
  
set_error_handler('error_handler');

function exception_handler($exception, $res) {     
    if(LOG_ERRORS){
        $log_file = BASE_DIR.'/logs/errors_'. Date('d_m_Y').'.log';      
        file_put_contents($log_file,  Date('d-m-Y H:i:s') .': Exception Error: '.$exception->getMessage(). ' at '.$exception->getFile().' on line '.$exception->getLine(). "\n", FILE_APPEND | LOCK_EX);
    }

    if(ENVIROMENT=='production'){
        switch($exception->getCode()){
            case 23000:               
                    $res->error('Data integrity violation: Uploaded data already exists!');
                    break;
                default:
                    $res->error('An exception error may have occurred while processing the request');
        }
      
    }else{ 
        $res->error($exception->getMessage());
    }
}

set_exception_handler(function($exception) use($res){
    exception_handler($exception, $res);
});



require_once BASE_DIR . '/core/app.php';

$app = new App();

$app->run();

?>
