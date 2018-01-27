<?php
    // Jay Guru Maharaj
    // START:17-12-2015
    require_once('../../config/1_0/config.php');
    
    // COMMON FUNCTION AND LIBRARIES INCLUDED IN COMMON.PHP
    require_once('../../config/1_0/common.php');
  
    includeClasses('1_0/common');
    $common     = new Common();
    
    if(isset($_GET['delMatch']) && is_numeric($_GET['delMatch']) && $_GET['delMatch'] > 0){
        echo $common->deleteMatch($_GET['delMatch']);
        exit;
    }
    $utc_time       = time();
    $json_data      = array();
    $api_function   = urldecode( (isset($_POST['apiName']) && $_POST['apiName'] != '') ? $_POST['apiName'] : '');
    
    $logPostArr = isset($_POST) && is_array($_POST) && count($_POST) > 0 ? $_POST : array();
    $logGetArr  = isset($_GET) && is_array($_GET) && count($_GET) > 0 ? $_GET : array();
    $logFileArr  = isset($_FILES) && is_array($_FILES) && count($_FILES) > 0 ? $_FILES : array();
    $common->requestLogData($api_function,$logPostArr,$logGetArr,$logFileArr);
    
	// app send these parameters in all apies
	$longitude    = getPostVar('longitude');
    $latitude     = getPostVar('latitude');
	
    switch($api_function)
    {
        case 'loginWithSocialData':	// tp // done
            
                $socialId           = getPostVar('socialId');
                $firstName          = getPostVar('firstName');	
				$lastName           = getPostVar('lastName');
                $email              = getPostVar('email');
				$latitude          	= getPostVar('latitude');
				$longitude          = getPostVar('longitude');
				$uniqueToken        = getPostVar('uniqueToken');
                $deviceToken        = getPostVar('deviceToken');
                $deviceType         = getPostVar('deviceType');
                $profileImage       = getFileVar('profileImage'); 
                
                if(empty($uniqueToken) || $uniqueToken == null){
                    $json_data = array(
                            'statusCode'	=> '2',
                            'message'	    => "Oops,Unique token should not be blank!",
                        );
                    break;
                }
                
                $userData = array(
                        'u_first_name'      => $firstName,
												'u_last_name'       => $lastName,
                        'u_email'           => $email,
												'u_password'        => "",
                        'u_xmpp_username'   => "",
                        'u_xmpp_jid'        => "",
                        'u_xmpp_password'   => "",
                        'u_social_id'       => $socialId,
                        'u_reg_type'        => "2",
						'u_photo'           => "",
						'u_thumb_photo'     => "",
						'u_zip_code'				=> "",
						'u_address'					=> "",
						'u_reg_complete_stage'		=> "0",
						'u_latitude' 	  => $latitude,
						'u_longitude'	  => $longitude,
						'u_total_allow_trove'     => "29",
                        'u_created_date'    => $utc_time,            
                        'u_modified_date'   => $utc_time,
                        'u_status'          => "1",
                    );
      
                $tokenData = array(
                        'udt_u_id'              => "",
                        'udt_unique_token'      => $uniqueToken,
                        'udt_security_token'    => "",
                        'udt_device_token'      => $deviceToken,
                        'udt_device_type'       => $deviceType,
                        'udt_created_date'      => $utc_time,
                        'udt_modified_date'     => $utc_time,
                        'udt_status'            => "1",
                    );
                $inputData = array(
                        'userData'      => $userData,
                        'tokenData'     => $tokenData,
                        'profileImage'  => $profileImage,
                    );
                $json_data = $common->loginWithSocialData($inputData);
                
            break;
        
        case 'signup': // tp // done
                $firstName          = getPostVar('firstName');	
								$lastName           = getPostVar('lastName');
                $email              = getPostVar('email');
				        $password           = getPostVar('password');
								$zipCode           	= getPostVar('zipCode');
								$address           	= getPostVar('address');
								$latitude          	= getPostVar('latitude');
								$longitude          = getPostVar('longitude');
								$uniqueToken        = getPostVar('uniqueToken');
                $deviceToken        = getPostVar('deviceToken');
                $deviceType         = getPostVar('deviceType');
                $profileImage       = getFileVar('profileImage');
								
                if(empty($uniqueToken) || $uniqueToken == null){
                    $json_data = array(
                            'statusCode'	=> '2',
                            'message'	    => "Unique token should not be blank!",
                        );
                    break;
                }
                
                if($password == ""){
                    $json_data = array(
                            'statusCode'	=> '2',
                            'message'	    => "Password should not be blank",
                        );
                    break;
                }
                
                $userData = array(
                        'u_first_name'      => $firstName,
												'u_last_name'       => $lastName,
                        'u_email'           => $email,
												'u_password'        => $password,
                        'u_xmpp_username'   => "",
                        'u_xmpp_jid'        => "",
                        'u_xmpp_password'   => "",
                        'u_social_id'       => "",
                        'u_reg_type'        => "1",
												'u_photo'           => "",
												'u_thumb_photo'     => "",
												'u_zip_code'				=> $zipCode,
												'u_address'					=> $address,
												'u_latitude'				=> $latitude,
												'u_longitude'				=> $longitude,
												'u_reg_complete_stage'		=> "0",
						'u_total_allow_trove'     => "29",
                        'u_created_date'    => $utc_time,            
                        'u_modified_date'   => $utc_time,
                        'u_status'          => "1",
                    );
                    
                $tokenData = array(
                        'udt_u_id'              => "",
                        'udt_unique_token'      => $uniqueToken,
                        'udt_security_token'    => "",
                        'udt_device_token'      => $deviceToken,
                        'udt_device_type'       => $deviceType,
                        'udt_created_date'      => $utc_time,
                        'udt_modified_date'     => $utc_time,
                        'udt_status'            => "1",
                    );
                $inputData = array(
                        'userData'  		=> $userData,
                        'tokenData' 		=> $tokenData,
                        'profileImage'  => $profileImage,
                    );
                $json_data = $common->signup($inputData);
                
            break;
        
        case 'login':  // tp // done
                $email              = getPostVar('email');
				        $password           = getPostVar('password');
								$uniqueToken        = getPostVar('uniqueToken');
                $deviceToken        = getPostVar('deviceToken');
                $deviceType         = getPostVar('deviceType');
                 
                if(empty($uniqueToken) || $uniqueToken == null){
                    $json_data = array(
                            'statusCode'	=> '2',
                            'message'	    => "Unique token should not be blank!",
                        );
                    break;
                }
                
                $tokenData = array(
                        'udt_u_id'              => "",
                        'udt_unique_token'      => $uniqueToken,
                        'udt_security_token'    => "",
                        'udt_device_token'      => $deviceToken,
                        'udt_device_type'       => $deviceType,
                        'udt_created_date'      => $utc_time,
                        'udt_modified_date'     => $utc_time,
                        'udt_status'            => "1",
                    );
                $inputData = array(
                        'email'     => $email,
                        'password'  => $password,
                        'tokenData' => $tokenData,
						'latitude' 	=> $latitude,
						'longitude' => $longitude,
                    );
                $json_data = $common->login($inputData);
                
            break;
				
				case 'updateUserProfile': // tp // done
								$userId					= getPostVar('userId');
								$firstName			= getPostVar('firstName');
								$lastName				= getPostVar('lastName');
								$zipCode				= getPostVar('zipCode');
								$address				= getPostVar('address');
								$regCompleteFlage= getPostVar('regCompleteFlage');
								$profileImage		= getFileVar('profileImage');
								$securityToken	= getPostVar('securityToken');
								
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> "9",
                            'message'	    => "Access denied.",
                        );
                    break;
                }
              
				
				$userData = array(
						'u_modified_date'	=> $utc_time
					);
				if($firstName != ""){
					$userData['u_first_name'] = $firstName;
				}
				if($lastName != ""){
					$userData['u_last_name'] = $lastName;
				}
				if($zipCode != ""){
					$userData['u_zip_code'] = $zipCode;
				}
				if($address != ""){
					$userData['u_address'] = $address;
				}
				if($regCompleteFlage != ""){
					$userData['u_reg_complete_stage'] = $regCompleteFlage;
				}
				
                $inputData = array(
                        'userId'        => $userId,
						'securityToken' => $securityToken,
                        'userData'      => $userData,
                        'profileImage'  => $profileImage,
                    );
                $json_data = $common->updateUserProfile($inputData);
                
            break;
				
				case 'getUserProfile': // tp // done
								$userId					= getPostVar('userId');
								$otherUserId		= getPostVar('otherUserId');
								$securityToken	= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'      => $userId,
                        'otherUserId' => $otherUserId,
                    );
                $json_data      = $common->getUserProfile($inputData);
                
            break;
		
		
			case 'unblockUser': // tp // done
								$userId					= getPostVar('userId');
								$otherUserId		= getPostVar('otherUserId');
								$securityToken	= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'      => $userId,
                        'otherUserId' => $otherUserId,
                    );
                $json_data      = $common->unblockUser($inputData);
                
            break; 
		
			case 'reportUser': // tp // done
								$userId					= getPostVar('userId');
								$otherUserId		= getPostVar('otherUserId');
								$securityToken	= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'      => $userId,
                        'otherUserId' => $otherUserId,
                    );
                $json_data      = $common->reportUser($inputData);
                
            break;

			case 'reportTrove': // tp // done
								$userId					= getPostVar('userId');
								$troveId		= getPostVar('troveId');
								$securityToken	= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'      => $userId,
                        'troveId' => $troveId,
                    );
                $json_data      = $common->reportTrove($inputData);
                
            break;
		
			case 'troveSlotPurchase': // tp // done
				$userId				= getPostVar('userId');
				$newSlotCount		= getPostVar('newSlotCount');
				$purchaseJsonData	= getPostVar('purchaseJsonData');
				$securityToken		= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'      => $userId,
                        'newSlotCount' => $newSlotCount,
						'purchaseJsonData' => $purchaseJsonData,
                    );
                $json_data      = $common->troveSlotPurchase($inputData);
                
            break;
		
			
			case 'updateLastTradeSeen': // tp // done
				$userId				= getPostVar('userId');
				$badgeCount			= getPostVar('badgeCount');
				$lastMadeTradeId	= getPostVar('lastMadeTradeId');
				$lastReceiveTradeId	= getPostVar('lastReceiveTradeId');
				$securityToken		= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'      => $userId,
                        'lastMadeTradeId' => $lastMadeTradeId,
						'lastReceiveTradeId' => $lastReceiveTradeId,
						'badgeCount' => $badgeCount,
                    );
                $json_data      = $common->updateLastTradeSeen($inputData);
                
            break;
		
			case 'getUnseenTradeCount': // tp // done
				$userId				= getPostVar('userId');
				$securityToken		= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'      => $userId,
                    );
                $json_data      = $common->getUnseenTradeCount($inputData);
                
            break;  
				
				 case 'forgotPassword':	// tp // done
                $email              = ( isset($_POST['email']) && $_POST['email'] != "" ? $db->escape($_POST['email']) : '');
                
                $inputData = array(
                        'email'         => $email,
                    );
                
                $json_data      = $common->forgotPassword($inputData);
                
            break;
        
        case 'logout': // tp
                $userId         = ( isset($_POST['userId']) && $_POST['userId'] != "" ? $db->escape($_POST['userId']) : '');
                $securityToken  = ( isset($_POST['securityToken']) && $_POST['securityToken'] != "" ? $db->escape($_POST['securityToken']) : '');
                
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
                        'securityToken' => $securityToken,
                    );
                $json_data      = $common->logout($inputData);
                
            break;
        
				case 'changePassword': // tp // done
								$userId          = getPostVar('userId');
								$oldPassword     = getPostVar('oldPassword');
								$newPassword     = getPostVar('newPassword');
								$securityToken   = getPostVar('securityToken');
								
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'         => $userId,
                        'oldPassword'    => $oldPassword,
                        'newPassword'    => $newPassword,
                    );
                $json_data      = $common->changePassword($inputData);
                
            break;
				
        case 'getUserConversationList': // tp
                $userId         = ( isset($_POST['userId']) && $_POST['userId'] != "" ? $db->escape($_POST['userId']) : '');
                $searchData     = ( isset($_POST['searchData']) && $_POST['searchData'] != "" ? $db->escape($_POST['searchData']) : '');
                $securityToken  = ( isset($_POST['securityToken']) && $_POST['securityToken'] != "" ? $db->escape($_POST['securityToken']) : '');
                
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
                        'searchData'        => $searchData,
                    );
                
                $json_data      = $common->getUserConversationList($inputData);
                
            break;
        
        case 'getUserConversationData':	// tp
                $userId         = ( isset($_POST['userId']) && $_POST['userId'] != "" ? $db->escape($_POST['userId']) : '');
                $conversationId = ( isset($_POST['conversationId']) && $_POST['conversationId'] != "" ? $db->escape($_POST['conversationId']) : '');
                $pageNo         = ( isset($_POST['pageNo']) && $_POST['pageNo'] != "" ? $db->escape($_POST['pageNo']) : '');
                $maxDataId      = ( isset($_POST['maxDataId']) && $_POST['maxDataId'] != "" ? $db->escape($_POST['maxDataId']) : '');
                $securityToken  = ( isset($_POST['securityToken']) && $_POST['securityToken'] != "" ? $db->escape($_POST['securityToken']) : '');
                
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
                        'conversationId'=> $conversationId,
                        'pageNo'        => $pageNo,
                        'maxDataId'     => $maxDataId,
                    );
                
                $json_data      = $common->getUserConversationData($inputData);
                
            break; 
						
				 case 'uploadChatMediaData':
								$userId          = getPostVar('userId');
								$conversationId	 = getPostVar('conversationId');
								$mediaType   	   = getPostVar('mediaType','1');
								$mediaData	 	   = getFileVar('mediaData');
								$securityToken   = getPostVar('securityToken');
								
                
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'            => $userId,
                        'conversationId'    => $conversationId,
                        'mediaData'         => $mediaData,
                        'mediaType'         => $mediaType,
                    );
                $json_data = $common->uploadChatMediaData($inputData);
                
            break;
				
        case 'getUserConversationId':	// tp
								$userId          = getPostVar('userId');
								$otherUserId     = getPostVar('otherUserId');
								$securityToken   = getPostVar('securityToken');
								
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
                        'otherUserId'   => $otherUserId,
                    );
                
                $json_data      = $common->getUserConversationId($inputData);
                
            break;
				case 'updateFacebookFriends':  // tp //done 
								$userId					= getPostVar('userId');
								$frdFbIdList		= getPostVar('frdFbIdList');
								$securityToken	= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
                        'frdFbIdList' 	=> $frdFbIdList,
                    );
                $json_data      = $common->updateFacebookFriends($inputData);
                
            break;
				case 'updateUserDesireList':  // tp //done 
								$userId					= getPostVar('userId');
								$desireIdList		= getPostVar('desireIdList');
								$securityToken	= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
                        'desireIdList' 	=> $desireIdList,
                    );
                $json_data      = $common->updateUserDesireList($inputData);
                
            break;
				
				
					
				case 'getCategoryList': // tp // done 
								$userId          = getPostVar('userId');
								$type          		= getPostVar('type','0');
								$securityToken   = getPostVar('securityToken');
								
								$isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
								if(!$isValid){
									$json_data = array(
											'statusCode'	=> '9',
											'message'	    => "Access denied..!!",
										);
									break;
								}
								
								$inputData = array(
										'userId'  => $userId,
										'type'  => $type,
									);
								
								$json_data      = $common->getCategoryList($inputData);
						break;
				
				case 'getMaterialList': // tp // done
								$userId          = getPostVar('userId');
								$securityToken   = getPostVar('securityToken');
								
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'  => $userId,
								    );
                
                $json_data      = $common->getMaterialList();
						break;
								
				case 'addNewTrove': //tp // done
								$userId					= getPostVar('userId');
								$troveType			= getPostVar('troveType');
								$categoryId			= getPostVar('categoryId');
								$priceRange			= getPostVar('priceRange');
								$quality				= getPostVar('quality');
								$materialId			= getPostVar('materialId');
								$desc						= getPostVar('desc');
								$troveImage_1		= getFileVar('troveImage_1');
								$troveImage_2		= getFileVar('troveImage_2');
								$troveImage_3		= getFileVar('troveImage_3');
								$securityToken	= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
								$troveData	= array(
												'trv_u_id'						=> $userId,
												'trv_type'						=> $troveType,
												'trv_cat_id'					=> $categoryId,
												'trv_price_range'			=> $priceRange,
												'trv_quality'					=> ($troveType == '1') ? $quality : "0" ,
												'trv_mtl_id'					=> ($troveType == '1') ? $materialId : "0",
												'trv_desc'						=> $desc,
												'trv_photo'						=> "",
												'trv_thumb_photo'			=> "",
												'trv_created_date'		=> $utc_time,
												'trv_modified_date'		=> $utc_time,
												'trv_status'				 	=> "1",
										);
								$troveImageList = array();
								if(checkArr($troveImage_1)){
										$troveImageList[] =  $troveImage_1;
								}
								if(checkArr($troveImage_2)){
										$troveImageList[] =  $troveImage_2;
								}
								if(checkArr($troveImage_3)){
										$troveImageList[] =  $troveImage_3;
								}
                $inputData = array(
                        'userId'      => $userId,
                        'troveData' 	=> $troveData,
												'troveImageList'  => $troveImageList,
                    );
                $json_data      = $common->addNewTrove($inputData);
            break;
				
				
				case 'removeTrove': // tp // done
								$userId					= getPostVar('userId');
								$troveId				= getPostVar('troveId');
								$securityToken	= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'      => $userId,
                        'troveId' 		=> $troveId,
                    );
                $json_data      = $common->removeTrove($inputData);
            break;
				
				case 'getUserTroveList': // tp // done
								$userId					= getPostVar('userId');
								$otherUserId		= getPostVar('otherUserId');
								$securityToken	= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'      		=> $userId,
                        'otherUserId' 		=> $otherUserId,
                    );
                $json_data      = $common->getUserTroveList($inputData);
            break;
				
				case 'getTroveDetail': 	// tp
								$userId					= getPostVar('userId');
								$troveId				= getPostVar('troveId');
								$securityToken	= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'      		=> $userId,
                        'troveId' 				=> $troveId,
                    );
                $json_data      = $common->getTroveDetail($inputData);
            break; 
            
            case 'removeTroveTradeBanner': 	// tp
                                    $userId					= getPostVar('userId');
                                    $troveId				= getPostVar('troveId');
                                    $securityToken	= getPostVar('securityToken');

                                    $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                                    if(!$isValid){
                                        $json_data = array(
                                                'statusCode'	=> '9',
                                                'message'	    => "Access denied..!!",
                                            );
                                        break;
                                    }

                                    $inputData = array(
                                            'userId'      		=> $userId,
                                            'troveId' 				=> $troveId,
                                        );
                                    $json_data      = $common->removeTroveTradeBanner($inputData);
                                break; 
            
				
				case 'purposeTrade': // tp // done
								$userId								= getPostVar('userId');
								$otherUserId					= getPostVar('otherUserId');
								$desiredTroveIdList		= getPostVar('desiredTroveIdList');
								$purposedTroveIdList	= getPostVar('purposedTroveIdList');
								$securityToken				= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'      				 => $userId,
                        'otherUserId' 				 => $otherUserId,
												'desiredTroveIdList' 	 => $desiredTroveIdList,
												'purposedTroveIdList'  => $purposedTroveIdList,
                    );
                $json_data      = $common->purposeTrade($inputData);
            break;
				
				case 'counterTrade': // tp // done 
								$tradeId							= getPostVar('tradeId');
								$userId								= getPostVar('userId');
								$otherUserId					= getPostVar('otherUserId');
								$desiredTroveIdList		= getPostVar('desiredTroveIdList');
								$purposedTroveIdList	= getPostVar('purposedTroveIdList');
								$securityToken				= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
												'tradeId'      				 => $tradeId,
                        'userId'      				 => $userId,
                        'otherUserId' 				 => $otherUserId,
												'desiredTroveIdList' 	 => $desiredTroveIdList,
												'purposedTroveIdList'  => $purposedTroveIdList,
                    );
                $json_data      = $common->counterTrade($inputData);
            break;
				
				case 'moderateTrade': // tp // done
								$tradeId				= getPostVar('tradeId');
								$userId					= getPostVar('userId');
								$status					= getPostVar('status');
								$securityToken	= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
												'tradeId'  	=> $tradeId,
												'status'  	=> $status,
                        'userId'   	=> $userId,
                    );
                $json_data      = $common->moderateTrade($inputData);
            break;
				
				case 'cancelTrade': // tp // done
								$tradeId				= getPostVar('tradeId');
								$userId					= getPostVar('userId');
								$securityToken	= getPostVar('securityToken');
						    
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
												'tradeId'  	=> $tradeId,
                        'userId'   	=> $userId,
                    );
                $json_data      = $common->cancelTrade($inputData);
            break;
				
				case 'getSentTradeList': // tp // done
								$userId          = getPostVar('userId');
								$pageNo     		 = getPostVar('pageNo','1');
								$maxDataId    	 = getPostVar('maxDataId');
								$securityToken   = getPostVar('securityToken');
								
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
                        'pageNo'        => (is_numeric($pageNo) && $pageNo > 0) ? $pageNo : "1" ,
								        'maxDataId'     => $maxDataId,
								    );
                
                $json_data    = $common->getSentTradeList($inputData);
                
            break;
				
				case 'getReceivedTradeList':  // tp // done
								$userId          = getPostVar('userId');
								$pageNo     		 = getPostVar('pageNo','1');
								$maxDataId    	 = getPostVar('maxDataId');
								$securityToken   = getPostVar('securityToken');
								
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
                        'pageNo'        => (is_numeric($pageNo) && $pageNo > 0) ? $pageNo : "1" ,
								        'maxDataId'     => $maxDataId,
								    );
                
                $json_data    = $common->getReceivedTradeList($inputData);
                
            break;
				
				case 'getMadeTradeList': // tp // done
								$userId          = getPostVar('userId');
								$pageNo     		 = getPostVar('pageNo','1');
								$maxDataId    	 = getPostVar('maxDataId');
								$securityToken   = getPostVar('securityToken');
								
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
                        'pageNo'        => (is_numeric($pageNo) && $pageNo > 0) ? $pageNo : "1" ,
								        'maxDataId'     => $maxDataId,
								    );
                
                $json_data    = $common->getMadeTradeList($inputData);
            break;
				
				case 'getUserReviewList': // tp // done
								$userId          = getPostVar('userId');
								$otherUserId     = getPostVar('otherUserId');
								$pageNo     		 = getPostVar('pageNo','1');
								$maxDataId    	 = getPostVar('maxDataId');
								$securityToken   = getPostVar('securityToken');
								
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
												'otherUserId'   => $otherUserId,
                        'pageNo'        => (is_numeric($pageNo) && $pageNo > 0) ? $pageNo : "1" ,
								        'maxDataId'     => $maxDataId,
								    );
                $json_data    = $common->getUserReviewList($inputData);
								
            break;
				
				
				case 'addRatingToUser': // tp // done
								$userId          = getPostVar('userId');
								$otherUserId     = getPostVar('otherUserId');
								$rateNo     		 = getPostVar('rateNo','1');
								$desc    	 			 = getPostVar('desc');
								$securityToken   = getPostVar('securityToken');
								
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
												'otherUserId'   => $otherUserId,
								        'rateNo'     		=> $rateNo,
												'desc'     			=> $desc,
								    );
                $json_data    = $common->addRatingToUser($inputData);
            break;
				
        case 'getBrowseTroveList':
								$userId          = getPostVar('userId');
								$pageNo     		 = getPostVar('pageNo','1');
								$maxDataId    	 = getPostVar('maxDataId');
								$materialId   	 = getPostVar('materialId');
								$searchData   	 = getPostVar('searchData');
								$priceRange   	 = getPostVar('priceRange');
								$frdFbIdList   	 = getPostVar('frdFbIdList');
						//		$isFacebookFriend= getPostVar('isFacebookFriend');
								$distanceRange   = getPostVar('distanceRange');
								$categoryIdList  = getPostVar('categoryIdList');
								$securityToken   = getPostVar('securityToken');
								
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
                        'pageNo'        => (is_numeric($pageNo) && $pageNo > 0) ? $pageNo : "1" ,
						'maxDataId'     => $maxDataId,
						'materialId'    => $materialId,
						'searchData'    => $searchData,
						'priceRange'    => $priceRange,
						'frdFbIdList'    => $frdFbIdList,
						'distanceRange' => $distanceRange,
						'categoryIdList'=> $categoryIdList,
				//		'isFacebookFriend'=> $isFacebookFriend,
                    );
                
                $json_data      = $common->getBrowseTroveList($inputData);
                
            break;
				
				case 'getUserList':
								$userId          = getPostVar('userId');
								$pageNo     		 = getPostVar('pageNo','1');
								$searchData   	 = getPostVar('searchData');
								$maxDataId    	 = getPostVar('maxDataId');
							//	$isFacebookFriend = getPostVar('isFacebookFriend');
								$frdFbIdList = getPostVar('frdFbIdList');
								$desireIdList	 	 = getPostVar('desireIdList');
								$securityToken   = getPostVar('securityToken');
								
                $isValid = $common->checkSecurityToken($userId,$securityToken,$latitude,$longitude);
                if(!$isValid){
                    $json_data = array(
                            'statusCode'	=> '9',
                            'message'	    => "Access denied..!!",
                        );
                    break;
                }
                
                $inputData = array(
                        'userId'        => $userId,
                        'pageNo'        => (is_numeric($pageNo) && $pageNo > 0) ? $pageNo : "1" ,
						'maxDataId'     => $maxDataId,
						'searchData'    => $searchData,
						'desireIdList'	=> $desireIdList,
					//	'isFacebookFriend'	=> $isFacebookFriend,
						'frdFbIdList'	=> $frdFbIdList,
                    );
                
                $json_data      = $common->getUserList($inputData);
            break;
				
     
    }// END OF SWITCH CASE
    
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
    echo json_encode($json_data);
?>