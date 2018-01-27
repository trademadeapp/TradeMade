<?php
     // SHREE
    require_once('../../config/1_0/config.php');
    
    // COMMON FUNCTION AND LIBRARIES INCLUDED IN COMMON.PHP
    require_once('../../config/1_0/common.php');
    
    set_time_limit(0);
    ini_set('memory_limit', '-1');
	ini_set('max_execution_time', 0);
    
    includeClasses('1_0/common');
    
    $common     = new Common();
    
    $utc_time       = time();
    $json_data      = array();
    
    $_POST = array();
    
    /* IMPORTANT NOTE : $argv will created automatically from exec command run in background...
     * IN exec not get or post generaate it generate $argv with space separate argument
     * and receive in array start from zero index with URL too..
     * SEMPALE : $argv[0] = "DOCROOT."api/1_0/sendNotification.php apiName=sendCreateNewGroupPushNotification
     *          $argv[1] = "apiName=sendCreateNewGroupPushNotification";
        $argv[2] = "userId=1";
        $argv[3] = "groupId=75";
    */
	
	// Argument convert in post variables
	if(isset($argv)){
		foreach($argv as $keyA => $valueA){
			$arr = explode("=",$valueA);
			if(isset($arr[0]) && $arr[0] != '' && isset($arr[1]) && $arr[1] != ''){
				$_POST[$arr[0]] = $arr[1];
			}
		}
	//	mail('kp@messapps.com',$_POST['apiName'],$_POST['apiName']);
    }
	
    $api_function   = urldecode( (isset($_POST['apiName']) && $_POST['apiName'] != '') ? $_POST['apiName'] : '');
    
    $logPostArr = isset($_POST) && is_array($_POST) && count($_POST) > 0 ? $_POST : array();
    $logGetArr  = isset($_GET) && is_array($_GET) && count($_GET) > 0 ? $_GET : array();
    $logFileArr  = isset($_FILES) && is_array($_FILES) && count($_FILES) > 0 ? $_FILES : array();
 //   $common->requestLogData($api_function,$logPostArr,$logGetArr,$logFileArr);
    

    switch($api_function)
    {
		//*/3 * * * * cd /var/www/html/api/1_0 && /usr/bin/php -q cronScript.php apiName=cronSendAPNSForRemainReviewAfterTrade > /var/www/html/cronScript.log 2>&1
        case 'cronSendAPNSForRemainReviewAfterTrade':
                $json_data = $common->cronSendAPNSForRemainReviewAfterTrade();
            break;
		
		//*/5 * * * * cd /var/www/html/api/1_0 && /usr/bin/php -q cronScript.php apiName=cronSendTroveAddNotification > /var/www/html/cronScript.log 2>&1
		case 'cronSendTroveAddNotification':
	             $json_data = $common->cronSendTroveAddNotification();
            break;
		// * 1 * * * cd /var/www/html/api/1_0 && /usr/bin/php -q cronScript.php apiName=removeOldRequestLog > /var/www/html/cronScript.log 2>&1
		case 'removeOldRequestLog':
	             $json_data = $common->removeOldRequestLog();
            break;
		
    }// END OF SWITCH CASE
    
    if(!is_array($json_data) || count($json_data) == '0')
    {
        $json_data = array( 
                'statusCode'	=> '0',
                'message'	    => "Invalid request apiname..!!",
            );
    }
   
    $json_data['message'] = ($json_data['message']);
    $json_data = strip_slashes_recursive($json_data);
    
    array_walk_recursive($json_data,function(&$item){$item=strval($item);});
   
    header('Content-type:application/json; charset=utf-8');
    echo json_encode($json_data);
?>