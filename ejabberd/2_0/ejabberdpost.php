<?php
    // Jay Guru Maharaj
    // START:17-12-2015
    require_once('../../config/1_0/config.php');
    
    // COMMON FUNCTION AND LIBRARIES INCLUDED IN COMMON.PHP
    require_once('../../config/1_0/common.php');
  
    includeClasses('1_0/common');
    $common     = new Common();
    
    $utc_time       = time();
    $json_data      = array();
	
	$_POST['apiName'] = "sendOfflineChatMessagePushNotification";
    $api_function   = urldecode( (isset($_POST['apiName']) && $_POST['apiName'] != '') ? $_POST['apiName'] : '');
    
    $logPostArr = isset($_POST) && is_array($_POST) && count($_POST) > 0 ? $_POST : array();
    $logGetArr  = isset($_GET) && is_array($_GET) && count($_GET) > 0 ? $_GET : array();
    $logFileArr  = isset($_FILES) && is_array($_FILES) && count($_FILES) > 0 ? $_FILES : array();
    $common->requestLogData($api_function,$logPostArr,$logGetArr,$logFileArr);
    
    switch($api_function)
    {
        case 'sendOfflineChatMessagePushNotification':	// tp // done
				$from         = getPostVar('from');
				$to     		 = getPostVar('to');
				$body   		 = getPostVar('body');
				$message_id		 = getPostVar('message_id');
				$access_token	 = getPostVar('access_token');
				
				// set in mod_http_offline
				if($access_token != "tadr4dpp2ah460an6sta16kisebeemTM" || $from == "" || $to == "" || $body == ""){
					break;
				}
             
                $inputData = array(
                        'from'      => $from,
                        'to'     => $to,
                        'body'  => $body,
                    );
                $json_data = $common->sendOfflineChatMessagePushNotification($inputData);
	              
            break;
        		
     
    }// END OF SWITCH CASE
  
?>