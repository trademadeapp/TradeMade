<?php 
/* This is Common include files for all pages. This files includes all basic necessary classes and functions and files. */

require_once DOCROOT.'classes/libraries/MysqliDb/MysqliDb.class.php';
require_once DOCROOT.'classes/libraries/PHPMailer/class.phpmailer.php';
require_once DOCROOT.'classes/libraries/Resize.class.php';
//echo DOCROOT.'classes/libraries/Resize.class.php';
/* for creating object of database method */
$db = new MysqliDb();

function getGetVar($param,$default = ""){
	global $db;
	$temp = ( isset($_GET[$param]) && $_GET[$param] != "" ? $db->escape(urldecode($_GET[$param])) : $default);
	return $temp;
}

function getPostVar($param,$default = ""){
	global $db;
	$temp = ( isset($_POST[$param]) && $_POST[$param] != "" ? $db->escape(urldecode($_POST[$param])) : $default);
	return $temp;
}

function getFileVar($param){
	$temp = (!empty($_FILES[$param])) ? $_FILES[$param] : array();
	return $temp;
}

/* The Function displayes array in the well define manner */
function _kd($var)
{
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}
/* The Function for check  file exist on another server */
function fileExists($path)
{
	if(fopen($path,"r")==true)
	{
		return '1';
	}
	else
	{
		return '0';
	}
}


/* The Function includes classes in our file */
function includeClasses($classes)
{
	$classesArr = func_get_args();
	foreach($classesArr as $class)
	{
		$file = DOCROOT.'classes/'.$class.'.class.php';
		if (file_exists($file))
		{
			require_once($file);
		}
	}
}

/* The Function includes classes in our file */
function includeLanguage($lang)
{
	$file = DOCROOT.'langauges/'.$lang.'.language.php';
	if (file_exists($file))
	{
		require_once($file);
	}
}

/* The Function includes Admin classes in our file */
function includeAdminClasses($classes)
{
	$classesArr = func_get_args();
	foreach($classesArr as $class)
	{
		$file = ADMINDOCROOT.'/'.ADMINFOLDER.'classes/'.$class.'.class.php';
		if (file_exists($file))
		{
			require_once($file);
		}
	}
}

/* The Function includes Library file in our file */
function includeLibrary($lib)
{
	$classesArr = func_get_args();
	foreach($classesArr as $class)
	{
		$file = DOCROOT.'classes/libraries/'.$lib.'.class.php';
		if (file_exists($file))
		{
			require_once($file);
		}
	}
}

/* For truncate string and add the ellipses to string */
function truncate($string, $del)
{
	$len = strlen($string);
	if ($len > $del)
	{
		$new = substr($string,0,$del)."...";
		return $new;
	}
	else
	{
		return $string;
	}
}
if(!function_exists('checkArr')){
	function checkArr($array){
		if(is_array($array) && count($array) > 0){
			return true;
		}
		return false;
	}
}

if(!function_exists('validArr')){
	function validArr($array){
		if(is_array($array)){
			return true;
		}
		return false;
	}
}
/*********** Generates JSON ENCODE IF NOT EXIST 21-05-2012 KD *****************/
if(!function_exists('json_encode'))
{
    function json_encode($a=false)
    {
        // Some basic debugging to ensure we have something returned
        if (is_null($a)) return 'null';
        if ($a === false) return 'false';
        if ($a === true) return 'true';
        if (is_scalar($a))
        {
            if (is_float($a))
            {
                // Always use '.' for floats.
                return floatval(str_replace(',', '.', strval($a)));
            }
            if (is_string($a))
            {
                static $jsonReplaces = array(array('\\', '/', "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
            }
            else
                return $a;
        }
	/** ORIGINAL ***/
	
       /* $isList = true;
        for ($i = 0, reset($a); true; $i++) {
            if (key($a) !== $i)
            {
                $isList = false;
                break;
            }
        } */
       
       /** REPLACED ***/
	//$isList = true;

	$isList = (is_array($a))?true:false;

	//for ($i = 0, reset($a), $size = count($a); $i &lt; $size; $i++)
	for ($i = 0, reset($a), $size = count($a); $i < $size; $i++)
	{
		if (key($a) !== $i)
		{
		    $isList = false;
		    break;
		}
		next($a);
	}
	
        $result = array();
        if ($isList)
        {
            foreach ($a as $v) $result[] = json_encode($v);
            return '[' . join(',', $result) . ']';
        }
        else
        {
            foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
            return '{' . join(',', $result) . '}';
        }
    }
}

/*********** Generates JSON DECODE IF NOT EXIST 21-05-2012 KD *****************/
if(!function_exists('json_decode'))
{
    function json_decode($json)
    {
        $comment = false;
        $out = '$x=';
        for ($i=0; $i<strlen($json); $i++)
        {
            if (!$comment)
            {
                if (($json[$i] == '{') || ($json[$i] == '['))
                    $out .= ' array(';
                else if (($json[$i] == '}') || ($json[$i] == ']'))
                    $out .= ')';
                else if ($json[$i] == ':')
                    $out .= '=>';
                else
                    $out .= $json[$i];
            }
            else
                $out .= $json[$i];
            if ($json[$i] == '"' && $json[($i-1)]!="\\")
                $comment = !$comment;
        }
        eval($out . ';');
        return $x;
    }
}
function unsetUserExtraData($dataArr = array(),$notUnset = array()){
	
	if(isset($dataArr['u_photo']) && !in_array('u_photo',$notUnset))
			unset($dataArr['u_photo']);
	if(isset($dataArr['u_thumb_photo']) && !in_array('u_thumb_photo',$notUnset))
			unset($dataArr['u_thumb_photo']);
	if(isset($dataArr['u_xmpp_password']) && !in_array('u_xmpp_password',$notUnset))
			unset($dataArr['u_xmpp_password']);
	if(isset($dataArr['u_password']) && !in_array('u_password',$notUnset))
			unset($dataArr['u_password']);
	if(isset($dataArr['u_email']) && !in_array('u_email',$notUnset))
			unset($dataArr['u_email']);
	
	return $dataArr;
}

function userProfileImage($userId,$userPhoto,$userThumbPhoto=""){
	$returnData = array();
	$returnData['mainUrl'] 		= ($userPhoto != "" && file_exists(API_USER_REL_IMGPATH.$userId."/".$userPhoto)) ? API_USER_ABS_IMGPATH.$userId."/".$userPhoto : "";
	$returnData['thumbUrl'] 	= ($userThumbPhoto != "" && file_exists(API_USER_REL_IMGPATH.$userId."/".THUMB_FLD_NAME.$userThumbPhoto)) ? API_USER_ABS_IMGPATH.$userId."/".THUMB_FLD_NAME.$userThumbPhoto : "";
	
	return $returnData;
}
function troveImage($troveUserId,$troveImage,$troveThumbImage=""){
	$returnData = array();
	$returnData[0]['mainUrl'] 		= ($troveImage != "" && file_exists(API_TROVE_REL_IMGPATH.$troveUserId."/".$troveImage)) ? API_TROVE_ABS_IMGPATH.$troveUserId."/".$troveImage : "";
	$returnData[0]['thumbUrl'] 	= ($troveThumbImage != "" && file_exists(API_TROVE_REL_IMGPATH.$troveUserId."/".THUMB_FLD_NAME.$troveThumbImage)) ? API_TROVE_ABS_IMGPATH.$troveUserId."/".THUMB_FLD_NAME.$troveThumbImage : "";
	
	return $returnData;
}
function troveImageById($troveUserId,$troveId){
	$returnData = array();
	$troveImage = "trv-".$troveId."-1.jpg";
	$troveThumbImage = "trv-".$troveId."-1.jpg";
	$returnData[0]['mainUrl'] 		= ($troveImage != "" && file_exists(API_TROVE_REL_IMGPATH.$troveUserId."/".$troveImage)) ? API_TROVE_ABS_IMGPATH.$troveUserId."/".$troveImage : "";
	$returnData[0]['thumbUrl'] 	= ($troveThumbImage != "" && file_exists(API_TROVE_REL_IMGPATH.$troveUserId."/".THUMB_FLD_NAME.$troveThumbImage)) ? API_TROVE_ABS_IMGPATH.$troveUserId."/".THUMB_FLD_NAME.$troveThumbImage : "";
	
	return $returnData;
}

function troveImageAllById($troveUserId,$troveId){
	$returnData = array();
	$k = 0;
	for($i=1;$i<=3;$i++){
		$troveImage 		 = "trv-".$troveId."-".$i.".jpg";
		$troveThumbImage = "trv-".$troveId."-".$i.".jpg";
		$returnData[$k]['mainUrl'] 		= ($troveImage != "" && file_exists(API_TROVE_REL_IMGPATH.$troveUserId."/".$troveImage)) ? API_TROVE_ABS_IMGPATH.$troveUserId."/".$troveImage : "";
		$returnData[$k]['thumbUrl'] 	= ($troveThumbImage != "" && file_exists(API_TROVE_REL_IMGPATH.$troveUserId."/".THUMB_FLD_NAME.$troveThumbImage)) ? API_TROVE_ABS_IMGPATH.$troveUserId."/".THUMB_FLD_NAME.$troveThumbImage : "";
		
		if($returnData[$k]['mainUrl'] == "" && $returnData[$k]['thumbUrl'] == ""){
			unset($returnData[$k]);
		}else{
			$k++;
		}
	}
	return $returnData;
}

function categoryImage($catPhoto,$servicePhoto){
	$returnData = array();
	$returnData['mainUrl'] = ($catPhoto != "" && file_exists(API_CATEGORY_REL_IMGPATH."badge/".$catPhoto)) ? API_CATEGORY_ABS_IMGPATH."badge/".$catPhoto : "";
	$returnData['serviceUrl'] = ($servicePhoto != "" && file_exists(API_CATEGORY_REL_IMGPATH."service/".$servicePhoto)) ? API_CATEGORY_ABS_IMGPATH."service/".$servicePhoto: "";	
	return $returnData;
}
/*********** Generates a File Upload Code 10 FEB 2014 KD*****************/
function UploadFile($files,$path,$type='',$overWrite="false")
{	
	
	if($type == '1')
	{
		$extensions	=	array('jpeg','JPEG','gif','GIF','png','PNG','jpg','JPG');
	}
	else if($type == '2')
	{
		$extensions	=	array('wmv','WMV','wav','WAV','m4r','M4R','mpeg','MPEG','mpg','MPG','mpe','MPE','mov','MOV','avi','AVI','mp4','MP4','m4v','M4V');
	}
	else if($type == '3')
	{
		$extensions	=	array('mp3','MP3','AAC','aac');
	}
	else
	{
		$extensions	=	array('jpeg','JPEG','gif','GIF','png','PNG','jpg','JPG','pdf','PDF','ZIP','zip','rar','RAR','html','HTML','TXT','txt','doc','docx','DOC','DOCX','ppt','PPT','pptx','PPTX','xlsx','XLSX','xls','XLS','exe','EXE','mp3','MP3','wav','WAV','m4r','M4R','mpeg','MPEG','mpg','MPG','mpe','MPE','mov','MOV','avi','AVI','wmv','WMV','3gp','3GP');
	}
	
	$destination 		=	$path.$files["name"];
	
	// GET FILE PARTS
	$fileParts		=	pathinfo($files['name']);
	$file_name		=	$files['name'];
	$file_name_only		=	$fileParts['filename'];
	$file_name_only		= 	preg_replace('/[^a-zA-Z0-9]/','-',$file_name_only);
	$file_extention		=	$fileParts['extension'];
	$Count			=	0;
	
	$destination 		=   	$path.$file_name_only.".$file_extention";
	$file_name		=	$file_name_only.".$file_extention";;
	
	
	// THIS SHOULD KEEP CHECKING UNTIL THE FILE DOESN'T EXISTS
	while( file_exists($destination) && $overWrite == "false")
	{
		$Count 		+= 1;
		$destination 	=  $path. $file_name_only."-".$Count.".$file_extention";
		$file_name 	=  $file_name_only."-".$Count.".$file_extention";
	}
	
	
	
	if(!empty($files))
	{
	//	$filename=$files['name'];
	//	$fileextension=substr($filename,strpos($filename,".")+1);
	//	if(in_array($fileextension,$extensions))
		if(in_array($file_extention,$extensions))
		{
			if(move_uploaded_file($files["tmp_name"],$destination))
			{
				return $file_name;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}	
	} 
}
if ( ! function_exists('crypto_rand_secure'))
{
	function crypto_rand_secure($min, $max) {
			$range = $max - $min;
			if ($range < 0) return $min; // not so random...
			$log = log($range, 2);
			$bytes = (int) ($log / 8) + 1; // length in bytes
			$bits = (int) $log + 1; // length in bits
			$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
			do {
				$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
				$rnd = $rnd & $filter; // discard irrelevant bits
			} while ($rnd >= $range);
			return $min + $rnd;
	}
}
if ( ! function_exists('getUniqueToken'))
{
	function getUniqueToken($length,$seeds='allalphanum'){
		$token = "";
		
		 // Possible seeds
		$seedings['alpha'] 					= 'abcdefghijklmnopqrstuvwqyz';
		$seedings['numeric'] 				= '0123456789';
		$seedings['alphanum'] 				= 'abcdefghijklmnopqrstuvwqyz0123456789';
		$seedings['allalphanum'] 			= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwqyz0123456789';
		$seedings['upperalphanum'] 			= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$seedings['alphanumspec'] 			= 'abcdefghijklmnopqrstuvwqyz0123456789!@#$%^*-_=+';
		$seedings['alphacapitalnumspec'] 	= 'abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789#@!*-_';
		$seedings['hexidec'] 				= '0123456789abcdef';
		$seedings['customupperalphanum'] 	= 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; //Confusing chars like 0,O,1,I not included
		
		// Choose seed
		if (isset($seedings[$seeds])){
			$seeds 			= $seedings[$seeds];
		}
	
		
		for($i=0;$i<$length;$i++){
			$token .= $seeds[crypto_rand_secure(0,strlen($seeds))];
		}
		return $token;
	}
}

if(!function_exists('sendSmtpEmail')){
	function sendSmtpEmail($from, $to, $subject, $message='', $name=''){
		$mail					= new PHPMailer();
		$mail->isSMTP();
		$mail->Host				= MAIL_HOST;
		$mail->SMTPAuth			= true;
		$mail->Username			= MAIL_USER;
		$mail->Password			= MAIL_PASSWORD;
		$mail->SMTPSecure		= 'tls';
		$mail->CharSet			= 'UTF-8';
		$mail->isHTML(true);
		$body					=  $message;
			
		$mail->SetFrom($from);
		$mail->AddAddress($to, $name);
		$mail->Subject			= $subject;
		$mail->MsgHTML($body);
		
		$status		= $mail->Send();
		return $status;
	}
}

/*---------------------------- STRIP SLASH RECURSIVE 30-04-2013 -------------------------------*/
if ( ! function_exists('strip_slashes_recursive'))
{
	function strip_slashes_recursive( $variable )
	{
	    if ( is_string( $variable ) )
		return stripslashes( $variable ) ;
	    if ( is_array( $variable ) )
		foreach( $variable as $i => $value )
		    $variable[ $i ] = strip_slashes_recursive( $value ) ;
	   
	    return $variable ;
	}
}

function send_notification_ios_with_pass($userdeviceToken, $body = array())
{
	if(IS_IOS_PUSH_ON != 'true')
	{
		return true;
	} 
	// Construct the notification payload
	if(!isset($body['aps']['sound']))
	{
		$body['aps']['sound'] = "default";
	}

	// End of Configurable Items
	$payload     = json_encode($body);
	
	$filename     = IOS_PUSH_PEM_PATH;
	$cer_filename     = IOS_PUSH_ENTRUST_CERT_PATH;
	if(!file_exists($filename) || !file_exists($cer_filename))
	{
		return true;
	}

	if(is_array($userdeviceToken) && count($userdeviceToken) > 0)
	{
		$ctx     = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $filename);
		stream_context_set_option($ctx, 'ssl', 'passphrase', IOS_PUSH_PASS_PHRASE);
		stream_context_set_option($ctx, 'ssl', 'cafile', $cer_filename);
		
		foreach($userdeviceToken as $key => $token_rec_id)
		{
			if(IS_PUSH_PRODUCTION == '1'){
				$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			}
			else{
				$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			}

			if (!$fp)
			{
				continue;
			}
			else
			{
				
				if($token_rec_id != '')
				{
		            $msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $token_rec_id)) . pack("n",strlen($payload)) . $payload;
					fwrite($fp, $msg);
				}
			}
			fclose($fp);
		}
	}
	else
	{
		$ctx     = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $filename);
		stream_context_set_option($ctx, 'ssl', 'passphrase', IOS_PUSH_PASS_PHRASE);
		stream_context_set_option($ctx, 'ssl', 'cafile', $cer_filename);
			
		if(IS_PUSH_PRODUCTION == '1'){
			$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		}
		else{
			$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		}


		if (!$fp)
		{
			return 'false';
		}
		else
		{
			$token_rec_id =  $userdeviceToken;
			if($token_rec_id != '')
			{
		        $msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $token_rec_id)) . pack("n",strlen($payload)) . $payload;
				fwrite($fp, $msg);
			}
		}
		fclose($fp);
	}
	return true;
	// END CODE FOR PUSH NOTIFICATIONS TO ALL USERS
}

function send_notification_ios($userdeviceToken,$body = array(),$pem_file_path=IOS_PUSH_PEM_PATH,$app_is_live=IS_PUSH_LIVE)
{
	if(IS_IOS_PUSH_ON != 'true')
	{
		return true;
	}
	// Construct the notification payload
	if(!isset($body['aps']['sound']))
	{
		$body['aps']['sound'] = "default";
	}
	
	// End of Configurable Items
	$payload 	= json_encode($body);
	$ctx 	= stream_context_create();
	$filename 	= $pem_file_path;
	if(!file_exists($filename))
	{
	return true;
	}
/*	stream_context_set_option($ctx, 'ssl', 'local_cert', $filename);
	
	// assume the private key passphase was removed.
//	$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
	$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
	if (!$fp)
	{
		return "Failed to connect $err $errstr";
	}
	else
	{ */
	if(is_array($userdeviceToken))
	{
		foreach($userdeviceToken as $key => $value)
		{
			try{
				stream_context_set_option($ctx, 'ssl', 'local_cert', $filename);
				if($app_is_live == 'true')
				$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
				else
				$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
				
				if (!$fp)
				{
				//	return "Failed to connect $err $errstr";
					continue;
				}
				else
				{
					$token_rec_id = '';
					//$token_rec_id =  $value['udt_device_token'];
					$token_rec_id =  $value; 
					if($token_rec_id != null && $token_rec_id != '')
					{
						$msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $token_rec_id)) . pack("n",strlen($payload)) . $payload;
						fwrite($fp, $msg);
					}
				}
				fclose($fp);
			} catch(Exception $e) {
				return false;
			}
		}
	}
	else
	{
		try{
			stream_context_set_option($ctx, 'ssl', 'local_cert', $filename);
	
			// assume the private key passphase was removed.
			if($app_is_live == 'true')
				$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
			else
				$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
			
			if (!$fp)
			{
				return false;
			}
			else
			{
				$token_rec_id =  $userdeviceToken; 
				if($token_rec_id != null && $token_rec_id != '')
				{
					$msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $token_rec_id)) . pack("n",strlen($payload)) . $payload;
					fwrite($fp, $msg);
				}
			}
			fclose($fp);
		} catch(Exception $e) {
			return false;
		}
	}
	return true;
	// END CODE FOR PUSH NOTIFICATIONS TO ALL USERS
}


if(!function_exists('geoDistance')){	
	/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
	/*::                                                                         :*/
	/*::  This routine calculates the distance between two points (given the     :*/
	/*::  latitude/longitude of those points). It is being used to calculate     :*/
	/*::  the distance between two locations using GeoDataSource(TM) Products    :*/
	/*::                                                                         :*/
	/*::  Definitions:                                                           :*/
	/*::    South latitudes are negative, east longitudes are positive           :*/
	/*::                                                                         :*/
	/*::  Passed to function:                                                    :*/
	/*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
	/*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
	/*::    unit = the unit you desire for results                               :*/
	/*::           where: 'ML' is statute miles (default)                         :*/
	/*::                  'KM' is kilometers                                      :*/
	/*::                  'NM' is nautical miles                                  :*/
	/*::                  'MT' is meters (kishan.patel)		                                  :*/
	/*::  Worldwide cities and other features databases with latitude longitude  :*/
	/*::  are available at http://www.geodatasource.com                          :*/
	/*::                                                                         :*/
	/*::  For enquiries, please contact sales@geodatasource.com                  :*/
	/*::                                                                         :*/
	/*::  Official Web site: http://www.geodatasource.com                        :*/
	/*::                                                                         :*/
	/*::         GeoDataSource.com (C) All Rights Reserved 2015		   		     :*/
	/*::                                                                         :*/
	/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
	function geoDistance($lat1, $lon1, $lat2, $lon2, $unit) {
	
		$theta 	= $lon1 - $lon2;
		$dist 	= sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist 	= acos($dist);
		$dist 	= rad2deg($dist);
		$miles	= $dist * 60 * 1.1515;
		$unit 	= strtoupper($unit);
	
		if ($unit == "MT") {
			return ($miles * 1.609344 * 1000);
		} else if ($unit == "KM") {
			return ($miles * 1.609344);
		} else if ($unit == "NM") {
			return ($miles * 0.8684);
		} else {
			return $miles;
		}
	}
}

if(!function_exists('getDataByCurlWithGetUrl')){	
	function getDataByCurlWithGetUrl($url){
		$ch 	= curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);      
		curl_close($ch);
		return $output;
	}
}


if(!function_exists('sendRESTRequestToEjabberd')){	
	function sendRESTRequestToEjabberd ($url, $request) {
		// Create a stream context so that we can POST the REST request to $url
		$context = stream_context_create (array ('http' => array ('method' => 'POST'
												,'header' => "Host: localhost:5285\nContent-Type: text/html; charset=utf-8\nContent-Length: ".strlen($request)
												,'content' => $request)));
		// Use file_get_contents for PHP 5+ otherwise use fopen, fread, fclose
		if (version_compare(PHP_VERSION, '5.0.0', '>=')) {
			$result = file_get_contents($url, false, $context);
		} else {
			// This is the PHP4 workaround which is slightly less elegant
			// Suppress fopen warnings, otherwise they interfere with the page headers
			$fp = @fopen($url, 'r', false, $context);
			$result = '';
			// Only proceed if we have a file handle, otherwise we enter an infinite loop
			if ($fp) {
				while(!feof($fp)) {
					$result .= fread($fp, 4096);
				}
				fclose($fp);
			}
		}
		return $result;
	}
}
/********* END OF common.php ***********/
?>
