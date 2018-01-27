<?php
/*	$_POST['apiName'] = "sendOfflineChatMessagePushNotification";
	// the message
	print_r($_POST);
	print_r($_GET);
$msg = json_encode($_SERVER).json_encode($_POST).json_encode($_REQUEST)." Kishan First line of text\nSecond line of text";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
mail("kp@messapps.com","My subject",$msg);
*/ 
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
				$from         = getPostVar('from','trademade_95');
				$to     		 = getPostVar('to','trademade_96');
				$body   		 = getPostVar('body',"Manual");
				$message_id		 = getPostVar('message_id');
				$access_token	 = getPostVar('access_token',"tadr4dpp2ah460an6sta16kisebeemTM");
				
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
				
			/*		$_POST['apiName'] = "TEEEEEEEEEEEEEEEEEEEE";
						// the message
						print_r($_POST);
						print_r($_GET);
					$msg = json_encode($json_data)." Kishan First line of text\nSecond line of text";
					*/
					// use wordwrap() if lines are longer than 70 characters
			//		$msg = wordwrap($msg,70);
					
					// send email
			//		mail("kp@messapps.com","My subject",$msg);
                
            break;
        		
     
    }// END OF SWITCH CASE
  /*  
    if(!is_array($json_data) || count($json_data) == '0')
    {
        $json_data = array(
                'statusCode'	=> '0',
                'message'	    => "Invalid request..!!",
            );
    }
   
    $json_data['message'] = ($json_data['message']);
    $json_data = strip_slashes_recursive($json_data);
    
    array_walk_recursive($json_data,function(&$item){$item=strval($item);});
   
    header('Content-type:application/json; charset=utf-8');
    echo json_encode($json_data); */
?>