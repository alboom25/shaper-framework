<?php
$no_direct_script  = array('Status'=> 403,'Data'=> null,'Message'=> 'No direct script access allowed');
defined('BASE_DIR') OR exit(json_encode($no_direct_script));
//register for africa's talking and get api credentials to send sms
class SMS{
    protected $apiKey = 'your_api_key';
    protected $username ='your_username';
    function send($phone, $message, $api_key=null){

        $url =  'https://api.africastalking.com/restless/send?username='. $this->username . '&Apikey=' . $this->apiKey . '&to=+' . $phone . '&message=' . urlencode($message);      
 
        $curl = curl_init();
     
        curl_setopt($curl, CURLOPT_URL, $url);
         
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/json'));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
        
        $data = curl_exec($curl);
        
        curl_close($curl);

        if (strpos($data, 'missing') !== false || strpos($data, 'failed') !== false) {
            return false;
        }else{
            $jdata   = json_decode($data);
            
            $status = $jdata->SMSMessageData->Recipients[0]->status;
        
            if($status=='Success'){
                return true;
            }else{
                return false;
            }		
        }
    }
}