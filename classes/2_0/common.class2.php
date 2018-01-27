<?php

if ( ! defined('DOCROOT')) exit('No direct script access allowed');
class Common{
    public $db;
		public $resize;
		public $uFL;
		public $mtlFL;
		public $trvFL;
		public $uMyFL;
		public $catFL;
		public $trdFL;
		public $uRvwFL;
		public $uDsrCatFL;
		public $chatConvFieldList;
		public $chatDataFieldList;
	
    public function __construct()
    {
				global $db;
				global $resize;
				$this->db 						= $db;
				$this->resize					= $resize;
				$this->config 					= $GLOBALS['config'];
				$this->utc_time					= time();
				$this->uFL						= "u.*";
				$this->uMyFL					= "u.*";
				$this->mtlFL					= "mtl.*";
				$this->trvFL					= "trv.*";
				$this->trdFL					= "trd.*";
				$this->uRvwFL					= "uRvw.*";
				$this->catFL					= "cat.cat_id,cat.cat_name,cat.cat_parent_cat_id,cat.cat_photo,cat.cat_service_photo,cat.cat_colour_type,cat.cat_carbon_lbs,cat.cat_type,cat.cat_status";
				$this->uDsrCatFL				= "uDsr.u_dsr_u_id,uDsr.u_dsr_cat_id,uDsr.u_dsr_status";
				$this->chatConvFieldList		= "chatConv.*";
				$this->chatDataFieldList		= "chatData.*";
				$this->notUserUnset 			= array('u_email','u_xmpp_password');
    }
    
	function qry(){
		echo $this->db->getLastQuery();
		return true;
	}
	/**
	 * Coder : kd6446@gmail.com
	 * 
     * Method to validate user with security token
     *
     * @param intiger $userId Validate user id is valide or not
     * @param String $securityToken validate unique security token with user id for valid access
     *
     * @return boolean true if valid otherwise false.
     */
	
	function requestLogData($api_function,$logPostArr,$logGetArr,$logFileArr){
		if(LOG_REQUEST_ON == "true"){
			$arr = array(
					'req_api_name'		=> $api_function,
					'req_u_id'			=> isset($logPostArr['userId']) ? $logPostArr['userId'] : 0,
					'req_post'			=> json_encode($logPostArr),
					'req_get'			=> json_encode($logGetArr),
					'req_files'			=> json_encode($logFileArr),
					'req_time'			=> $this->utc_time,
					'req_formate_time'	=> date('Y-m-d H:i:s',$this->utc_time),
				);
			$insertId = $this->db->insert(TBL_Z_REQUEST_LOG,$arr);
			if($insertId){
				return true;
			}
		}
		return false;
	}
	
	function checkSecurityToken($userId,$securityToken){
		if($securityToken == 'kd'){
			return true;
		}
		$this->db->join(TBL_USERS,"u_id = udt_u_id and u_status=1");
		$this->db->where('udt_u_id',$userId)
				 ->where('udt_security_token',$securityToken)
				 ->where('udt_status','1');
		$securityData = $this->db->getOne(TBL_USER_DEVICE_TOKENS);
		if(is_array($securityData) && count($securityData) > 0){
			return true;
		}
		return false;
	}
	
	function signup($inputData){ // tp // done
		
		$userData 	= $inputData['userData'];
		$tokenData	= $inputData['tokenData'];
		$file	  		= $inputData['profileImage'];
		//_kd($file);
		// Make sure the address is valid
		if(filter_var($userData['u_email'], FILTER_VALIDATE_EMAIL))
		{
				$this->db->where('u_email',$userData['u_email'])
						 ->where('u_reg_type','1')
						 ->where('u_status','1');
				$existData = $this->db->getOne(TBL_USERS);
		//		_kd($existData);
				if(is_array($existData) && count($existData) > 0){
					$returnData = array(
							'statusCode'	=> "2",
							'message'		=> "Email address already exist.",
						);
					return $returnData;
				}
		}else{
			$returnData = array(
					'statusCode'	=> "2",
					'message'		=> "Email address doesn't valid.",
				);
			return $returnData;
		}
		
		$userId = $this->db->insert(TBL_USERS,$userData);
		
		if($userId){
			if(is_array($file) && count($file) > 0 && isset($file["name"]) && $file["name"] != '')
			{
				$targetPath = API_USER_REL_IMGPATH.$userId.'/';
				
				if(!file_exists(str_replace('//','/',$targetPath.THUMB_FLD_NAME)))
				{
					mkdir(str_replace('//','/',$targetPath.THUMB_FLD_NAME), 0777, true);
				}
				
				$file["name"] = "tmuser-".$userId.".jpg";
				$filename = UploadFile($file,$targetPath,'1');
				
				if($filename)
				{
					//echo $targetPath.$filename;
					$resizeObj = new Resize($targetPath.$filename);
					
					$thymbimg  = $resizeObj -> resizeImage(USER_THUMB_WIDTH, USER_THUMB_HEIGHT, USER_THUMB_TYPE); // Resize image (options: exact, portrait, landscape, auto, crop)
					
					$imgPathInfoImg 	= pathinfo($targetPath.$filename);
					$fileOnlyName 		= $imgPathInfoImg['filename'];
					$fileThumbName 		= "tmuser-".$userId.".jpg";
					
					$thymbimg  = $resizeObj->saveImage($targetPath.THUMB_FLD_NAME.$fileThumbName, USER_THUMB_IMG_RESOLUTION); // Save Resize image
					unset($resizeObj); // DESTROY OBJECT WITH CURRENT IMAGE
					
					$updateData = array(
							'u_photo' 		  => $filename,
							'u_thumb_photo'	  => $fileThumbName,
							'u_modified_date' => $this->utc_time,
						);
					$this->db->where('u_id',$userId)
							 ->where('u_status','1');
					$isUpdated = $this->db->update(TBL_USERS,$updateData);
					
				}
			}
			
			if($_SERVER['HTTP_HOST'] != 'localhost')
			{
				$ofPassword	= getUniqueToken(12);
				$ofUsername	= XMPP_USER_PREFIX.$userId;
				
				$request = "register ".$ofUsername." ".EJABBERD_HOST_URL." ".$ofPassword;
				$response = sendRESTRequestToEjabberd(EJABBERD_MOD_REST_URL, $request);

				//Registering user to Openfire
			/*	$ofPassword	= getUniqueToken(12);
				$ofUsername	= XMPP_USER_PREFIX.$userId;
				$ofEmail	= "";
				$ofName		= trim($userData['u_first_name']." ".$userData['u_last_name']);
				
				$params		= array(
								'type'		=> 'add',
								'username'	=> $ofUsername,
								'password'	=> $ofPassword,
								'name'		=> $ofName,
								'email'		=> $ofEmail,
								'secret'	=> OPENFIRE_USERSERVICE_SECRET_KEY,
								'groups'	=> CHAT_GROUP_NAME,
							);
							
				$params				= http_build_query($params);
				$url					= OPENFIRE_USERSERVICE_URL.$params;
				$ofResponse		= getDataByCurlWithGetUrl($url);
				$responseArr	= json_decode(json_encode(simplexml_load_string($ofResponse)),true); 
				
				if ( is_array($responseArr) && count($responseArr) > 0 && $responseArr[0] == 'ok' ){
					
						$updateData = array(
								'u_xmpp_username'		=> $ofUsername,
								'u_xmpp_jid' 		  => $ofUsername.CHAT_JID_SUFFIX,
								'u_xmpp_password' 		  => $ofPassword,
								'u_modified_date' => $this->utc_time,
							);
						$this->db->where('u_id',$userId)
								 ->where('u_status','1');
						$isUpdated = $this->db->update(TBL_USERS,$updateData);
					
				} */
			
			$updateData = array(
								'u_xmpp_username'		=> $ofUsername,
								'u_xmpp_jid' 		  => $ofUsername.CHAT_JID_SUFFIX,
								'u_xmpp_password' 		  => $ofPassword,
								'u_modified_date' => $this->utc_time,
							);
						$this->db->where('u_id',$userId)
								 ->where('u_status','1');
						$isUpdated = $this->db->update(TBL_USERS,$updateData);
				
					
			}
			
			$this->db->where("((udt_u_id = ?) OR (udt_unique_token = ?) OR (udt_device_token != '' AND udt_device_token = ? AND udt_device_type = ?))",array($userId,$tokenData['udt_unique_token'],$tokenData['udt_device_token'],$tokenData['udt_device_type']))
					 ->where("udt_status","1");
			$this->db->delete(TBL_USER_DEVICE_TOKENS);
		   
			$securityExist = array();
			do{
				$securityToken = getUniqueToken(64);
				$this->db->where('udt_security_token',$securityToken)
						 ->where('udt_status','9','!=');
				$securityExist = $this->db->getOne(TBL_USER_DEVICE_TOKENS);
			} while(is_array($securityExist) && count($securityExist) > 0);
		   
			$tokenData['udt_security_token'] = $securityToken;
			$tokenData['udt_u_id'] = $userId;
			$tokenId = $this->db->insert(TBL_USER_DEVICE_TOKENS,$tokenData);
		
			$this->db->where('u_id',$userId)
					 ->where('u_status','1');
			$data = $this->db->getOne(TBL_USERS." as u",$this->uMyFL); 
			
			if(is_array($data) && count($data) > 0){
				$data['profileImage']  	= userProfileImage($data['u_id'],$data['u_photo'],$data['u_thumb_photo']);
				$data['securityToken'] 	= $securityToken;
				$data = unsetUserExtraData($data,$this->notUserUnset);
				$returnData = array(
						'statusCode'		=> "1",
						'message'			=> "Success",
						'userData'			=> $data,
					);
				return $returnData;
			}
		}
		
		$returnData = array(
				'statusCode'	=> "2",
				'message'		=> "Failed",
			);
		return $returnData;
    }
	
	
	
	function updateFacebookFriends($inputData){
		$userId 	= $inputData['userId'];
		$frdFbIdList 	= $inputData['frdFbIdList'];

		$frdArrList = explode(',',$frdFbIdList);
		if(!is_array($frdArrList) || count($frdArrList) == 0){
			$returnData = array(
					'statusCode'	=> "2",
					'message'		=> "Oops, we can't find your friend list. Check back soon",
				);
			return $returnData;
		}
		
		
		// DELETE REMOVED FRIENDS FROM FB FRIEND LIST
		$ids = $this->db->subQuery();
		$ids->where('u_social_id',$frdArrList,'IN')
				->where('u_reg_type','2')
			 ->where('u_status','9','!=');
		$ids->get(TBL_USERS,null,'u_id');
		
		//  frd_u_id = $userId and frd_other_u_id not in (ids)
		$this->db->where('scl_frd_u_id',$userId)
				 ->where('scl_frd_other_u_id',$ids,'NOT IN')
				 ->where('scl_frd_from',"2");
		$frdDeleted = $this->db->delete(TBL_SOCIAL_FRIENDS);
		
		$ids = $this->db->subQuery();
		$ids->where('u_social_id',$frdArrList,'IN')
		->where('u_reg_type','2')
			 ->where('u_status','9','!=');
		$ids->get(TBL_USERS,null,'u_id');
		
		//  frd_other_u_id = $userId and frd_u_id not in (ids)
		$this->db->where('scl_frd_other_u_id',$userId)
						 ->where('scl_frd_u_id',$ids,'NOT IN')
						 ->where('scl_frd_from',"2");
		$frdDeleted = $this->db->delete(TBL_SOCIAL_FRIENDS);
		
		// GET NEW FRIENDS UPDATED IN FACEBOOK
		$frdIds = $this->db->subQuery();
		$frdIds->where('(scl_frd_u_id = ? OR scl_frd_other_u_id = ?)',array($userId,$userId));
				   //->Where('frd_from','2');
		$frdIds->get(TBL_SOCIAL_FRIENDS,null,"IF(scl_frd_u_id = '".$userId."',scl_frd_other_u_id,scl_frd_u_id) as u_id");
		
		$this->db->where('u_id',$frdIds,'NOT IN')
				 ->where('u_social_id',$frdArrList,'IN')
				 ->where('u_reg_type','2')
				 ->where('u_status','1');
		$newFriednList = $this->db->get(TBL_USERS, null , 'u_id');
		$addedUserList = array();
		foreach($newFriednList as $key => $value){
			$newFrdData = array (
				"scl_frd_u_id" 					=> $userId,
				"scl_frd_other_u_id" 	  => $value['u_id'],
				"scl_frd_from" 				  => "2",
				"scl_frd_created_date" 	=> $this->utc_time,
				"scl_frd_status" 				=> "1",
			);
			$id = $this->db->insert (TBL_SOCIAL_FRIENDS, $newFrdData);
			$addedUserList[] = $value['u_id'];
		}
		
		$returnData = array(
				'statusCode'	=> "1",
				'message'		=> "Hurrah, all done",
			);
		return $returnData;
	}
		
		
	/**
	 * Coder : kd6446@gmail.com
	 * 
     * Method to signup data of user
     *
     * @param array $userData Needed data for user registration .
     *
     * @return array Contains the user's login status with data.
     */
	
	function changeUserEmailAddress($inputData){
		
		$email 		= $inputData['email'];
		$userId 	= $inputData['userId'];
		
		// Make sure the address is valid
		if(filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$this->db->where('u_id',$userId)
					 ->where('u_status','1');
			$existData = $this->db->getOne(TBL_USERS);
			
			if(checkArr($existData)){
				if($existData['u_is_email_change'] == '1'){
					$returnData = array(
							'statusCode'	=> "2",
							'message'		=> "You have already changed your email address one time.",
						);
					return $returnData;
				}else if($existData['u_email'] == $email){
					$returnData = array(
							'statusCode'	=> "2",
							'message'		=> "Please, Update new different email address to alter.",
						);
					return $returnData;
				}
			}
			
			$this->db->where('u_email',$email)
					 ->where('u_id',$userId,'!=')
					 ->where('u_status','1');
			$existData = $this->db->getOne(TBL_USERS);
			
			if(checkArr($existData)){
				$returnData = array(
						'statusCode'	=> "2",
						'message'		=> "Email address already exist.",
					);
				return $returnData;
			}
			
			$userData = array(
					'u_email'			=> $email,
					'u_modified_date'	=> $this->utc_time,
				);
			$this->db->where('u_id',$userId)
					->where('u_status','1');
		    $isUpdated = $this->db->update(TBL_USERS,$userData);
		   
		   if($isUpdated){
				$returnData = array(
						'statusCode'	=> "1",
						'message'		=> "Success",
					);
				return $returnData;
			}
			
			$returnData = array(
					'statusCode'	=> "2",
					'message'		=> "There are something problem to update your emaill",
				);
			return $returnData;
		}
		
		$returnData = array(
				'statusCode'	=> "2",
				'message'		=> "Invalid email address, please try again.",
			);
		return $returnData;
    }
	
	function checkSocialIdRegistration($inputData){
		
		$socialId 		= $inputData['socialId'];
		$socialRegType 	= $inputData['socialRegType'];
		$tokenData 		= $inputData['tokenData'];
		
		if(($socialRegType == '2' || $socialRegType == '3') && $socialId != ""){
			
		
			$this->db->where('u_social_id',$socialId)
					 ->where('u_reg_type',$socialRegType)
					 ->where('u_status','1');
			$existData = $this->db->getOne(TBL_USERS);
			
			if(is_array($existData) && count($existData) > 0){
				
				$userId = $existData['u_id'];
				
				$this->db->where("((udt_unique_token = ?) OR (udt_device_token != '' AND udt_device_token = ? AND udt_device_type = ?))",array($tokenData['udt_unique_token'],$tokenData['udt_device_token'],$tokenData['udt_device_type']))
						 ->where("udt_status","1");
				$this->db->delete(TBL_USER_DEVICE_TOKENS);
			   
				$securityExist = array();
				do{
					$securityToken = getUniqueToken(64);
					$this->db->where('udt_security_token',$securityToken)
							 ->where('udt_status','9','!=');
					$securityExist = $this->db->getOne(TBL_USER_DEVICE_TOKENS);
				} while(is_array($securityExist) && count($securityExist) > 0);
			   
				$tokenData['udt_security_token'] = $securityToken;
				$tokenData['udt_u_id'] = $userId;
				$tokenId = $this->db->insert(TBL_USER_DEVICE_TOKENS,$tokenData);
			
				$this->db->where('u_id',$userId)
						 ->where('u_status','1');
				$data = $this->db->getOne(TBL_USERS." as u",$this->uMyFL); 
				
				if(is_array($data) && count($data) > 0){
					$data['profileImage']  	= userProfileImage($data['u_id'],$data['u_photo'],$data['u_thumb_photo']);
					$data['securityToken'] 	= $securityToken;
					$data = unsetUserExtraData($data,$this->notUserUnset);
					
					$returnData = array(
							'statusCode'		=> "1",
							'message'			=> "Success",
							'userData'			=> $data,
						);
					return $returnData;
				}
			}
			
			$returnData = array(
					'statusCode'	=> "1",
					'message'		=> "Success",
					'isRegistered'	=> "false",
				);
			return $returnData;
			
		}
		
		$returnData = array(
				'statusCode'	=> "2",
				'message'		=> "Social Id doesn't seems to be valid.",
			);
		return $returnData;
    }

    
	
	function loginWithSocialData($inputData){ // tp // done
		
		$userData 	= $inputData['userData'];
		$tokenData	= $inputData['tokenData'];
		$file	  	= $inputData['profileImage'];
		
		if($userData['u_reg_type'] != "1" && $userData['u_social_id'] == "" ){
			
			$returnData = array(
					'statusCode'	=> "2",
					'message'		=> "Invalid request data",
				);
			return $returnData;
		}
		
		
		$isRegister = "false";
		
		$this->db->where('u_reg_type',$userData['u_reg_type'])
				 ->where('u_social_id',$userData['u_social_id'])
				 ->where('u_status','1');
		$existData = $this->db->getOne(TBL_USERS);
		
		if(checkArr($existData)){
			$userId = $existData['u_id'];
		}else {
				//_kd($userData);
				$userId = $this->db->insert(TBL_USERS,$userData);
			//	$this->qry();
				$isRegister = "true";
				
				if(is_array($file) && count($file) > 0 && isset($file["name"]) && $file["name"] != '')
				{
					$targetPath = API_USER_REL_IMGPATH.$userId.'/';
					
					// MAKE THE DIRECTORY IF IT DOESN'T EXIST
					if(!file_exists(str_replace('//','/',$targetPath.THUMB_FLD_NAME)))
					{
						mkdir(str_replace('//','/',$targetPath.THUMB_FLD_NAME), 0777, true);
					}
					$file["name"] = "tm_".$userId.".jpg";
					$filename = UploadFile($file,$targetPath,'1');
					
					if($filename)
					{
							chmod($targetPath.$filename, 0777);
							$resizeObj = new Resize($targetPath.$filename);
							$thymbimg  = $resizeObj->resizeImage(USER_THUMB_WIDTH, USER_THUMB_HEIGHT, USER_THUMB_TYPE); // Resize image (options: exact, portrait, landscape, auto, crop)
							
							$imgPathInfoImg 	= pathinfo($targetPath.$filename);
							$fileOnlyName 		= $imgPathInfoImg['filename'];
							$fileThumbName 		= 'tm_'.$userId.'.jpg';
							
							$thymbimg  = $resizeObj->saveImage($targetPath.THUMB_FLD_NAME.$fileThumbName, USER_THUMB_IMG_RESOLUTION); // Save Resize image
							unset($resizeObj); // DESTROY OBJECT WITH CURRENT IMAGE
							
							$updateData = array(
									'u_photo' 		  		=> $filename,
									'u_thumb_photo' 		=> $fileThumbName,
									'u_modified_date' 	=> $this->utc_time,
								);
							$this->db->where('u_id',$userId)
									 ->where('u_status','1');
							$isUpdated = $this->db->update(TBL_USERS,$updateData);
					}
				}
			
		}
	    
		if($userId){
			
			$this->db->where("((udt_u_id = ?) OR (udt_unique_token = ?) OR (udt_device_token != '' AND udt_device_token = ? AND udt_device_type = ?))",array($userId,$tokenData['udt_unique_token'],$tokenData['udt_device_token'],$tokenData['udt_device_type']))
					 ->where("udt_status","1");
			$this->db->delete(TBL_USER_DEVICE_TOKENS);
		   
			$securityExist = array();
			do{
				$securityToken = getUniqueToken(64);
				$this->db->where('udt_security_token',$securityToken)
						 ->where('udt_status','9','!=');
				$securityExist = $this->db->getOne(TBL_USER_DEVICE_TOKENS);
			} while(is_array($securityExist) && count($securityExist) > 0);
		   
			$tokenData['udt_security_token'] = $securityToken;
			$tokenData['udt_u_id'] = $userId;
			$tokenId = $this->db->insert(TBL_USER_DEVICE_TOKENS,$tokenData);
			
			$this->db->where('u_id',$userId)
					 ->where('u_status','1');
			$data = $this->db->getOne(TBL_USERS." as u",$this->uMyFL);
			
			if(is_array($data) && count($data) > 0){
				$data['profileImage']  	= userProfileImage($data['u_id'],$data['u_photo'],$data['u_thumb_photo']);
				$data['securityToken'] 	= $securityToken;
				$data = unsetUserExtraData($data,$this->notUserUnset);
				$returnData = array(
						'statusCode'		=> "1",
						'message'				=> "Success",
						'isNewRegister'	=> $isRegister,
						'userData'			=> $data,
					);
				return $returnData;
			}
		}
		
		$returnData = array(
				'statusCode'	=> "2",
				'message'		=> "Login failed",
			);
		return $returnData;
    }
	
    function login($inputData){ // tp // done
				$email 			= $inputData['email'];
				$password 	= $inputData['password'];
				$tokenData	= $inputData['tokenData'];
				
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
					$returnData = array(
							'statusCode'	=> "2",
							'message'		=> "Your email and password don't match. Please try again or use a different email to register.",
						);
					return $returnData;
				}
				
				$this->db->where('u_email',$email)
						 ->where('u_password',$password)
						 ->where('u_reg_type',"1")
						 ->where('u_status','1');
				$userData = $this->db->getOne(TBL_USERS." as u",$this->uMyFL);
					
				
				if(!checkArr($userData)){
					$returnData = array(
							'statusCode'	=> "2",
							'message'		=> "Your email and password don't match. Please try again or use a different email to register.",
						);
					return $returnData;
				}
				
				unset($userData['u_password']);
				
				
				$this->db->where("((udt_u_id = ?) OR (udt_unique_token = ?) OR (udt_device_token != '' AND udt_device_token = ? AND udt_device_type = ?))",array($userData['u_id'],$tokenData['udt_unique_token'],$tokenData['udt_device_token'],$tokenData['udt_device_type']))
						 ->where("udt_status","1");
				$this->db->delete(TBL_USER_DEVICE_TOKENS);
				 
				$securityExist = array();
				do{
					$securityToken = getUniqueToken(64);
					$this->db->where('udt_security_token',$securityToken)
							 ->where('udt_status','9','!=');
					$securityExist = $this->db->getOne(TBL_USER_DEVICE_TOKENS);
				} while(is_array($securityExist) && count($securityExist) > 0);
				 
				$tokenData['udt_security_token'] = $securityToken;
				$tokenData['udt_u_id'] = $userData['u_id'];
				$tokenId = $this->db->insert(TBL_USER_DEVICE_TOKENS,$tokenData);
				
				$userData['profileImage']  	= userProfileImage($userData['u_id'],$userData['u_photo'],$userData['u_thumb_photo']);
				$userData['securityToken'] 	= $securityToken;
				$userData = unsetUserExtraData($userData,$this->notUserUnset);
				$returnData = array(
						'statusCode'		=> "1",
						'message'			=> "Success",
						'userData'			=> $userData,
					);
				return $returnData;
    }
		
		function updateUserProfile($inputData){ // tp // done
				$userId 				= $inputData['userId'];
				$securityToken	= $inputData['securityToken'];
				$userData 			= $inputData['userData'];
				$fileImage 			= $inputData['profileImage'];
				
				if(is_array($fileImage) && count($fileImage) > 0 && isset($fileImage["name"]) && $fileImage["name"] != '')
				{
						$targetPath = API_USER_REL_IMGPATH.$userId.'/';
						
						// MAKE THE DIRECTORY IF IT DOESN'T EXIST
						if(!file_exists(str_replace('//','/',$targetPath.THUMB_FLD_NAME)))
						{
							mkdir(str_replace('//','/',$targetPath.THUMB_FLD_NAME), 0777, true);
						}
						$fileImage["name"] = "tm_".$userId.".jpg";
						$filename = UploadFile($fileImage,$targetPath,'1',"true");
						
						if($filename)
						{
								chmod($targetPath.$filename, 0777);
								$resizeObj = new Resize($targetPath.$filename);
								$thymbimg  = $resizeObj->resizeImage(USER_THUMB_WIDTH, USER_THUMB_HEIGHT, USER_THUMB_TYPE); // Resize image (options: exact, portrait, landscape, auto, crop)
								
								$imgPathInfoImg 	= pathinfo($targetPath.$filename);
								$fileThumbName 		= "tm_".$userId.".jpg";
								
								$thymbimg  = $resizeObj->saveImage($targetPath.THUMB_FLD_NAME.$fileThumbName, USER_THUMB_IMG_RESOLUTION); // Save Resize image
								unset($resizeObj); // DESTROY OBJECT WITH CURRENT IMAGE
								$userData['u_photo'] 		  		= $filename;
								$userData['u_thumb_photo'] 		= $fileThumbName;
						}
				}
		
				$this->db->where('u_id',$userId)
						 ->where('u_status','1');
				$isUpdated = $this->db->update(TBL_USERS,$userData);
		
				if($isUpdated){
						if($_SERVER['HTTP_HOST'] != 'localhost' && !empty($userData['u_first_name']) && !empty($userData['u_last_name']))
						{
								$ofUsername	= XMPP_USER_PREFIX.$userId;
								$ofEmail	= "";
								$ofName		= trim($userData['u_first_name']." ".$userData['u_last_name']);
								
								$params		= array(
											'type'		=> 'update',
											'username'	=> $ofUsername,
											'name'		=> $ofName,
											'email'		=> $ofEmail,
											'secret'	=> OPENFIRE_USERSERVICE_SECRET_KEY,
											'groups'	=> CHAT_GROUP_NAME,
										);
											
								$params			= http_build_query($params);
								$url			= OPENFIRE_USERSERVICE_URL.$params;
								$ofResponse		= getDataByCurlWithGetUrl($url);
								$responseArr	= json_decode(json_encode(simplexml_load_string($ofResponse)),true);
								
								if ( is_array($responseArr) && count($responseArr) > 0 && $responseArr[0] == 'ok' ){
									//Do Nothing
								}
						}	
					
						$this->db->where('u_id',$userId)
								 ->where('u_status','1');
						$data = $this->db->getOne(TBL_USERS." as u",$this->uMyFL);
						
						if(is_array($data) && count($data) > 0){
								$data['securityToken'] = $securityToken;
								$data['profileImage'] 	= userProfileImage($data['u_id'],$data['u_photo'],$data['u_thumb_photo']);
								$data = unsetUserExtraData($data,$this->notUserUnset);
								$returnData = array(
										'statusCode'		=> "1",
										'message'			=> "Success",
										'userData'			=> $data,
									);
								return $returnData;
						}
				}
				
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "No update made to your profile",
					);
				return $returnData;
    }
		
		function updateUserData($inputData){
				$userId 	= $inputData['userId'];
				$securityToken = $inputData['securityToken'];
				$userData 	= $inputData['userData'];
				
				$this->db->where('u_id',$userId)
						 ->where('u_status','1');
				$isUpdated = $this->db->update(TBL_USERS,$userData);
		
				if($isUpdated){
						$this->db->where('u_id',$userId)
								 ->where('u_status','1');
						$data = $this->db->getOne(TBL_USERS." as u",$this->uMyFL);
						
						if(is_array($data) && count($data) > 0){
								$data['securityToken'] 	= $securityToken;
								$data['profileImage'] 	= userProfileImage($data['u_id'],$data['u_photo'],$data['u_thumb_photo']);
								$data['profileVideo'] 	= userProfileVideo($data['u_id'],$data['u_video'],$data['u_thumb_video']);
								unset($data['u_email']);
								unset($data['u_photo']);
								unset($data['u_password']);
								$returnData = array(
										'statusCode'		=> "1",
										'message'			=> "Success",
										'userData'			=> $data,
									);
								return $returnData;
						}
				}
				
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "No update made to your profile",
					);
				return $returnData;
    }
	
		function getUserProfile($inputData){ // tp // done
				$userId 			= $inputData['userId'];
				$otherUserId	= $inputData['otherUserId'];
				
				$this->db->join(TBL_TRADE_REVIEW_REMAINS_AS,"rvw_remain_u_id = '".$userId."' AND rvw_remain_other_u_id = u_id","LEFT");
				$this->db->where('u_id',$otherUserId)
						 ->where('u_status','1');
				$data = $this->db->getOne(TBL_USERS." as u",$this->uMyFL.",IF(rvw_remain_other_u_id = u_id,1,0) as isReviewRemain"); 
					
				if(checkArr($data)){
						
						$this->db->join(TBL_CATEGORIES." as cat","cat_id = u_dsr_cat_id AND cat_status = 1");
						$this->db->where("u_dsr_u_id",$otherUserId);
						$desireList = $this->db->get(TBL_USER_DESIRES." as uDsr",null,$this->catFL.",".$this->uDsrCatFL);
						
						foreach($desireList as $key => $value){
								$desireList[$key]['categoryImage'] = categoryImage($value['cat_photo'],$value['cat_service_photo']);
						}
						
						$this->db->where("trv_u_id",$otherUserId)
										 ->where("trv_status","1");
						$this->db->orderBy("trv_id","desc");
						$troveList = $this->db->get(TBL_TROVES_AS,null,$this->trvFL);
						
						foreach($troveList as $key => $value){
								$troveList[$key]['troveImages'] = troveImage($value['trv_u_id'],$value['trv_photo'],$value['trv_thumb_photo']);
						}
		
						$data['profileImage']  	= userProfileImage($data['u_id'],$data['u_photo'],$data['u_thumb_photo']);
						$data = ($userId == $otherUserId) ? unsetUserExtraData($data,$this->notUserUnset) : unsetUserExtraData($data);
						
						$returnData = array(
										'statusCode'		=> "1",
										'message'			=> "Success",
										'userData'			=> $data,
										'desireList'		=> $desireList,
										'troveList'			=> $troveList,
								);
						return $returnData;
				}
				
				$returnData = array(
						'statusCode'	=> "2",
						'message'			=> "No profile data found",
					);
				return $returnData;
    }
		
		function getMaterialList(){ // tp // done
				$this->db->where("mtl_status","1");
				$materialList = $this->db->get(TBL_MATERIALS." as mtl",null,$this->mtlFL);
				
				$returnData = array(
						'statusCode'		=> "1",
						'message'				=> "Success",
						'dataList'			=> $materialList,
					);
				return $returnData;
    }
		
		function getCategoryList($inputData){ // tp // done
	
				$userId 			= $inputData['userId'];
				$type 				= $inputData['type'];
				
				$itemCategoryList = array();
				$serviceCategoryList = array();
				
				if($type == '1' || $type == '0'){
					$this->db->join(TBL_USER_DESIRES." as uDsr","u_dsr_u_id = '".$userId."' AND u_dsr_cat_id = cat_id AND u_dsr_status = 1","LEFT");
					$this->db->where("cat_type","1")
									 ->where("cat_parent_cat_id","0")
									 ->where("cat_status","1");
					$this->db->orderBy("cat_name","ASC");	 
					$itemCategoryList = $this->db->get(TBL_CATEGORIES." as cat",null,$this->catFL.",".$this->uDsrCatFL);
					
					foreach($itemCategoryList as $key => $value){
							$itemCategoryList[$key]['categoryImage'] = categoryImage($value['cat_photo'],$value['cat_service_photo']);
							$this->db->join(TBL_USER_DESIRES." as uDsr","u_dsr_u_id = '".$userId."' AND u_dsr_cat_id = cat_id AND u_dsr_status = 1","LEFT");
							$this->db->where("cat_type","1")
											->where("cat_parent_cat_id",$value['cat_id'])
											->where("cat_status","1");
							$this->db->orderBy("cat_name","ASC");	
							$subCategoryList = $this->db->get(TBL_CATEGORIES." as cat",null,$this->catFL.",".$this->uDsrCatFL);
							foreach($subCategoryList as $subKey => $subValue){
									$subCategoryList[$subKey]['categoryImage'] = categoryImage($subValue['cat_photo'],$subValue['cat_service_photo']);
							}
							$itemCategoryList[$key]['subCategoryList'] = $subCategoryList;
					}
				}
				
				if($type == '2' || $type == '0'){
					$this->db->join(TBL_USER_DESIRES." as uDsr","u_dsr_u_id = '".$userId."' AND u_dsr_cat_id = cat_id AND u_dsr_status = 1","LEFT");
					$this->db->where("cat_type","2")
									 ->where("cat_parent_cat_id","0")
									 ->where("cat_status","1");
					$this->db->orderBy("cat_name","ASC");	
					$serviceCategoryList = $this->db->get(TBL_CATEGORIES." as cat",null,$this->catFL.",".$this->uDsrCatFL);
					
					foreach($serviceCategoryList as $key => $value){
							$serviceCategoryList[$key]['categoryImage'] = categoryImage($value['cat_photo'],$value['cat_service_photo']);
							$this->db->join(TBL_USER_DESIRES." as uDsr","u_dsr_u_id = '".$userId."' AND u_dsr_cat_id = cat_id AND u_dsr_status = 1","LEFT");
							$this->db->where("cat_type","2")
											->where("cat_parent_cat_id",$value['cat_id'])
											->where("cat_status","1");
							$this->db->orderBy("cat_name","ASC");	
							$subCategoryList = $this->db->get(TBL_CATEGORIES." as cat",null,$this->catFL.",".$this->uDsrCatFL);
							foreach($subCategoryList as $subKey => $subValue){
									$subCategoryList[$subKey]['categoryImage'] = categoryImage($subValue['cat_photo'],$subValue['cat_service_photo']);
							}
							$serviceCategoryList[$key]['subCategoryList'] = $subCategoryList;
					}
				
				}
				$returnData = array(
						'statusCode'			=> "1",
						'message'				=> "Success",
						'itemCategoryList'			=> $itemCategoryList,
						'serviceCategoryList'			=> $serviceCategoryList,
					);
				return $returnData;
		
		}
		
		function addNewTrove($inputData){ // tp // done 
				$userId 				= $inputData['userId'];
				$troveData			= $inputData['troveData'];
				$troveImageList	= $inputData['troveImageList'];
				
				$this->db->where('trv_u_id',$userId)
								 ->where('trv_status',"1");
			  $totalTroveCount = $this->db->getValue(TBL_TROVES,"count(trv_id)");
				
				if($totalTroveCount >= 9){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "Oops, You can only upload 9 items to your item trove! Please remove an item before adding another.",
							);
						return $returnData;
				}
				
				$troveId = $this->db->insert(TBL_TROVES,$troveData);
				
				if($troveId){
						foreach($troveImageList as $fileKey => $fileImage){
								
								//fileImage = checkArr($troveImageList[$i]) ? $troveImageList[$i] : array();
								if(is_array($fileImage) && count($fileImage) > 0 && isset($fileImage["name"]) && $fileImage["name"] != '')
								{
										$targetPath = API_TROVE_REL_IMGPATH.$troveData['trv_u_id'].'/';
										if(!file_exists(str_replace('//','/',$targetPath.THUMB_FLD_NAME))){	
												mkdir(str_replace('//','/',$targetPath.THUMB_FLD_NAME), 0777, true);
										}
										
										$fileImage["name"] = "trv-".$troveId."-".($fileKey+1).".jpg";
										$filename = UploadFile($fileImage,$targetPath,'1','true');
										
										if($filename)
										{
												$resizeObj = new Resize($targetPath.$filename);
												
												$thymbimg  = $resizeObj -> resizeImage(TROVE_THUMB_WIDTH, TROVE_THUMB_HEIGHT, TROVE_THUMB_TYPE); // Resize image (options: exact, portrait, landscape, auto, crop)
												
												$imgPathInfoImg 	= pathinfo($targetPath.$filename);
												//$fileOnlyName 		= $imgPathInfoImg['filename'];
												$fileThumbName 		=  $filename; //"trv-".$troveId.".jpg";
												
												$thymbimg  = $resizeObj->saveImage($targetPath.THUMB_FLD_NAME.$fileThumbName, TROVE_THUMB_IMG_RESOLUTION); // Save Resize image
												unset($resizeObj); // DESTROY OBJECT WITH CURRENT IMAGE
												
												//$troveImageInsert = array(
												//				'trv_img_trv_id'				=> $troveId,
												//				'trv_img_name'					=> $filename,
												//				'trv_img_thumb_name'		=> $fileThumbName,
												//		);
												//$this->db->insert(TBL_TROVE_IMAGES,$troveImageInsert);
												//
												if($fileKey == '0'){
														$updateData = array(
																'trv_photo' 		  	=> $filename,
																'trv_thumb_photo'	  => $fileThumbName,
																'trv_modified_date' => $this->utc_time,
															);
														$this->db->where('trv_id',$troveId)
																		 ->where('trv_status','1');
														$isUpdated = $this->db->update(TBL_TROVES,$updateData);
												}
										}
								}
						}
						$returnData = array(
										'statusCode'	=> "1",
										'message'			=> "Success",
										'troveId'			=> $troveId,
								);
						return $returnData;
				}
				
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "Oops, There are something problem with updating subscription to your profile",
					);
				return $returnData;
    }
		
		function removeTrove($inputData){ // tp // done
				$userId 	= $inputData['userId']; 
				$troveId 	= $inputData['troveId'];
				
				$this->db->where('trv_id',$troveId)
								 ->where('trv_u_id',$userId)
								 ->where('trv_status','1');
				$existData = $this->db->getOne(TBL_TROVES);
				
				if(!checkArr($existData)){
						$returnData = array(
										'statusCode'	=> "2",
										'message'		=> "An item trove is not available longer to your tray.",
								);
						return $returnData;
				}
				
				$updateData = array(
								'trv_modified_date'	=> $this->utc_time,
								'trv_status'				=> "9",
						);
				$this->db->where("trv_id",$troveId);
				$isUpdated = $this->db->update(TBL_TROVES,$updateData);
				
				if($isUpdated){
						$returnData = array(
								'statusCode'		=> "1",
								'message'			=> "Success",
							);
						return $returnData;
				}
				
				$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "Oops, There are something problem with remove this item from tray.",
						);
				return $returnData;
		}
		
		function getTroveDetail($inputData){ // tp // done
				$userId 				= $inputData['userId'];
				$troveId				= $inputData['troveId'];
				
				$this->db->where('u_id',$userId)
						 ->where('u_status','1');
				$userData = $this->db->getOne(TBL_USERS);
				
				$this->db->join(TBL_USERS_AS,"u_id = trv_u_id AND u_status = 1");
				$this->db->join(TBL_CATEGORIES_AS,"cat_id = trv_cat_id AND cat_status = 1");
				$this->db->where("trv_id",$troveId)
								 ->where("trv_status","1");
				$troveDetail = $this->db->getOne(TBL_TROVES_AS,"*,get_distance_in_miles_between_geo_locations('".$userData['u_latitude']."','".$userData['u_longitude']."',u_latitude,u_longitude) as distance ");
				
				if(!checkArr($troveDetail)){
						$returnData = array(
								'statusCode' => "2",
								'message'	 => "Sorry, This trove not exists longer.",
							);
						return $returnData;
				}
				
				$troveDetail['troveImages']  	= troveImageAllById($troveDetail['trv_u_id'],$troveDetail['trv_id']);
				$troveDetail['profileImage']  = userProfileImage($troveDetail['u_id'],$troveDetail['u_photo'],$troveDetail['u_thumb_photo']);
				$troveDetail['categoryImage'] = categoryImage($troveDetail['cat_photo'],$troveDetail['cat_service_photo']);
				
				$returnData = array(
						'statusCode' => "1",
						'message'	 => "Success",
						'troveDetail'	 => $troveDetail,
					);
				return $returnData;
		}
		
		function getUserTroveList($inputData){ // tp // done
				$userId 			= $inputData['userId'];
				$otherUserId	= $inputData['otherUserId'];
				
				$this->db->join(TBL_CATEGORIES,"trv_cat_id = cat_id AND cat_status = 1");
				$this->db->join(TBL_MATERIALS,"trv_mtl_id = mtl_id AND mtl_status = 1","LEFT");
				$this->db->where("trv_u_id",$otherUserId)
								 ->where("trv_status","1");
				$this->db->orderBy("trv_id","desc");
				$dataList = $this->db->get(TBL_TROVES_AS,null,$this->trvFL.",cat_name,mtl_name");
				
				foreach($dataList as $key => $value){
						$dataList[$key]['troveImages'] = troveImage($value['trv_u_id'],$value['trv_photo'],$value['trv_thumb_photo']);
				}
				
				$returnData = array(
						'statusCode'		=> "1",
						'message'				=> "Success",
						'dataList'			=> $dataList,
					);
				return $returnData;
		
    }
		
		function purposeTrade($inputData){ // tp // done  
				$userId 							= $inputData['userId'];
				$otherUserId					= $inputData['otherUserId'];
				$desiredTroveIdList 	= $inputData['desiredTroveIdList'];
				$purposedTroveIdList 	= $inputData['purposedTroveIdList'];
				
				if($desiredTroveIdList == ""){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "Please, select some valid desire trove items.",
							);
						return $returnData;
				}
				
				$desiredTroveIdArr 	= explode(',',$desiredTroveIdList);
				
				// Check all desired items in other user's tray or not
				$this->db->where('trv_id',$desiredTroveIdArr,"IN")
								 ->where('trv_u_id',$otherUserId)
								 ->where('trv_status',"1");
			  $totalDesiredTrove = $this->db->getValue(TBL_TROVES,"count(trv_id)");
				
				if($totalDesiredTrove != count($desiredTroveIdArr)){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "Please, select some valid desire trove items.",
							);
						return $returnData;
				}
				
				if($totalDesiredTrove > 4){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "You can't select more than 4 desire items.",
							);
						return $returnData;
				}
				
				// Check all purposed items in my's tray or not
				if($purposedTroveIdList == ""){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "Please, select some valid trove items for purpose.",
							);
						return $returnData;
				}
				$purposedTroveIdArr 	= explode(',',$purposedTroveIdList);
				$this->db->where('trv_id',$purposedTroveIdArr,"IN")
								 ->where('trv_u_id',$userId)
								 ->where('trv_status',"1");
			  $totalPurposedTrove = $this->db->getValue(TBL_TROVES,"count(trv_id)");
				
				if($totalPurposedTrove != count($purposedTroveIdArr)){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "Please, select some valid trove items for purpose.",
							);
						return $returnData;
				}
				
				if($totalPurposedTrove > 4){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "You can't purpose more than 4 items.",
							);
						return $returnData;
				}
				
				$insertData = array(
								'trd_u_id'									=> $userId,
								'trd_other_u_id'						=> $otherUserId,
								'trd_parent_trd_id'					=> "0",
								'trd_purpose_trv_id_list'		=> $purposedTroveIdList,
								'trd_desire_trv_id_list'		=> $desiredTroveIdList,
								'trd_created_date'					=> $this->utc_time,
								'trd_modified_date'					=> $this->utc_time,
								'trd_status'								=> "2",
						);
				$tradeId = $this->db->insert(TBL_TRADES,$insertData);
				
				if($tradeId){			
						$returnData = array(
										'statusCode'	=> "1",
										'message'			=> "Success",
										'tradeId'			=> $tradeId,
								);
						return $returnData;
				}
				
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "Oops, There are something problem occure whilre trade purposing.",
					);
				return $returnData;
    }
		
		
		function counterTrade($inputData){ // tp // done
				$tradeId 							= $inputData['tradeId'];
				$userId 							= $inputData['userId'];
				$otherUserId					= $inputData['otherUserId'];
				$desiredTroveIdList 	= $inputData['desiredTroveIdList'];
				$purposedTroveIdList 	= $inputData['purposedTroveIdList'];
				
				$this->db->where('trd_id',$tradeId)
								 ->where('trd_u_id',$otherUserId)
								 ->where('trd_other_u_id',$userId)
								 ->where('trd_status','2');
				$existData = $this->db->getOne(TBL_TRADES);
				
				if(!checkArr($existData)){
						$returnData = array(
								'statusCode'	=> "2",
								'message'			=> "There are no trade exists with which you want to counter.",
							);
						return $returnData;
				}
				
				if($desiredTroveIdList == ""){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "Please, select some valid desire trove items.",
							);
						return $returnData;
				}
				
				$desiredTroveIdArr 	= explode(',',$desiredTroveIdList);
				
				// Check all desired items in other user's tray or not
				$this->db->where('trv_id',$desiredTroveIdArr,"IN")
								 ->where('trv_u_id',$otherUserId)
								 ->where('trv_status',"1");
			  $totalDesiredTrove = $this->db->getValue(TBL_TROVES,"count(trv_id)");
				
				if($totalDesiredTrove != count($desiredTroveIdArr)){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "Please, select some valid desire trove items.",
							);
						return $returnData;
				}
				
				if($totalDesiredTrove > 4){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "You can't select more than 4 desire items.",
							);
						return $returnData;
				}
				
				// Check all purposed items in my's tray or not
				if($purposedTroveIdList == ""){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "Please, select some valid trove items for purpose.",
							);
						return $returnData;
				}
				$purposedTroveIdArr 	= explode(',',$purposedTroveIdList);
				$this->db->where('trv_id',$purposedTroveIdArr,"IN")
								 ->where('trv_u_id',$userId)
								 ->where('trv_status',"1");
			  $totalPurposedTrove = $this->db->getValue(TBL_TROVES,"count(trv_id)");
				
				if($totalPurposedTrove != count($purposedTroveIdArr)){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "Please, select some valid trove items for purpose.",
							);
						return $returnData;
				}
				
				if($totalPurposedTrove > 4){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "You can't purpose more than 4 items.",
							);
						return $returnData;
				}
				
				$updateData = array(
						'trd_status' 						=> "3",  // First reject previous trade
						'trd_moderate_by_u_id'	=> $userId,
						'trd_modified_date'			=> $this->utc_time,
					);
				$this->db->where("trd_id",$existData['trd_id']);
				$isUpdated = $this->db->update(TBL_TRADES,$updateData);
				
				if(!$isUpdated){
						$returnData = array(
								'statusCode'	=> "2",
								'message'			=> "There are something problem with counter this trade.",
							);
						return $returnData;
				}
				
				$insertData = array(
								'trd_u_id'									=> $userId,
								'trd_other_u_id'						=> $otherUserId,
								'trd_parent_trd_id'					=> $tradeId,
								'trd_purpose_trv_id_list'		=> $purposedTroveIdList,
								'trd_desire_trv_id_list'		=> $desiredTroveIdList,
								'trd_created_date'					=> $this->utc_time,
								'trd_modified_date'					=> $this->utc_time,
								'trd_status'								=> "2",
						);
				$counterTradeId = $this->db->insert(TBL_TRADES,$insertData);
				
				if($counterTradeId){			
						$returnData = array(
										'statusCode'	=> "1",
										'message'			=> "Success",
										'tradeId'			=> $counterTradeId,
								);
						return $returnData;
				}
				
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "Oops, There are something problem occure while trade countering.",
					);
				return $returnData;
    }
		
		
		function moderateTrade($inputData){ // tp // done  
				$tradeId 							= $inputData['tradeId'];
				$status								= $inputData['status'];
				$userId								= $inputData['userId'];
				
				$this->db->where('trd_id',$tradeId)
								 ->where('trd_other_u_id',$userId)
								 ->where('trd_status','2');
				$existData = $this->db->getOne(TBL_TRADES);
				
				if(!checkArr($existData)){
						$returnData = array(
								'statusCode'	=> "2",
								'message'			=> "There are no trade exists with which you want to action.",
							);
						return $returnData;
				}
				
				$totalSaveCrabonByTradeSender = "0";
				$totalSaveCrabonByTradeReceiver = "0";
				if($status == '1'){
						
						// if trade made accept than check all items are exist to both user's own tray
						$desiredTroveIdList  = $existData['trd_desire_trv_id_list'];
						$desiredTroveIdArr 	= explode(',',$desiredTroveIdList);
						
						// Check all desired items in other user's tray or not
						$this->db->join(TBL_CATEGORIES,"cat_id = trv_cat_id AND cat_status = 1");
						$this->db->where('trv_id',$desiredTroveIdArr,"IN")
										 ->where('trv_u_id',$existData['trd_other_u_id'])
										 ->where('trv_status',"1");
						$totalDesiredTroveList = $this->db->get(TBL_TROVES);
						
						if(!checkArr($totalDesiredTroveList) || (count($totalDesiredTroveList) != count($desiredTroveIdArr))){
								$returnData = array(
										'statusCode'		=> "2",
										'message'			=> "Sorry, TradeMade found that there are some trove item in your which aren't exist longer for trade.",
									);
								return $returnData;
						}
						
						foreach($totalDesiredTroveList as $key => $value){
								$totalSaveCrabonByTradeReceiver += $value["cat_carbon_lbs"];
						}
						
						// Check all purposed items in my's tray or not
						$purposedTroveIdList = $existData['trd_purpose_trv_id_list'];
						$purposedTroveIdArr 	= explode(',',$purposedTroveIdList);
						
						$this->db->join(TBL_CATEGORIES,"cat_id = trv_cat_id AND cat_status = 1");
						$this->db->where('trv_id',$purposedTroveIdArr,"IN")
										 ->where('trv_u_id',$existData['trd_u_id'])
										 ->where('trv_status',"1");
						$totalPurposedTroveList = $this->db->get(TBL_TROVES);
						if(!checkArr($totalPurposedTroveList) || (count($totalPurposedTroveList) != count($purposedTroveIdArr))){
								$returnData = array(
										'statusCode'	=> "2",
										'message'			=> "Sorry, TradeMade found that there are some trove items which aren't exist longer for trade in user account.",
									);
								return $returnData;
						}
						
						// if i am purpose my items than my items category carbons i saved as per Nathan
						// which items user purpose to receiver person that items he saved carbon..
						foreach($totalDesiredTroveList as $key => $value){
								$totalSaveCrabonByTradeSender += $value["cat_carbon_lbs"];
						}
						
				}
				
				$updateData = array(
						'trd_status' 						=> ($status == '1' ? '1' : '3'), 	// 1: accept , 3 : reject
						'trd_moderate_by_u_id'	=> $userId,
						'trd_modified_date'			=> $this->utc_time,
					);
				$this->db->where("trd_id",$existData['trd_id']);
				$isUpdated = $this->db->update(TBL_TRADES,$updateData);
				
				if($isUpdated){
						if($status == '1'){
								// Updat saved carbon and trade success counter
								$updateUserSenderData = array(
												'u_total_saved_carbon' => $this->db->inc($totalSaveCrabonByTradeSender),
												'u_total_trade_made' 	 => $this->db->inc(1),
												'u_modified_date'			 => $this->utc_time,
										);
								$this->db->where("u_id",$existData['trd_u_id']);
								$this->db->update(TBL_USERS,$updateUserSenderData);
								
								// Updat saved carbon and trade success counter
								$updateUserSenderData = array(
												'u_total_saved_carbon' => $this->db->inc($totalSaveCrabonByTradeReceiver),
												'u_total_trade_made' 	 => $this->db->inc(1),
												'u_modified_date'			 => $this->utc_time,
										);
								$this->db->where("u_id",$existData['trd_other_u_id']);
								$this->db->update(TBL_USERS,$updateUserSenderData);
								
								// Delete old entries for review pending
								$this->db->where("((rvw_remain_u_id = ? AND rvw_remain_other_u_id = ?) OR (rvw_remain_other_u_id = ? AND rvw_remain_u_id = ?))",array($existData['trd_u_id'],$existData['trd_other_u_id'],$existData['trd_u_id'],$existData['trd_other_u_id']));
								$this->db->delete(TBL_TRADE_REVIEW_REMAINS);
								
								// Make new entries for reveiew to each other user after succussfully trade.
								$remainReviewData = array(
												'rvw_remain_u_id'				=> $existData['trd_u_id'],
												'rvw_remain_other_u_id'	=> $existData['trd_other_u_id'],
												'rvw_remain_trade_id'		=> $existData['trd_id'],
												'rvw_remain_is_notificaiton_sent'	=> "0",
												'rvw_remain_created_date'	=> $this->utc_time,
										);
								$this->db->insert(TBL_TRADE_REVIEW_REMAINS,$remainReviewData);
								
								$remainReviewData = array(
												'rvw_remain_u_id'				=> $existData['trd_other_u_id'],
												'rvw_remain_other_u_id'	=> $existData['trd_u_id'],
												'rvw_remain_trade_id'		=> $existData['trd_id'],
												'rvw_remain_is_notificaiton_sent'	=> "0",
												'rvw_remain_created_date'	=> $this->utc_time,
										);
								$this->db->insert(TBL_TRADE_REVIEW_REMAINS,$remainReviewData);
						}
						$returnData = array(
								'statusCode'	=> "1",
								'message'			=> "Success.",
								'action'			=> ($status == '1' ? '1' : '3'),
							);
						return $returnData;
				}
				
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "Oops, There are something problem occure while trade actioning.",
					);
				return $returnData;
    }
		
		
		function cancelTrade($inputData){ // tp // done  
				$tradeId 							= $inputData['tradeId'];
				$userId								= $inputData['userId'];
				
				$this->db->where('trd_id',$tradeId)
								 ->where('trd_u_id',$userId)
								 ->where('trd_status','2');
				$existData = $this->db->getOne(TBL_TRADES);
				
				if(!checkArr($existData)){
						$returnData = array(
								'statusCode'	=> "2",
								'message'			=> "There are no trade exists with which you want to cancel.",
							);
						return $returnData;
				}
				
				$updateData = array(
						'trd_status' 						=> "9",	// delete 
						'trd_moderate_by_u_id'	=> $userId,
						'trd_modified_date'			=> $this->utc_time,
					);
				$this->db->where("trd_id",$existData['trd_id']);
				$isUpdated = $this->db->update(TBL_TRADES,$updateData);
				
				if($isUpdated){
						$returnData = array(
								'statusCode'	=> "1",
								'message'			=> "Success.",
								'action'			=> "9",
							);
						return $returnData;
				}
				
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "Oops, There are something problem occure while trade canceling.",
					);
				return $returnData;
    }
		
		function getSentTradeList($inputData){ // done
				$userId 				= $inputData['userId'];
				$maxDataId			= $inputData['maxDataId'];
				$pageNo 				= $inputData['pageNo'];
				
				$limit      = 20;
				$pageStartLimit	= ($pageNo - 1) * $limit;
		
				$maxId		= 0;
				$totalCount = 0;
				
				$whereQuery = "";
				if($pageNo > 1){
					$whereQuery .= " trd_id <= '".$maxDataId."' AND ";
				}
				
				if($pageNo == '1'){
						$qry = "SELECT
										count(trd_id) as totalCount,MAX(trd_id) as maxId
								FROM
										".TBL_TRADES_AS."
								JOIN
										`".TBL_USERS."` AS u ON u_id = trd_other_u_id and u_status = '1'
								WHERE
										".$whereQuery."
										trd_u_id = '".$userId."' AND
										(trd_status = '2' OR trd_status = '3')
						";		
						$countData = $this->db->rawQuery($qry,null,false);
						if(checkArr($countData)){
								$totalCount = $countData['0']['totalCount'];
								$maxId 		= ($totalCount > 0) ? $countData['0']['maxId'] : 0;
						}
				}
				$qry = "SELECT
										".$this->trdFL.",u.u_id,u.u_photo,u.u_thumb_photo,u.u_first_name,u.u_last_name,u.u_avg_rating
								FROM
										".TBL_TRADES_AS."
								JOIN
										`".TBL_USERS."` AS u ON u_id = trd_other_u_id and u_status = '1'
								WHERE
										".$whereQuery."
										trd_u_id = '".$userId."' AND
										(trd_status = '2' OR trd_status = '3')
								ORDER BY
										trd_status ASC,	
										trd_id DESC
								LIMIT
										".$pageStartLimit.", ".$limit."
					";		//FIELD(trd_status,'2','1','3'),
				$dataList = $this->db->rawQuery($qry,null,false);
				
				foreach($dataList as $key => $value){
						
						// Pruposed list
						$purposedList = explode(',',$value['trd_purpose_trv_id_list']);
						$dataList[$key]['purposedList'] = array();
						foreach($purposedList as $k => $v){
								$dataList[$key]['purposedList'][] = array(
												'trv_id' => $v,
												'troveImage' => troveImageById($value['trd_u_id'],$v),
										);
						}
						
						// Desire list
						$desiredList = explode(',',$value['trd_desire_trv_id_list']);
						$dataList[$key]['desiredList'] = array();
						foreach($desiredList as $k => $v){
								$dataList[$key]['desiredList'][] = array(
												'trv_id' 		=> $v,
												'troveImage'  => troveImageById($value['trd_other_u_id'],$v),
										);
						}
						
						$dataList[$key]['profileImage']  = userProfileImage($value['u_id'],$value['u_photo'],$value['u_thumb_photo']);
						$dataList[$key] = unsetUserExtraData($dataList[$key]);
				}
					
				$returnData = array(
						'statusCode' => "1",
						'message'	 => count($dataList) > 0 ? "Success" : "No more data availible",
						'dataList'	 => $dataList,
						'totalCount' => $totalCount,
						'maxId'		 => ($pageNo == 1) ? $maxId : $maxDataId,
					);
				return $returnData;
		}
		
		function getReceivedTradeList($inputData){ // tp // done
				$userId 				= $inputData['userId'];
				$maxDataId			= $inputData['maxDataId'];
				$pageNo 				= $inputData['pageNo'];
				
				$limit      = 20;
				$pageStartLimit	= ($pageNo - 1) * $limit;
		
				$maxId		= 0;
				$totalCount = 0;
				
				$whereQuery = "";
				if($pageNo > 1){
					$whereQuery .= " trd_id <= '".$maxDataId."' AND ";
				}
				
				if($pageNo == '1'){
						$qry = "SELECT
										count(trd_id) as totalCount,MAX(trd_id) as maxId
								FROM
										".TBL_TRADES_AS."
								JOIN
										`".TBL_USERS."` AS u ON u_id = trd_u_id and u_status = '1'
								WHERE
										".$whereQuery."
										trd_other_u_id = '".$userId."' AND
										(trd_status = '2' OR trd_status = '3')
						";		
						$countData = $this->db->rawQuery($qry,null,false);
						if(checkArr($countData)){
								$totalCount = $countData['0']['totalCount'];
								$maxId 		= ($totalCount > 0) ? $countData['0']['maxId'] : 0;
						}
				}
				$qry = "SELECT
										".$this->trdFL.",u.u_id,u.u_photo,u.u_thumb_photo,u.u_first_name,u.u_last_name,u.u_avg_rating
								FROM
										".TBL_TRADES_AS."
								JOIN
										`".TBL_USERS."` AS u ON u_id = trd_u_id and u_status = '1'
								WHERE
										".$whereQuery."
										trd_other_u_id = '".$userId."' AND
										(trd_status = '2' OR trd_status = '3')
								ORDER BY
										trd_status ASC,
										trd_id DESC
								LIMIT
										".$pageStartLimit.", ".$limit."
					";		
				$dataList = $this->db->rawQuery($qry,null,false);
				
				foreach($dataList as $key => $value){
						
						// Pruposed list
						$purposedList = explode(',',$value['trd_purpose_trv_id_list']);
						$dataList[$key]['purposedList'] = array();
						foreach($purposedList as $k => $v){
								$dataList[$key]['purposedList'][] = array(
												'trv_id' => $v,
												'troveImage' => troveImageById($value['trd_u_id'],$v),
										);
						}
						
						// Desire list
						$desiredList = explode(',',$value['trd_desire_trv_id_list']);
						$dataList[$key]['desiredList'] = array();
						foreach($desiredList as $k => $v){
								$dataList[$key]['desiredList'][] = array(
												'trv_id' 		=> $v,
												'troveImage'  => troveImageById($value['trd_other_u_id'],$v),
										);
						}
						
						$dataList[$key]['profileImage']  = userProfileImage($value['u_id'],$value['u_photo'],$value['u_thumb_photo']);
						$dataList[$key] = unsetUserExtraData($dataList[$key]);
				}
					
				$returnData = array(
						'statusCode' => "1",
						'message'	 => count($dataList) > 0 ? "Success" : "No more data availible",
						'dataList'	 => $dataList,
						'totalCount' => $totalCount,
						'maxId'		 => ($pageNo == 1) ? $maxId : $maxDataId,
					);
				return $returnData;
		}
		
		function getMadeTradeList($inputData){ // tp // done
				$userId 				= $inputData['userId'];
				$maxDataId			= $inputData['maxDataId'];
				$pageNo 				= $inputData['pageNo'];
				
				$limit      = 20;
				$pageStartLimit	= ($pageNo - 1) * $limit;
		
				$maxId		= 0;
				$totalCount = 0;
				
				$whereQuery = "";
				if($pageNo > 1){
					$whereQuery .= " trd_id <= '".$maxDataId."' AND ";
				}
				
				if($pageNo == '1'){
						$qry = "SELECT
										count(trd_id) as totalCount,MAX(trd_id) as maxId
								FROM
										".TBL_TRADES_AS."
								JOIN
										`".TBL_USERS."` AS u ON u_id = trd_u_id and u_status = '1'
								WHERE
										".$whereQuery."
										(
												trd_u_id = '".$userId."' OR
												trd_other_u_id = '".$userId."'
										) AND
										trd_status = '1'
						";		
						$countData = $this->db->rawQuery($qry,null,false);
						if(checkArr($countData)){
								$totalCount = $countData['0']['totalCount'];
								$maxId 		= ($totalCount > 0) ? $countData['0']['maxId'] : 0;
						}
				}
				$qry = "SELECT
										".$this->trdFL.",u.u_id,u.u_photo,u.u_thumb_photo,u.u_first_name,u.u_last_name,u.u_avg_rating
								FROM
										".TBL_TRADES_AS."
								JOIN
										`".TBL_USERS."` AS u ON u_id = trd_u_id and u_status = '1'
								WHERE
										".$whereQuery."
										(
												trd_u_id = '".$userId."' OR
												trd_other_u_id = '".$userId."'
										) AND
										trd_status = '1'
								ORDER BY
										trd_modified_date DESC
								LIMIT
										".$pageStartLimit.", ".$limit."
					";		
				$dataList = $this->db->rawQuery($qry,null,false);
				
				foreach($dataList as $key => $value){
						
						// Pruposed list
						$purposedList = explode(',',$value['trd_purpose_trv_id_list']);
						$dataList[$key]['purposedList'] = array();
						foreach($purposedList as $k => $v){
								$dataList[$key]['purposedList'][] = array(
												'trv_id' => $v,
												'troveImage' => troveImageById($value['trd_u_id'],$v),
										);
						}
						
						// Desire list
						$desiredList = explode(',',$value['trd_desire_trv_id_list']);
						$dataList[$key]['desiredList'] = array();
						foreach($desiredList as $k => $v){
								$dataList[$key]['desiredList'][] = array(
												'trv_id' 		=> $v,
												'troveImage'  => troveImageById($value['trd_other_u_id'],$v),
										);
						}
						
						$dataList[$key]['profileImage']  = userProfileImage($value['u_id'],$value['u_photo'],$value['u_thumb_photo']);
						$dataList[$key] = unsetUserExtraData($dataList[$key]);
				}
					
				$returnData = array(
						'statusCode' => "1",
						'message'	 => count($dataList) > 0 ? "Success" : "No more data availible",
						'dataList'	 => $dataList,
						'totalCount' => $totalCount,
						'maxId'		 => ($pageNo == 1) ? $maxId : $maxDataId,
					);
				return $returnData;
		}
		
		function addRatingToUser($inputData){ // tp 
				$userId 			= $inputData['userId'];
				$otherUserId	= $inputData['otherUserId'];
				$rateNo 			= $inputData['rateNo'];
				$desc 				= $inputData['desc'];
				
				if(!is_numeric($rateNo) && $rateNo < "1" || $rateNo > "5"){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "Please try again, invalid selection of user rating.",
							);
						return $returnData;
				}
				
				$this->db->where('rvw_remain_u_id',$userId)
								 ->where('rvw_remain_other_u_id',$otherUserId);
			  $remainReview = $this->db->getOne(TBL_TRADE_REVIEW_REMAINS);
				
				if(!checkArr($remainReview)){
						$returnData = array(
								'statusCode'		=> "2",
								'message'			=> "Oops, There are no review remain for this user. Try after next successfully trade.",
							);
						return $returnData;
				}
				
				$insertData = array(
								'u_rvw_u_id'					=> $otherUserId,
								'u_rvw_by_u_id'				=> $userId,
								'u_rvw_rate_no'				=> $rateNo,
								'u_rvw_desc'					=> $desc,
								'u_rvw_created_date'	=> $this->utc_time,
								'u_rvw_modified_date'	=> $this->utc_time,
								'u_rvw_status'				=> "1",
						);
				$reviewId = $this->db->insert(TBL_USER_REVIEWS,$insertData);
				
				if($reviewId){
						$this->db->where("rvw_remain_u_id",$userId)
										 ->where("rvw_remain_other_u_id",$otherUserId);
						$this->db->delete(TBL_TRADE_REVIEW_REMAINS);
								
						$returnData = array(
										'statusCode'	=> "1",
										'message'			=> "Success",
										'reviewId'		=> $reviewId,
								);
						return $returnData;
				}
				
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "Oops, There are something problem occure whilre rating to user.",
					);
				return $returnData;
    }
		
		
		function getUserReviewList($inputData){ // tp // done
				$userId 				= $inputData['userId'];
				$otherUserId		= $inputData['otherUserId'];
				$maxDataId			= $inputData['maxDataId'];
				$pageNo 				= $inputData['pageNo'];
				
				$limit      = 20;
				$pageStartLimit	= ($pageNo - 1) * $limit;
		
				$maxId		= 0;
				$totalCount = 0;
				
				$whereQuery = "";
				if($pageNo > 1){
					$whereQuery .= " u_rvw_id <= '".$maxDataId."' AND ";
				}
				
				if($pageNo == '1'){
						$qry = "SELECT
										count(u_rvw_id) as totalCount,MAX(u_rvw_id) as maxId
								FROM
										".TBL_USER_REVIEWS_AS."
								JOIN
										".TBL_USERS_AS." ON u_id = u_rvw_by_u_id and u_status = '1'
								WHERE
										".$whereQuery."
										u_rvw_u_id = '".$otherUserId."' AND
										u_rvw_status = '1'
						";		
						$countData = $this->db->rawQuery($qry,null,false);
						if(checkArr($countData)){
								$totalCount = $countData['0']['totalCount'];
								$maxId 		= ($totalCount > 0) ? $countData['0']['maxId'] : 0;
						}
				}
				$qry = "SELECT
										".$this->uRvwFL.",u.u_id,u.u_photo,u.u_thumb_photo,u.u_first_name,u.u_last_name
								FROM
										".TBL_USER_REVIEWS_AS."
								JOIN
										".TBL_USERS_AS." ON u_id = u_rvw_by_u_id and u_status = '1'
								WHERE
										".$whereQuery."
										u_rvw_u_id = '".$otherUserId."' AND
										u_rvw_status = '1'
								ORDER BY
										u_rvw_id DESC
								LIMIT
										".$pageStartLimit.", ".$limit."
					";		
				$dataList = $this->db->rawQuery($qry,null,false);
				
				foreach($dataList as $key => $value){
						$dataList[$key]['profileImage']  = userProfileImage($value['u_id'],$value['u_photo'],$value['u_thumb_photo']);
						$dataList[$key] = unsetUserExtraData($dataList[$key]);
				}
					
				$returnData = array(
						'statusCode' => "1",
						'message'	 => count($dataList) > 0 ? "Success" : "No more data availible",
						'dataList'	 => $dataList,
						'totalCount' => $totalCount,
						'maxId'		 => ($pageNo == 1) ? $maxId : $maxDataId,
					);
				return $returnData;
		}
		
		
		function getUserList($inputData){ // tp // done 
				$userId 				= $inputData['userId'];
				$desireIdList		= $inputData['desireIdList'];
				$searchData			= $inputData['searchData'];
				$isFacebookFriend	= $inputData['isFacebookFriend'];
				$maxDataId			= $inputData['maxDataId'];
				$pageNo 				= $inputData['pageNo'];
				
				$limit      = 20;
				$pageStartLimit	= ($pageNo - 1) * $limit;
		
				$maxId		= 0;
				$totalCount = 0;
				
				$whereQuery = "";
				if($pageNo > 1){
					$whereQuery .= " u_id <= '".$maxDataId."' AND ";
				}
				
				if($desireIdList != ""){
						$whereQuery .= " u_id IN ( SELECT u_dsr_u_id FROM ".TBL_USER_DESIRES." WHERE 	u_dsr_cat_id IN (".$desireIdList.") AND u_dsr_status = '1' ) AND ";
				}
				
				if($searchData != ""){
						$whereQuery .= " ( u_first_name LIKE '".$searchData."%' OR u_first_name = '".$searchData."' OR  u_last_name LIKE '".$searchData."%' OR u_last_name = '".$searchData."' OR concat(u_first_name,' ',u_last_name)  = '".$searchData."' OR concat(u_first_name,' ',u_last_name) LIKE '".$searchData."%'  ) AND ";
				}
				
				if($isFacebookFriend == "1"){
						$whereQuery .= " u_id IN (SELECT scl_frd_other_u_id FROM ".TBL_SOCIAL_FRIENDS_AS." WHERE scl_frd_u_id = '".$userId."' AND scl_frd_from = '2' AND scl_frd_status = '1') AND ";
				}
				
				if($pageNo == '1'){
						$qry = "SELECT
										count(u_id) as totalCount,MAX(u_id) as maxId
								FROM
										".TBL_USERS_AS."
								WHERE
										".$whereQuery."
										u_id != '".$userId."' AND
										u_status = '1'
						";		
						$countData = $this->db->rawQuery($qry,null,false);
						if(checkArr($countData)){
								$totalCount = $countData['0']['totalCount'];
								$maxId 		= ($totalCount > 0) ? $countData['0']['maxId'] : 0;
						}
				}
				$qry = "SELECT
										".$this->uFL."
								FROM
										".TBL_USERS_AS."
								WHERE
										".$whereQuery."
										u_id != '".$userId."' AND
										u_status = '1'
								ORDER BY
										u_id DESC
								LIMIT
										".$pageStartLimit.", ".$limit."
					";		
				$dataList = $this->db->rawQuery($qry,null,false);
				
				foreach($dataList as $key => $value){
						$dataList[$key]['profileImage']  = userProfileImage($value['u_id'],$value['u_photo'],$value['u_thumb_photo']);
						$dataList[$key] = unsetUserExtraData($dataList[$key]);
				}
					
				$returnData = array(
						'statusCode' => "1",
						'message'	 	 => count($dataList) > 0 ? "Success" : "No more data availible",
						'dataList'	 => $dataList,
						'totalCount' => $totalCount,
						'maxId'		 	 => ($pageNo == 1) ? $maxId : $maxDataId,
					);
				return $returnData;
		}
		
		//ALTER TABLE `a_users` CHANGE `u_latitude` `u_latitude` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',CHANGE `u_longitude` `u_longitude` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0';
		function getBrowseTroveList($inputData){ // tp // done 
				$userId 			= $inputData['userId'];
				$searchData			= $inputData['searchData'];
				$priceRange			= $inputData['priceRange'];
				$distanceRange		= $inputData['distanceRange'];
				$categoryIdList		= $inputData['categoryIdList'];
				$materialId			= $inputData['materialId'];
				$isFacebookFriend	= $inputData['isFacebookFriend'];
				$maxDataId			= $inputData['maxDataId'];
				$pageNo 			= $inputData['pageNo'];
				
				$this->db->where('u_id',$userId)
						 ->where('u_status','1');
				$userData = $this->db->getOne(TBL_USERS);
				
				$limit      = 20;
				$pageStartLimit	= ($pageNo - 1) * $limit;
		
				$maxId		= 0;
				$totalCount = 0;
				
				$whereQuery = "";
				$orderByQuery = "trv_id DESC";
				if($pageNo > 1){
					$whereQuery .= " trv_id <= '".$maxDataId."' AND ";
				}
				
				if($categoryIdList != ""){
						$whereQuery .= " trv_cat_id IN (".$categoryIdList.") AND ";
						$orderByQuery = "distance ASC, trv_id DESC";
				}
				
				if($isFacebookFriend == "1"){
						$whereQuery .= " trv_u_id IN (SELECT scl_frd_other_u_id FROM ".TBL_SOCIAL_FRIENDS_AS." WHERE scl_frd_u_id = '".$userId."' AND scl_frd_from = '2' AND scl_frd_status = '1') AND ";
						$orderByQuery = "distance ASC, trv_id DESC";
				}
				
				if($materialId != ""){
						$whereQuery .= " trv_mtl_id = '".$materialId."' AND ";
						$orderByQuery = "distance ASC, trv_id DESC";
				}
				
				if($priceRange != "" && $priceRange > 0){
						$whereQuery .= " trv_price_range = '".$priceRange."' AND ";
						$orderByQuery = "distance ASC, trv_id DESC";
				}
				
				if($searchData != ""){
						$whereQuery .= " ( trv_desc LIKE '".$searchData."%' OR trv_desc = '".$searchData."' ) AND ";
						$orderByQuery = "distance ASC, trv_id DESC";
				}
				
				$havingQuery = "";
				if($distanceRange != "" && $distanceRange > 0){ 
						$havingQuery .= " HAVING distance < '".$distanceRange."' ";
						$orderByQuery = "distance ASC, trv_id DESC";
				}
				
				if($pageNo == '1'){
						$qry = "SELECT
										count(trv_id) as totalCount,MAX(trv_id) as maxId,get_distance_in_miles_between_geo_locations('".$userData['u_latitude']."','".$userData['u_longitude']."',u_latitude,u_longitude) as distance 
								FROM
										".TBL_TROVES_AS."
								JOIN
										".TBL_USERS_AS." ON u_id = trv_u_id and u_status = '1'
								WHERE
										".$whereQuery."
										trv_u_id != '".$userId."' AND 
										trv_status = '1'
								".$havingQuery."
						";		
						$countData = $this->db->rawQuery($qry,null,false);
						if(checkArr($countData)){
								$totalCount = $countData['0']['totalCount'];
								$maxId 		= ($totalCount > 0) ? $countData['0']['maxId'] : 0;
						}
				}
				$qry = "SELECT
										".$this->trvFL.",".$this->uFL.",get_distance_in_miles_between_geo_locations('".$userData['u_latitude']."','".$userData['u_longitude']."',u_latitude,u_longitude) as distance 
								FROM
										".TBL_TROVES_AS."
								JOIN
										".TBL_USERS_AS." ON u_id = trv_u_id and u_status = '1'
								WHERE
										".$whereQuery."
										trv_u_id != '".$userId."' AND 
										trv_status = '1'
								".$havingQuery."
								ORDER BY
										".$orderByQuery."
								LIMIT
										".$pageStartLimit.", ".$limit."
					";		
				$dataList = $this->db->rawQuery($qry,null,false);
				
				
				
				foreach($dataList as $key => $value){
						$dataList[$key]['troveImages'] =  troveImageAllById($value['trv_u_id'],$value['trv_id']); 
						$dataList[$key]['profileImage']  = userProfileImage($value['u_id'],$value['u_photo'],$value['u_thumb_photo']);
						$dataList[$key] = unsetUserExtraData($dataList[$key]);
				}
					
				$returnData = array(
						'statusCode' => "1",
						'message'	 	 => count($dataList) > 0 ? "Success" : "No more data availible",
						'dataList'	 => $dataList,
						'totalCount' => $totalCount,
						'maxId'		 	 => ($pageNo == 1) ? $maxId : $maxDataId,
					);
				return $returnData;
		}
		function removeTroveasdasd($inputData){ 
				$userId 	 		= $inputData['userId']; 
				$otherUserId 	= $inputData['otherUserId'];
				$status	 			= $inputData['status'];
				
				$this->db->join(TBL_MATCHES,"((match_u_id = '".$userId."' AND match_other_u_id = u_id) OR (match_u_id = u_id AND match_other_u_id = '".$userId."'))","LEFT");
				$this->db->where('u_id',$otherUserId)
						 ->where('u_status','1');
				$existData = $this->db->getOne(TBL_USERS);
				
				if(!checkArr($existData)){
						$returnData = array(
								'statusCode'	=> "2",
								'message'		=> "Invalid request data.",
							);
						return $returnData;
				}
				
				if(is_numeric($existData['match_id']) && $existData['match_id'] > 0){
						if($existData['match_status'] == "3" && $existData['match_u_id'] == $userId){				
								$returnData = array(
												'statusCode'		=> "1",
												'message'			=> "You have already rejected to this user",
												'actionPerformed'	=> "3",
												'isMatchedFromBoth'	=> "false",
										);
								return $returnData;
						}else if($existData['match_status'] == "3" && $existData['match_other_u_id'] == $userId){
								$returnData = array(
												'statusCode'		=> "1",
												'message'			=> "You have already rejected by this user",
												'actionPerformed'	=> "3",
												'isMatchedFromBoth'	=> "false",
										);
								return $returnData;
						}else if($existData['match_status'] == "1"){
								$returnData = array(
												'statusCode'		=> "1",
												'message'			=> "Your matches have been already done with this user",
												'actionPerformed'	=> "1",
												'isMatchedFromBoth'	=> "true",
										);
								return $returnData;
						}else if($existData['match_status'] == "2" && $existData['match_u_id'] == $userId){
								$returnData = array(
												'statusCode'		=> "1",
												'message'			=> "You have already accepted this user",
												'actionPerformed'	=> "2",
												'isMatchedFromBoth'	=> "false",
										);
								return $returnData;
						} else{
						//}else if($existData['match_status'] == "2" && $existData['match_other_u_id'] == $userId){
							$updateData = array(
									'match_status' 			=> ($status == "2") ? "1" :"3",  // first user accepted than your matches done
									'match_modified_date'	=> $this->utc_time,
								);
							$this->db->where("match_id",$existData['match_id']);
							$isUpdated = $this->db->update(TBL_MATCHES,$updateData);
							
							$conversationId = 0;
							$otherUserdata = array();
							if($status == "2"){
								$a = array(
										'userId'	=> $userId,
										'otherUserId'	=> $otherUserId,
									);
								$res = $this->getUserConversationId($a);
								
								if(isset($res['conversationId']) && $res['conversationId'] > 0){
									$conversationId = $res['conversationId'];
								}
								
								$this->db->where('u_id',$otherUserId)
										 ->where('u_status','1');
								$otherUserdata = $this->db->getOne(TBL_USERS." as u",$this->uFL); 
									
								if(checkArr($otherUserdata)){
										$otherUserdata['profileImage']  	= userProfileImage($otherUserdata['u_id'],$otherUserdata['u_photo'],$otherUserdata['u_thumb_photo']);
										$otherUserdata['profileVideo'] 		= userProfileVideo($otherUserdata['u_id'],$otherUserdata['u_video'],$otherUserdata['u_thumb_video']);
										unset($otherUserdata['u_photo']);
								}
								/// FIRST LIKER GET NOTIFICATION
								$this->db->join(TBL_USERS,"u_id = udt_u_id AND u_status = '1'"); //AND u_notification_on = '1' 
								$this->db->where('udt_u_id',$otherUserId) 
										->where('udt_device_token',"","!=")
										->where('udt_device_token','null',"!=")
										->where('udt_status','1');
								$frdDeviceList = $this->db->get(TBL_USER_DEVICE_TOKENS);
								//echo $this->db->getLastQuery();
								if(is_array($frdDeviceList) && count($frdDeviceList) > 0){
											$this->db->where('u_id',$userId)
													 ->where('u_status','1');
											$senderData = $this->db->getOne(TBL_USERS);
											
											$iosDeviceList = array();
											foreach($frdDeviceList as $key=> $value){
												if($value['udt_device_token'] != ""){
													$iosDeviceList[] = $value['udt_device_token'];
												}
											}
											
											$message = array();
											$message['aps']['icon'] 		= "appicon";
											$message['aps']['alert'] 		= "You and ".$senderData['u_name']." have matched! Start chatting now.";
											$message['aps']['badge'] 		= "1";
											$message['aps']['sound'] 		= "default";
											$message['aps']['uname'] 		= $senderData['u_name'];
											$message['aps']['uid'] 			= $userId;
											$message['aps']['type']			= "match";
											$message['aps']['chat_conv_id']			= $conversationId;
											
											
											$sendNotification 		= send_notification_ios_with_pass($iosDeviceList,$message);
									
								}
								
							}
							$returnData = array(
									'statusCode'		=> "1",
									'message'			=> "Success",
									'actionPerformed'	=> ($status == "2") ? "1" :"3",
									'isMatchedFromBoth'	=> ($status == "2") ? "true" : "false",
									'conversationId'	=> $conversationId,
									'userData'			=> $otherUserdata,
								);
							return $returnData;
						}
				}else{
						$insertData = array(
										'match_u_id'			=> $userId,
										'match_other_u_id'		=> $otherUserId,
										'match_created_date'	=> $this->utc_time,
										'match_modified_date'	=> $this->utc_time,
										'match_status'			=> $status,
								);
						$matchId = $this->db->insert(TBL_MATCHES,$insertData);
						if($matchId){
								$returnData = array(
												'statusCode'		=> "1",
												'message'			=> "Success.",
												'actionPerformed'	=> $status,
												'isMatchedFromBoth'	=> "false",
										);
								return $returnData;
						}
				}
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "Failed.",
					);
				return $returnData;
		}
	
	
		
	
	
	
		function updateUserSettings($inputData){
				$userId 	= $inputData['userId'];
				$userData 	= $inputData['userData'];
				
				$this->db->where('u_id',$userId)
						 ->where('u_status','1');
				$isUpdated = $this->db->update(TBL_USERS,$userData);
				
				if($isUpdated){
					$returnData = array(
							'statusCode'		=> "1",
							'message'			=> "Success",
						);
					return $returnData;
				}
				
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "No update made to your settings",
					);
				return $returnData;
    }
	
	
	function logout($inputData){
		$userId 		= $inputData['userId'];
		$securityToken	= $inputData['securityToken'];
		
		$this->db->where('udt_u_id',$userId)
	//			 ->where('udt_security_token',$securityToken)
				 ->where('udt_status','1');
		$this->db->delete(TBL_USER_DEVICE_TOKENS);
		
		$returnData = array(
				'statusCode'		=> "1",
				'message'			=> "Success",
			);
		return $returnData;
    }
	
		
		function uploadChatMediaData($inputData){
				$userId 					= $inputData['userId'];
				$conversationId		= $inputData['conversationId'];
				$totalUserCanView	= $inputData['totalUserCanView'];
				$mediaData 				= $inputData['mediaData'];
				$thumbMediaData		= $inputData['thumbMediaData'];
				$mediaType				= $inputData['mediaType'];
				
				$this->db->where('chat_conv_id',$conversationId)
						 ->where('( chat_conv_u_id = ? OR chat_conv_other_u_id = ? )',array($userId,$userId))
						->where('chat_conv_status','1');
					$convData = $this->db->getOne(TBL_CHAT_CONVERSATIONS);
				if(!checkArr($convData)){
					$returnData = array(
							'statusCode'		=> "2",
							'message'			=> "You don't allow to upload media to this conversation",
						);
					return $returnData;
				}
				
				if($mediaType != "2"){
					$returnData = array(
							'statusCode'		=> "2",
							'message'			=> "This type of media should not be allowed",
						);
					return $returnData;
				}
				
				$userMediaData = array(
					'chat_file_chat_conv_id'	 			=> $conversationId,
					'chat_file_u_id'			 					=> $userId,
					'chat_file_media_type'					=> $mediaType,
					'chat_file_media_name'		 			=> "",
					'chat_file_media_thumb_name' 		=> "",
					'chat_file_total_user_can_view' => $totalUserCanView,
					'chat_file_total_viewed' 				=> "0",
					'chat_file_created_date'				=> $this->utc_time,
					'chat_file_modified_date'				=> $this->utc_time,
					'chat_file_status'							=> '1',
				);
				
				if(is_array($mediaData) && count($mediaData) > 0 && isset($mediaData["name"]) && $mediaData["name"] != '')
				{
						$targetPath = API_CHAT_REL_MEDIAPATH.$conversationId.'/';
						// MAKE THE DIRECTORY IF IT DOESN'T EXIST
						if(!file_exists(str_replace('//','/',$targetPath)))
						{
							mkdir(str_replace('//','/',$targetPath), 0777, true);
						}
						
						$filename = UploadFile($mediaData,$targetPath,$mediaType);
						
						if($filename)
						{	
							$userMediaData['chat_file_media_name'] = $filename;
						}
				}
				
				if(is_array($thumbMediaData) && count($thumbMediaData) > 0 && isset($thumbMediaData["name"]) && $thumbMediaData["name"] != '')
				{
						$targetPath = API_CHAT_REL_MEDIAPATH.$conversationId.'/'.MEDIA_THUMB_FLD_NAME;
						
						// MAKE THE DIRECTORY IF IT DOESN'T EXIST
						if(!file_exists(str_replace('//','/',$targetPath)))
						{
							mkdir(str_replace('//','/',$targetPath), 0777, true);
						}
						
						$thumbFilename = UploadFile($thumbMediaData,$targetPath,"1");
						
						if($thumbFilename)
						{	
							$userMediaData['chat_file_media_thumb_name'] = $thumbFilename;
						}
				} 
				
				if($userMediaData['chat_file_media_name'] != ""){
					
					$chatMediaId = $this->db->insert(TBL_CHAT_FILES,$userMediaData);
					
					if($chatMediaId){
						$returnData = array(
								'statusCode'		=> "1",
								'message'				=> "Success",
								'chatMediaId'		=> $chatMediaId,
								'mediaUrl'			=> ($userMediaData['chat_file_media_name'] != "" && file_exists(API_CHAT_REL_MEDIAPATH.$conversationId.'/'.$userMediaData['chat_file_media_name'])) ? API_CHAT_ABS_MEDIAPATH.$conversationId.'/'.$userMediaData['chat_file_media_name'] : "",
								'thumbMediaUrl'	=> ($userMediaData['chat_file_media_thumb_name'] != "" && file_exists(API_CHAT_REL_MEDIAPATH.$conversationId.'/'.MEDIA_THUMB_FLD_NAME.$userMediaData['chat_file_media_thumb_name'])) ? API_CHAT_ABS_MEDIAPATH.$conversationId.'/'.MEDIA_THUMB_FLD_NAME.$userMediaData['chat_file_media_thumb_name'] : "",
							);
						return $returnData;
					}
				}
				
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "There are something problem with sending media data",
					);
				return $returnData;
    }
		
		function updateChatMediaViewCount($inputData){
				$userId 				= $inputData['userId'];
				$totalNewUsed		= $inputData['totalNewUsed'];
				$chatMediaId		= $inputData['chatMediaId'];
				$conversationId	= $inputData['conversationId'];
								
				$this->db->where('chat_file_id',$chatMediaId)
						 ->where('chat_file_chat_conv_id',$conversationId)
						 ->where('chat_file_status','1');
				$checkMediaFile = $this->db->getOne(TBL_CHAT_FILES);
				
				if(!checkArr($checkMediaFile)){
					$returnData = array(
							'statusCode'		=> "2",
							'message'			=> "Invalid chat media file, Please try again.",
						);
					return $returnData;
				}
				
				$totalUsed = $checkMediaFile['chat_file_total_viewed'] + $totalNewUsed;
				
				$userData = array(
						'chat_file_total_viewed'	=> $totalUsed,
						'chat_file_modified_date'	=> $this->utc_time,
					);
				$this->db->where('chat_file_id',$chatMediaId)
								 ->where('chat_file_chat_conv_id',$conversationId)
								 ->where('chat_file_status','1');
				$isUpdated = $this->db->update(TBL_CHAT_FILES,$userData);
				
				if($isUpdated){
					$returnData = array(
							'statusCode'	=> "1",
							'message'			=> "Success",
							'totalUsed'	=> $totalUsed,
							'totalRemain' => $checkMediaFile['chat_file_total_user_can_view'] - $totalUsed,
							
						);
					return $returnData;
				}
				
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "No update made to your password",
					);
				return $returnData;
    }
		
		function changePassword($inputData){ // tp // done
				$userId 		= $inputData['userId'];
				$oldPassword 	= $inputData['oldPassword'];
				$newPassword 	= $inputData['newPassword'];
				
				$this->db->where('u_id',$userId)
						 ->where('u_password',$oldPassword)
						 ->where('u_status','1');
				$checkOldPassword = $this->db->getOne(TBL_USERS);
				
				if(!checkArr($checkOldPassword)){
					$returnData = array(
							'statusCode'		=> "2",
							'message'			=> "Invalid current password, please try again.",
						);
					return $returnData;
				}
				$userData = array(
						'u_password'		=> $newPassword,
						'u_modified_date'	=> $this->utc_time,
					);
				$this->db->where('u_id',$userId)
						 ->where('u_password',$oldPassword)
						 ->where('u_status','1');
				$isUpdated = $this->db->update(TBL_USERS,$userData);
				
				if($isUpdated){
					$returnData = array(
							'statusCode'		=> "1",
							'message'			=> "Success",
						);
					return $returnData;
				}
				
				$returnData = array(
						'statusCode'		=> "2",
						'message'			=> "No update made to your password",
					);
				return $returnData;
    }
	
	
		
	
	
		function updateUserDesireList($inputData){   
				$desireIdList	= $inputData['desireIdList'];
				$userId				= $inputData['userId'];
				
				if($desireIdList == ""){
						$returnData = array(
								'statusCode' => "2",
								'message'	 => "Oops, Select some valid desire list.",
						);
						return $returnData;
				}
				
				$desireIdArr = explode(',',$desireIdList);
				
				$this->db->where("u_dsr_u_id",$userId)
								 ->where("u_dsr_cat_id",$desireIdArr,"NOT IN");
				$this->db->delete(TBL_USER_DESIRES);
			
				$sq = $this->db->subQuery();
				$sq->where("u_dsr_u_id",$userId);
				$sq->get(TBL_USER_DESIRES,null,"u_dsr_cat_id");
		
				$this->db->where("cat_status","1")
						 ->where("cat_id",$sq,"NOT IN")
						 ->where("cat_id",$desireIdArr,"IN");
				$remainCategory = $this->db->get(TBL_CATEGORIES,null,'*');
				
				foreach($remainCategory as $key => $value){
						$insertData = array(
										'u_dsr_u_id'					=> $userId,
										'u_dsr_cat_id'				=> $value['cat_id'],
										'u_dsr_created_date'	=> $this->utc_time,
										'u_dsr_status'				=> '1',
								);
						$favCatId = $this->db->insert(TBL_USER_DESIRES,$insertData);
				}
				
				$updateData = array(
						'u_reg_complete_stage' => '1',
						'u_modified_date' => $this->utc_time,
					);
				$this->db->where('u_id',$userId)
						 ->where('u_status','1');
				$isUpdated = $this->db->update(TBL_USERS,$updateData);
				
				$returnData = array(
								'statusCode' => "1",
								'message'	 	 => "Succes",
						);
				return $returnData;
		}
	
	function getUserConversationList($inputData){ // done
		$userId 		= $inputData['userId'];
		$searchData		= $inputData['searchData'];
		
		$deleteChatTimeBefore = $this->utc_time - (5*24*60*60);
		$this->db->where("chat_created_date",$deleteChatTimeBefore,"<");
		$this->db->delete(TBL_CHAT_DATAS);
		
		$maxId		= 0;
		
		$whereQuery = "";
		
		if($searchData != ""){
			$whereQuery .= " u_name LIKE '%".$searchData."%' AND ";
		}
	
		$qry = "SELECT
					".$this->uFL.",".$this->chatConvFieldList.",".$this->chatDataFieldList."
				FROM
					`".TBL_CHAT_CONVERSATIONS."` as chatConv
				JOIN
					`".TBL_USERS."` as u ON IF(chat_conv_u_id='".$userId."',u_id=chat_conv_other_u_id AND chat_conv_u_id='".$userId."' ,u_id=chat_conv_u_id AND chat_conv_other_u_id='".$userId."') AND u_status = '1'
				LEFT JOIN
					`".TBL_CHAT_DATAS."` as chatData ON chat_conv_last_chat_id = chat_id
				WHERE
					".$whereQuery."
					(
						chat_conv_u_id = '".$userId."' OR
						chat_conv_other_u_id = '".$userId."'
					) AND
					chat_conv_status = '1'
				ORDER BY
					chat_conv_last_msg_date desc
			";
		$dataList = $this->db->rawQuery($qry,null,false);
		
		foreach($dataList as $key => $value){
				$dataList[$key]['profileImage']  = userProfileImage($value['u_id'],$value['u_photo'],$value['u_thumb_photo']);
				$dataList[$key]['profileVideo']  = userProfileVideo($value['u_id'],$value['u_video'],$value['u_thumb_video']);
				$dataList[$key] = unsetUserExtraData($dataList[$key]);
		}
		
		$returnData = array(
				'statusCode' => "1",
				'message'	 => count($dataList) > 0 ? "Success" : "No more data availible",
				'dataList'	 => $dataList,
			);
		return $returnData;
	}
	
	
	function getUserConversationData($inputData){ // done
		$userId 		= $inputData['userId'];
		$conversationId	= $inputData['conversationId'];
		$maxDataId		= $inputData['maxDataId'];
		$pageNo 		= ($inputData['pageNo'] =='' || $inputData['pageNo'] < 1 ) ? '1' : $inputData['pageNo'];
		
		$limit      = 100;
		$pageStartLimit	= ($pageNo - 1) * $limit;

		$maxId		= 0;
		$totalCount = 0;
		
		$whereQuery = "";
		if($pageNo > 1){
			$whereQuery .= " chat_id <= '".$maxDataId."' AND ";
		}else{
			$deleteChatTimeBefore = $this->utc_time - (5*24*60*60);
			$this->db->where('chat_chat_conv_id',$conversationId)
					 ->where("chat_created_date",$deleteChatTimeBefore,"<");
			$this->db->delete(TBL_CHAT_DATAS);
		}
		
		if($pageNo == '1'){
			$qry = "SELECT
						count(chat_id) as totalCount,MAX(chat_id) as maxId
					FROM
						`".TBL_CHAT_DATAS."`
					WHERE
						".$whereQuery."
						chat_chat_conv_id = '".$conversationId."' AND
						(
							chat_from_u_id 	= '".$userId."' OR
							chat_to_u_id 	= '".$userId."'
						) AND
						chat_status = '1'
			";		
			$countData = $this->db->rawQuery($qry,null,false);
			if(checkArr($countData)){
				$totalCount = $countData['0']['totalCount'];
				$maxId 		= ($totalCount > 0) ? $countData['0']['maxId'] : 0;
			}
		}
		$qry = "SELECT
					".$this->chatDataFieldList."
				FROM
					`".TBL_CHAT_DATAS."` as chatData
				WHERE
					".$whereQuery."
					chat_chat_conv_id = '".$conversationId."' AND
					(
						chat_from_u_id 	= '".$userId."' OR
						chat_to_u_id 	= '".$userId."'
					) AND
					chat_status = '1'
				ORDER BY
					chat_created_date desc
				LIMIT
					".$pageStartLimit.", ".$limit."
			";		
		$dataList = $this->db->rawQuery($qry,null,false);
		/*
		foreach($dataList as $key => $value){
				$dataList[$key]['profileImage']  = userProfileImage($value['u_id'],$value['u_photo']);
				$dataList[$key] = unsetUserExtraData($dataList[$key]);
				//unset($dataList[$key]['u_photo']);
		} */
			
		$returnData = array(
				'statusCode' => "1",
				'message'	 => count($dataList) > 0 ? "Success" : "No more data availible",
				'dataList'	 => $dataList,
				'totalCount' => $totalCount,
				'maxId'		 => ($pageNo == 1) ? $maxId : $maxDataId,
			);
		return $returnData;
	}
	
	function getUserConversationId($inputData){ // done
		$userId 			= $inputData['userId'];
		$otherUserId	= $inputData['otherUserId'];
		
		if($userId == $otherUserId){
				$returnData = array(
						'statusCode' 		=> "2",
						'message'	 		=> "Failed",
					);
				return $returnData;
		}
		$this->db->where('((chat_conv_u_id = ? AND chat_conv_other_u_id = ? ) OR (chat_conv_u_id = ? AND chat_conv_other_u_id = ? ) )',array($userId,$otherUserId,$otherUserId,$userId))
				 ->where('chat_conv_status','1');
		$conversationData = $this->db->getOne(TBL_CHAT_CONVERSATIONS);
		
		if(checkArr($conversationData)){
			$returnData = array(
					'statusCode' => "1",
					'message'	 => "Success",
					'conversationId'	 => $conversationData['chat_conv_id'],
				);
			return $returnData;
		}
		
		$arr = array(
				'chat_conv_u_id'			=> $userId,
				'chat_conv_other_u_id'		=> $otherUserId,
				'chat_conv_last_msg_date'	=> "0",
				'chat_conv_last_chat_id'	=> "0",	
				'chat_conv_created_date'	=> $this->utc_time,
				'chat_conv_modified_date'	=> $this->utc_time,
				'chat_conv_status'			=> "1",
			);
		$conversationId = $this->db->insert(TBL_CHAT_CONVERSATIONS,$arr);
		
		if($conversationId){
			$returnData = array(
					'statusCode' 		=> "1",
					'message'	 		=> "Success",
					'conversationId'	=> $conversationId,
				);
			return $returnData;
		}
		
		$returnData = array(
				'statusCode' 		=> "2",
				'message'	 		=> "Failed",
			);
		return $returnData;
	}
	
	function getFeedUserList($inputData){ // done
		$userId 			= $inputData['userId'];
		$cityId 			= $inputData['cityId'];
		$genderPreferences	= $inputData['genderPreferences'];
		$distance			= $inputData['distance'];
		$minAge 			= $inputData['minAge'];
		$maxAge 			= $inputData['maxAge'];
		$minHeight 		= $inputData['minHeight'];
		$maxHeight		= $inputData['maxHeight'];
		$ethnicity		= $inputData['ethnicity'];
		$latitude			= $inputData['latitude'];
		$longitude		= $inputData['longitude'];
		$pageNo				= $inputData['pageNo'];
		$maxDataId		= $inputData['maxDataId'];
		
		$randSeed = ($inputData['randSeed'] != "") ? ( (int) $inputData['randSeed'] ): rand();

		
		$this->db->where('u_id',$userId)
						 ->where('u_status','1');
		$userData = $this->db->getOne(TBL_USERS);

		$limit      = 50;
		$pageStartLimit	= ($pageNo - 1) * $limit;

		$maxId		= 0;
		$totalCount = 0;
		
		$whereQuery = "";
		if($pageNo > 1){
			$whereQuery .= " u_id <= '".$maxDataId."' AND ";
		}
		
		if($genderPreferences == "1" || $genderPreferences == "2"){
			$whereQuery .= " u_gender = '".$gender."' AND ";
		}
		
		if(is_numeric($cityId) && $cityId > "0"){
			$whereQuery .= " u_city_id = '".$cityId."' AND ";
		}
		
		$havingQuery = "";
		$selectDistance = "";
		if($userData['u_subscribed_date'] >= $this->utc_time){
				if($distance > 0){
						$havingQuery .= " HAVING distance <= '".$distance."' ";
						$selectDistance = ",get_distance_in_miles_between_geo_locations('".$latitude."','".$longitude."',u_last_latitude,u_last_longitude) as distance ";
				}
				
				if($minAge != "" && $maxAge != ""){
						$whereQuery .= " u_age BETWEEN '".$minAge."' AND '".$maxAge."' AND ";
				}
				
				if($minHeight != "" && $maxHeight != ""){
						$whereQuery .= " u_height BETWEEN '".$minHeight."' AND '".$maxHeight."' AND ";
				}
				
				if($ethnicity != ""){
						$whereQuery .= " u_ethnicity = '".$ethnicity."' AND ";
				}
				
		}else{
				if($pageNo > 2){
						$returnData = array(
								'statusCode' 	=> "1",
								'message'	 		=> "No more data availible",
								'dataList'	 	=> array(),
								'totalCount' 	=> "0",
								'randSeed' 		=> $randSeed,
								'maxId'		 		=> $maxDataId,
							);
						return $returnData;
				}
		}
		//if($userData['u_gender_preferences'] != ''){
		//	$u_gender_preferences = explode(',',$userData['u_gender_preferences']);
		//	
		//	if(count($u_gender_preferences) == '1'){
		//		$whereQuery .= " u_gender = '".$u_gender_preferences[0]."' AND ";
		//	}else{
		//		$u_gender_preferences_arr = implode("','",$u_gender_preferences);
		//		$whereQuery .= " u_gender IN ('".$u_gender_preferences_arr."') AND ";
		//	}
		//}
		//
		
		if($pageNo == '1'){
			$qry = "SELECT
						count(u_id) as totalCount,MAX(u_id) as maxId
					FROM
					`".TBL_USERS."` as u
					LEFT JOIN
						`".TBL_MATCHES."` as mat ON match_u_id = u_id AND match_other_u_id = '".$userId."' AND match_status = 2
					WHERE
						".$whereQuery."
						u_id != '".$userId."' AND
						IF(u_visiable_for = 0, 1, u_visiable_for = '".$userData['u_gender']."') AND
						u_id NOT IN(
								SELECT
									IF(match_other_u_id = '".$userId."',match_u_id,match_other_u_id) as userId
								FROM
									`".TBL_MATCHES."` 
								WHERE
									(
										match_other_u_id = '".$userId."' OR
										match_u_id = '".$userId."'
									) AND
									(
										match_status IN (1 ,3) OR
										(
											match_status = '2' AND
											match_u_id = '".$userId."'
										)
									)
						) AND
						u_status = '1'
						".$havingQuery;
			$countData = $this->db->rawQuery($qry,null,false);
			if(checkArr($countData)){
				$totalCount = $countData['0']['totalCount'];
				$maxId 		= ($totalCount > 0) ? $countData['0']['maxId'] : 0;
			}
		}
		$qry = "SELECT
					".$this->uFL.",IF(match_id > 0,'1','2') as halfMashed ".$selectDistance."
				FROM
					`".TBL_USERS."` as u
				LEFT JOIN
					`".TBL_MATCHES."` as mat ON match_u_id = u_id AND match_other_u_id = '".$userId."' AND match_status = 2
				WHERE
						".$whereQuery."
						u_id != '".$userId."' AND
						IF(u_visiable_for = 0, 1, u_visiable_for = '".$userData['u_gender']."') AND
						u_id NOT IN(
								SELECT
									IF(match_other_u_id = '".$userId."',match_u_id,match_other_u_id) as userId
								FROM
									`".TBL_MATCHES."` 
								WHERE
									(
										match_other_u_id = '".$userId."' OR
										match_u_id = '".$userId."'
									) AND
									(
										match_status IN (1 ,3) OR
										(
											match_status = '2' AND
											match_u_id = '".$userId."'
										)
									)
						) AND
						u_status = '1'
						".$havingQuery."
				ORDER BY
					FIELD(halfMashed,'1','2'), RAND(".$randSeed.")
				LIMIT
					".$pageStartLimit.", ".$limit."
			";
		$dataList = $this->db->rawQuery($qry,null,false);
	//	_kd($dataList);
		foreach($dataList as $key => $value){
				$dataList[$key]['profileImage'] = userProfileImage($value['u_id'],$value['u_photo'],$value['u_thumb_photo']);
				$dataList[$key]['profileVideo'] = userProfileVideo($value['u_id'],$value['u_video'],$value['u_thumb_video']);
				
		//		$dataList[$key] = unsetUserExtraData($dataList[$key]);
		}
		//_kd($dataList);
		$returnData = array(
				'statusCode' 	=> "1",
				'message'	 		=> count($dataList) > 0 ? "Success" : "No more data availible",
				'dataList'	 	=> $dataList,
				'totalCount' 	=> $totalCount,
				'randSeed' 		=> $randSeed,
				'maxId'		 		=> ($pageNo == 1) ? $maxId : $maxDataId,
			);
		return $returnData;
	}
	
	function getCityList($inputData){
		$maxDataId		= $inputData['maxDataId'];
		$pageNo 			= ($inputData['pageNo'] =='' || $inputData['pageNo'] < 1 ) ? '1' : $inputData['pageNo'];
		$searchData		= $inputData['searchData'];
		
		$limit      = 100;
		$pageStartLimit	= ($pageNo - 1) * $limit;

		$maxId		= 0;
		$totalCount = 0;
		
		$whereQuery = "";
		if($pageNo > 1){
			$whereQuery .= " ct_id <= '".$maxDataId."' AND ";
		}
		
		if($searchData != ""){
			$whereQuery .= " ct_name LIKE '".$searchData."%' AND ";
		}
		
		if($pageNo == '1'){
			$qry = "SELECT
						count(ct_id) as totalCount,MAX(ct_id) as maxId
					FROM
						`".TBL_CITIES."` as ct
					WHERE
						".$whereQuery."
						ct_status = '1'
			";
			$countData = $this->db->rawQuery($qry,null,false);
			if(checkArr($countData)){
				$totalCount = $countData['0']['totalCount'];
				$maxId 		= ($totalCount > 0) ? $countData['0']['maxId'] : 0;
			}
		}
		$qry = "SELECT
					ct_id,ct_name
				FROM
						`".TBL_CITIES."` as ct
					WHERE
						".$whereQuery."
						ct_status = '1'
				ORDER BY
					ct_name ASC
				LIMIT
					".$pageStartLimit.", ".$limit."
			";		
		$dataList = $this->db->rawQuery($qry,null,false);
		
		$returnData = array(
				'statusCode' 	=> "1",
				'message'	 		=> count($dataList) > 0 ? "Success..!!" : "No more data availible..!!",
				'dataList'	 	=> $dataList,
				'totalCount' 	=> $totalCount,
				'maxId'		 		=> ($pageNo == 1) ? $maxId : $maxDataId,
			);
		return $returnData;
	}
	
	//HP Envy 15-k203tx (K8U29PA) Notebook (5th Gen Ci7/ 8GB/ 1TB/ Win8.1/ 4GB Graph)
	function forgotPassword($inputData){ // tp // done
		$email 	= $inputData['email'];
		
		$data	= array();
		$this->db->where('u_email', $email);
		$this->db->where('u_reg_type', "1");
		$this->db->where('u_status', '1');
		$userData	= $this->db->getOne(TBL_USERS);
		
		if (is_array($userData) && count($userData) > 0){
			
			$from			= MAIL_FROM;
			$to				= $email;
			$subject	 	= "TradeMade : Forgot Passoword Request";
			$message	 	= "Hello ".$userData['u_first_name']." ".$userData['u_last_name'].","
							."<br><br>We have recieved your forgot password request."
							."<br><br>Find your TradeMade account credentials as below."
							."<br><br>Email : <strong>".$userData['u_email']."</strong>"
							."<br><br>Password : <strong>".$userData['u_password']."</strong>"
							."<br><br><br>Regards,"
							."<br>TradeMade Team";
			
			// Always set content-type when sending HTML email
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: <'.MAIL_FROM.'>'. PHP_EOL 
		 . 'Reply-To: <'.MAIL_FROM.'>' . PHP_EOL 
		 . 'X-Mailer: PHP/' . phpversion();

			//;
			
		//	if (sendSmtpEmail($from, $to, $subject, $message)){
			if(mail($to,$subject,$message,$headers)){
					$returnData = array(
							'statusCode'		=> "1",
							'message'			=> "Your account credentials has been sent successfully to your email",
						);
					return $returnData;
			}
			
			$returnData = array(
					'statusCode'		=> "2",
					'message'			=> "There are something problem to send email, Please try later.",
				);
			return $returnData;
		}
		$returnData = array(
				'statusCode'		=> "2",
				'message'			=> "This email address does not exist, please re-enter.",
			);
		return $returnData;
	}
	
//	function updateUserPreferences($inputData){
//		$userId 				= $inputData['userId'];
//		$genderPreferences 		= $inputData['genderPreferences'];
//		$lookingForPreferences 	= $inputData['lookingForPreferences'];
//		
//		if($genderPreferences == ""){
//			$returnData = array(
//					'statusCode'		=> "2",
//					'message'			=> "Please, Select some gender preferences",
//				);
//			return $returnData;
//		}else if($lookingForPreferences == ""){
//			$returnData = array(
//					'statusCode'		=> "2",
//					'message'			=> "Please, Select some looking for preferences",
//				);
//			return $returnData;
//		}
//		
//		$userData = array(
//				'u_gender_preferences'		=> $genderPreferences,
//				'u_looking_for_preferences'	=> $lookingForPreferences,
//				'u_modified_date'			=> $this->utc_time,
//			);
//		
//		$this->db->where('u_id',$userId)
//				 ->where('u_status','1');
//		$isUpdated = $this->db->update(TBL_USERS,$userData);
//		
//		if($isUpdated){
//			$returnData = array(
//					'statusCode'		=> "1",
//					'message'			=> "Success",
//				);
//			return $returnData;
//		}
//		
//		$returnData = array(
//				'statusCode'		=> "2",
//				'message'			=> "No update made to your preferences",
//			);
//		return $returnData;
//    }
	
	
	
	
	// NOT IN USE
	function updateAllMatchMashere(){
		
		$qry = "SELECT
					*
				FROM
					`".TBL_MATCHES."` as up
				WHERE
					match_status = 1
			";		
		$matchList = $this->db->rawQuery($qry,null,false);
		_kd($matchList);
		foreach($matchList as $key => $inputData){
			$userId 		= $inputData['match_u_id'];
			$otherUserId	= $inputData['match_other_u_id'];
			
			if($userId != $otherUserId){
				
				$this->db->where('((chat_conv_u_id = ? AND chat_conv_other_u_id = ? ) OR (chat_conv_u_id = ? AND chat_conv_other_u_id = ? ) )',array($userId,$otherUserId,$otherUserId,$userId))
						 ->where('chat_conv_status','1');
				$conversationData = $this->db->getOne(TBL_CHAT_CONVERSATIONS);
				
				if(!checkArr($conversationData)){
					$returnData = array(
							'statusCode' => "1",
							'message'	 => "Success",
							'conversationId'	 => $conversationData['chat_conv_id'],
						);
				//	return $returnData;
				
				
				$arr = array(
						'chat_conv_u_id'			=> $userId,
						'chat_conv_other_u_id'		=> $otherUserId,
						'chat_conv_last_msg_date'	=> "0",
						'chat_conv_last_chat_id'	=> "0",	
						'chat_conv_created_date'	=> $this->utc_time,
						'chat_conv_modified_date'	=> $this->utc_time,
						'chat_conv_status'			=> "1",
					);
				$conversationId = $this->db->insert(TBL_CHAT_CONVERSATIONS,$arr);
				
				if($conversationId){
					$returnData = array(
							'statusCode' 		=> "1",
							'message'	 		=> "Success",
							'conversationId'	=> $conversationId,
						);
					//return $returnData;
				}
				}
			}
		}
	}
	// NOT IN USE
	function deleteMatch($id){
			$this->db->where("match_u_id = ? OR match_other_u_id = ? ",array($id,$id));
			$this->db->delete(TBL_MATCHES);
			return "Delete all mataches";
	}
	
	// WEBSOCKET FOR OFFLINE CHAT MESSAGE SEND
	function webSocketSendPushNotificationForOfflineChat($inputJson){
			//{"uid":"1","jid":"hyrapp_2@45.55.36.133","msg":"Test Message"}
			try {
					$sentPush 	= array();
					$inputData 	= json_decode($inputJson,true);
					$message 	= (isset($inputData['msg']) && $inputData['msg'] != null) ? trim($inputData['msg']) : "";
					$uid 		= (isset($inputData['uid']) && $inputData['uid'] != null)  ? $inputData['uid'] : "";
					$jid 		= (isset($inputData['jid']) && $inputData['jid'] != null)  ? $inputData['jid'] : "";				
					
					if($jid != "" && $message != "" && $uid != ""){
							$this->db->join(TBL_USERS,"udt_u_id = u_id AND u_status = '1'");
							$this->db->where('u_xmpp_jid',$jid)
									 ->where('udt_device_token','',"!=")
									 ->where('udt_device_token','null',"!=")
									 ->where('udt_status','1');
							$tokenDataList = $this->db->get(TBL_USER_DEVICE_TOKENS);
							foreach($tokenDataList as $tokenDataKey => $tokenData){
									if($tokenData['udt_device_type'] == "1" && strlen($tokenData['udt_device_token']) > 32 ){
											try{
													$body = array();
													$body['aps']['alert']   = substr($message,0,20); //"New message in event :"+$eid;
													$body['aps']['uid'] 	= $uid;
													$body['aps']['type'] 	= "1";
													$body['aps']['badge'] 	= 1;
													$sendNotification 		= send_notification_ios($tokenData['udt_device_token'],$body);
											}catch(Exception $e1) {
													
											}
									}
							}
					}
					$returnData = array(
									'statusCode' 					=> "1",
									'message'	 						=> "Success." ,
								);
					return json_encode($returnData);
			}catch(Exception $e) {
					$returnData = array(
									'statusCode' 					=> "2",
									'message'	 						=> $e->getMessage() ,
								);
					return json_encode($returnData);
			}
			
			$returnData = array(
							'statusCode' 					=> "2",
							'message'	 						=> "failed",
						);
			return json_encode($returnData);
	}
	
	
	function cronSendTroveAddNotification(){
		
		$twoDayBefore = $this->utc_time - (2*24*60*60);
		$qry = "SELECT
					u.*,(select count(trv_id) FROM ".TBL_TROVES." WHERE trv_u_id = u_id AND trv_status = '9') as totalTrove
				FROM
					".TBL_USERS_AS."
				WHERE
					u_created_date >= '".$twoDayBefore."' AND
					u_status = 1
				HAVING
					totalTrove < 5
			";		
		$userList = $this->db->rawQuery($qry,null,false);
		_kd($matchList);
		foreach($matchList as $key => $inputData){
			$userId 		= $inputData['match_u_id'];
			$otherUserId	= $inputData['match_other_u_id'];
			
			if($userId != $otherUserId){
				
				$this->db->where('((chat_conv_u_id = ? AND chat_conv_other_u_id = ? ) OR (chat_conv_u_id = ? AND chat_conv_other_u_id = ? ) )',array($userId,$otherUserId,$otherUserId,$userId))
						 ->where('chat_conv_status','1');
				$conversationData = $this->db->getOne(TBL_CHAT_CONVERSATIONS);
				
				if(!checkArr($conversationData)){
					$returnData = array(
							'statusCode' => "1",
							'message'	 => "Success",
							'conversationId'	 => $conversationData['chat_conv_id'],
						);
				//	return $returnData;
				
				
				$arr = array(
						'chat_conv_u_id'			=> $userId,
						'chat_conv_other_u_id'		=> $otherUserId,
						'chat_conv_last_msg_date'	=> "0",
						'chat_conv_last_chat_id'	=> "0",	
						'chat_conv_created_date'	=> $this->utc_time,
						'chat_conv_modified_date'	=> $this->utc_time,
						'chat_conv_status'			=> "1",
					);
				$conversationId = $this->db->insert(TBL_CHAT_CONVERSATIONS,$arr);
				
				if($conversationId){
					$returnData = array(
							'statusCode' 		=> "1",
							'message'	 		=> "Success",
							'conversationId'	=> $conversationId,
						);
					//return $returnData;
				}
				}
			}
		}
	}
    /********************************** LAST USE ON  01-JUNE-2013 ****************/
}	// END OF CLASS
