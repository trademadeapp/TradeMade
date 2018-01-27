<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once("../../config/1_0/config.php");
require_once DOCROOT.'classes/libraries/PHPMailer/class.phpmailer.php';
$mail = new PHPMailer(); // create a new object
$mail->IsSMTP(); // enable SMTP
$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
$mail->SMTPAuth = false; // authentication enabled
$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
$mail->Host = "smtp.gmail.com";
$mail->Port = 465; // or 587
$mail->IsHTML(true);
$mail->Username = "Trademade.app@gmail.com";
$mail->Password = "quiettrain1618";
$mail->SetFrom("Trademade.app@gmail.com");
$mail->Subject = "Test";
$mail->Body = "hello";
$mail->AddAddress("kp@messapps.com");

 if(!$mail->Send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
 } else {
    echo "Message has been sent";
 }
 
 exit;
if(isset($_POST) && count($_POST) > 0){
  echo "Post Data :<br/><pre>";
  print_r($_POST);
  echo "</pre>";
}

if(isset($_FILES) && count($_FILES) > 0){
  echo "Files Data :<br/><pre>";
  print_r($_FILES);
  echo "</pre>";
}

// pratham dulara pratham vandito gajananana gan naaya vighna vinashak gunijanak balak
$apiArr = array(
			'signup',					// tp // done
			
	 );		  
	sort($apiArr);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"    "http://www.w3.org/TR/html4/strict.dtd"    >
<html lang="en">
  <head>
  <title>App > 1.0 > API Manager</title>
  <script src="../js/jquery-ui/jquery-1.7.2.min.js" type="text/javascript"></script>
  <script src="../js/jquery-ui/jquery-ui-1.8.21.custom.min.js" type="text/javascript"></script>
  <script src="../js/validationEngine/jquery.validationEngine.js" type="text/javascript"></script>
  <script src="../js/validationEngine/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
  <link rel="stylesheet" href="../css/validationEngine/validationEngine.jquery.css" type="text/css"/>
  <link rel="stylesheet" href="../css/jquery-ui/jquery-ui-1.8.21.custom.css" type="text/css"/>
  <script type="text/javascript">
        $('document').ready(function(){
	    var id = $('#apiChange').val();
            $('.all').hide();
            $('#'+id).show();
            $('#apiChange').change(function(){
                var id = $(this).val();
                $('.all').hide();
                $('#'+id).show();
            });
	    jQuery("form").validationEngine();
	    
	    $( ".datepicker" ).datepicker();
         });
	
	function validate()
	{
	 
	 var id = $('#apiChange').val();
	
	 if($('#'+id+'Form').validationEngine('validate'))
	 {
	  $('#'+id+'Form').submit();
	  
	 }
	}
    </script>
  <style type="text/css">
td {
	vertical-align: super;
	min-width: 110px;
	padding-bottom: 10px;
}
td input[type="text"] {
	background: none repeat scroll 0 0 #FFFFFF;
	border-color: #CCCCCC #999999 #999999 #CCCCCC;
	border-radius: 3px 3px 3px 3px;
	border-style: solid;
	border-width: 1px;
	box-shadow: 0 1px 0 #E8E8E8;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	min-width: 300px;
	padding: 3px;
}
.submit-button {
	border-top: 1px solid #898bfa;
	background: #1d2042;
	background: -webkit-gradient(linear, left top, left bottom, from(#3e969c), to(#1d2042));
	background: -webkit-linear-gradient(top, #3e969c, #1d2042);
	background: -moz-linear-gradient(top, #3e969c, #1d2042);
	background: -ms-linear-gradient(top, #3e969c, #1d2042);
	background: -o-linear-gradient(top, #3e969c, #1d2042);
	padding: 8px 16px;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
	-webkit-box-shadow: rgba(0, 0, 0, 1) 0 1px 0;
	-moz-box-shadow: rgba(0, 0, 0, 1) 0 1px 0;
	box-shadow: rgba(0, 0, 0, 1) 0 1px 0;
	text-shadow: rgba(0, 0, 0, .4) 0 1px 0;
	color: #f2cc6d;
	font-size: 16px;
	font-family: Helvetica, Arial, Sans-Serif;
	text-decoration: none;
	vertical-align: middle;
}
.submit-button:hover {
	background: -webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#C1C1C1));
	background: -webkit-linear-gradient(top, #FFFFFF, #C1C1C1);
	background: -moz-linear-gradient(top, #FFFFFF, #C1C1C1);
	background: -ms-linear-gradient(top, #FFFFFF, #C1C1C1);
	background: -o-linear-gradient(top, #FFFFFF, #C1C1C1);
	border-radius: 6px 6px 6px 6px;
	border-top: 1px solid #9B9B9B;
	box-shadow: 0 1px 0 #CCCCCC;
	color: #444444;
	font-family: Helvetica, Arial, Sans-Serif;
	font-size: 16px;
	padding: 8px 16px;
	text-decoration: none;
	text-shadow: none;
	vertical-align: middle;
}
.submit-button:active {
	border-top-color: #005085;
}
</style>
  </head>
  <body>
<!-- Insert your content here -->
<table style="width:100%">
    <tr>
    <td align="center" style="alignment-adjust: central;" colspan="2">
	  <a href="http://messapps.com" target="_blank">
		<h1 style="font-size: 49px; font-family: sans-serif; text-decoration: underline; color: purple;"></h1>
	  </a>
	</td>
  </tr>
    <tr>
	  
    <td style="padding: 20px;text-align: center;"> apiName :
        <select name="apiChange" id="apiChange">
		  <?php
		  foreach($apiArr as $key => $value){
			echo '<option value= "'.$value.'">'.$value.'</option>';
		  }
		  ?>
		</select>
	</td>
  </tr>
    <tr>
    <td align="center" style="padding: 30px 0 0 0;">
      
	  <?php $apiName = "loginWithSocialData"; ?>
	  <div class="all" id="<?php echo $apiName; ?>" style="display: none;">
        <form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
          <table>
						<tr>
                <td>socialId:</td>
                <td><input type="text" name="socialId" value=""></td>
            </tr>
						<tr>
                <td>firstName:</td>
                <td><input type="text" name="firstName" value=""></td>
            </tr>
						<tr>
                <td>lastName:</td>
                <td><input type="text" name="lastName" value=""></td>
            </tr>
						<tr>
                <td>email:</td>
                <td><input type="text" name="email" value=""></td>
            </tr>
						<tr>
                <td>profileImage:</td>
                <td><input type="file" name="profileImage" value=""></td>
            </tr>
						<tr>
                <td>uniqueToken:</td>
                <td><input type="text" name="uniqueToken" value=""></td>
            </tr>
						<tr>
                <td>deviceToken:</td>
                <td><input type="text" name="deviceToken" value=""></td>
            </tr>
						<tr>
                <td>deviceType:</td>
                <td>
									<select name="deviceType" >
									<option value="1"> 1 : IOS </option>
									</select>
								</td>
            </tr>
						<tr>
              <td></td>
              <td></td>
            </tr>
          </table>
          <br/>
          <br/>
          <br/>
          <input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
          <a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
        </form>
      </div>
			
			<?php $apiName = "signup"; ?>
			<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
        <form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="testdata.php">
          <table>
						<tr>
                <td>firstName:</td>
                <td><input type="text" name="firstName" value=""></td>
            </tr>
						<tr>
                <td>lastName:</td>
                <td><input type="text" name="lastName" value=""></td>
            </tr>
						<tr>
                <td>email:</td>
                <td><input type="text" name="email" value=""></td>
            </tr>
						<tr>
                <td>password:</td>
                <td><input type="text" name="password" value=""></td>
            </tr>
						<tr>
                <td>zipCode:</td>
                <td><input type="text" name="zipCode" value=""></td>
            </tr>
						<tr>
                <td>address:</td>
                <td><input type="text" name="address" value=""></td>
            </tr>
						<tr>
                <td>latitude:</td>
                <td><input type="text" name="latitude" value=""></td>
            </tr>
						<tr>
                <td>longitude:</td>
                <td><input type="text" name="longitude" value=""></td>
            </tr>
						<tr>
                <td>profileImage:</td>
                <td><input type="file" name="profileImage" value=""></td>
            </tr>
						<tr>
                <td>uniqueToken:</td>
                <td><input type="text" name="uniqueToken" value=""></td>
            </tr>
						<tr>
                <td>deviceToken:</td>
                <td><input type="text" name="deviceToken" value=""></td>
            </tr>
						<tr>
                <td>deviceType:</td>
                <td>
									<select name="deviceType" >
									<option value="1"> 1 : IOS </option>
									</select>
								</td>
            </tr>
						<tr>
              <td></td>
              <td></td>
            </tr>
          </table>
          <br/>
          <br/>
          <br/>
          <input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
          <a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
        </form>
      </div>
			
			<?php $apiName = "login"; ?>
			<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
        <form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
          <table>
						<tr>
                <td>email:</td>
                <td><input type="text" name="email" value=""></td>
            </tr>
						<tr>
                <td>password:</td>
                <td><input type="text" name="password" value=""></td>
            </tr>
						<tr>
                <td>uniqueToken:</td>
                <td><input type="text" name="uniqueToken" value=""></td>
            </tr>
						<tr>
                <td>deviceToken:</td>
                <td><input type="text" name="deviceToken" value=""></td>
            </tr>
						<tr>
                <td>deviceType:</td>
                <td>
									<select name="deviceType" >
									<option value="1"> 1 : IOS </option>
									</select>
								</td>
            </tr>
						<tr>
              <td></td>
              <td></td>
            </tr>
          </table>
          <br/>
          <br/>
          <br/>
          <input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
          <a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
        </form>
      </div>
			
			<?php $apiName = "updateUserProfile"; ?>
			<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
        <form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
          <table>
						<tr>
                <td>userId:</td>
                <td><input type="text" name="userId" value=""></td>
            </tr>
						<tr>
                <td>firstName:</td>
                <td><input type="text" name="firstName" value=""></td>
            </tr>
						<tr>
                <td>lastName:</td>
                <td><input type="text" name="lastName" value=""></td>
            </tr>
						<tr>
                <td>zipCode:</td>
                <td><input type="text" name="zipCode" value=""></td>
            </tr>
						<tr>
                <td>address:</td>
                <td><input type="text" name="address" value=""></td>
            </tr>
						<tr>
                <td>profileImage:</td>
                <td><input type="file" name="profileImage" value=""></td>
            </tr>
						<tr>
                <td>securityToken:</td>
                <td><input type="text" name="securityToken" value=""></td>
            </tr>
						<tr>
              <td></td>
              <td></td>
            </tr>
          </table>
          <br/>
          <br/>
          <br/>
          <input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
          <a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
        </form>
      </div>
			
			<?php $apiName = "getUserProfile"; ?>
				<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>otherUserId:</td>
										<td><input type="text" name="otherUserId" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
					<?php $apiName = "getUserConversationList"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>searchData:</td>
										<td><input type="text" name="searchData" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
				
				<?php $apiName = "getUserConversationData"; ?> 
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>conversationId:</td>
										<td><input type="text" name="conversationId" value=""></td>
								</tr>
								<tr>
										<td>maxDataId:</td>
										<td><input type="text" name="maxDataId" value=""></td>
								</tr>
								<tr>
										<td>pageNo:</td>
										<td><input type="text" name="pageNo" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
			<?php $apiName = "forgotPassword"; ?>
			<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
        <form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
          <table>
						<tr>
                <td>email:</td>
                <td><input type="text" name="email" value=""></td>
            </tr>
						<tr>
              <td></td>
              <td></td>
            </tr>
          </table>
          <br/>
          <br/>
          <br/>
          <input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
          <a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
        </form>
      </div>
					
			<?php $apiName = "logout"; ?>
			<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
        <form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
          <table>
						<tr>
                <td>userId:</td>
                <td><input type="text" name="userId" value=""></td>
            </tr>
						<tr>
                <td>securityToken:</td>
                <td><input type="text" name="securityToken" value=""></td>
            </tr>
						<tr>
              <td></td>
              <td></td>
            </tr>
          </table>
          <br/>
          <br/>
          <br/>
          <input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
          <a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
        </form>
      </div>
			
			<?php $apiName = "getUserConversationId"; ?>
				<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
					<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
						<table>
							<tr>
									<td>userId:</td>
									<td><input type="text" name="userId" value=""></td>
							</tr>
							<tr>
									<td>otherUserId:</td>
									<td><input type="text" name="otherUserId" value=""></td>
							</tr>
							<tr>
									<td>securityToken:</td>
									<td><input type="text" name="securityToken" value=""></td>
							</tr>
							<tr>
								<td></td>
								<td></td>
							</tr>
						</table>
						<br/>
						<br/>
						<br/>
						<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
						<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
					</form>
				</div>
				
					<?php $apiName = "changePassword"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>oldPassword:</td>
										<td><input type="text" name="oldPassword" value=""></td>
								</tr>
								<tr>
										<td>newPassword:</td>
										<td><input type="text" name="newPassword" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
					<?php $apiName = "updateUserDesireList"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>desireIdList:</td>
										<td><input type="text" name="desireIdList" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
				
				<?php $apiName = "getCategoryList"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
				
				<?php $apiName = "getMaterialList"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
				<?php $apiName = "addNewTrove"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>troveType:</td>
										<td>
											<select name="troveType" >
											<option value="1"> 1 : Item </option>
											<option value="2"> 2 : Service </option>
											</select>
										</td>
								</tr>
								<tr>
										<td>categoryId:</td>
										<td><input type="text" name="categoryId" value=""></td>
								</tr>
								<tr>
										<td>priceRange:</td>
										<td>
											<select name="priceRange" >
												<option value="1"> 1 : $ </option>
												<option value="2"> 2 : $ $ </option>
												<option value="3"> 3 : $ $ $</option>
												<option value="4"> 4 : $ $ $ $</option>
												<option value="5"> 5 : $ $ $ $ $</option>
											</select>
										</td>
								</tr>
								<tr>
										<td>quality:</td>
										<td>
											<select name="quality" >
												<option value="1"> 1 : Ok </option>
												<option value="2"> 2 : Good </option>
												<option value="3"> 3 : Great</option>
												<option value="4"> 4 : Best</option>
											</select>
											<br>IF troveType = 1 than pass this quality number otherwise pass blank
										</td>
								</tr>
								<tr>
										<td>materialId:</td>
										<td><input type="text" name="materialId" value=""><br>IF troveType = 1 than pass this material id otherwise pass blank</td>
								</tr>
								<tr>
										<td>desc:</td>
										<td><input type="text" name="desc" value=""></td>
								</tr>
								<tr>
										<td>troveImage_1:</td>
										<td><input type="file" name="troveImage_1" value=""></td>
								</tr>
								<tr>
										<td>troveImage_2:</td>
										<td><input type="file" name="troveImage_2" value=""></td>
								</tr>
								<tr>
										<td>troveImage_3:</td>
										<td><input type="file" name="troveImage_3" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
			
			
					<?php $apiName = "removeTrove"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>troveId:</td>
										<td><input type="text" name="troveId" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
					
					<?php $apiName = "getUserTroveList"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>otherUserId:</td>
										<td><input type="text" name="otherUserId" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
					<?php $apiName = "getTroveDetail"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>troveId:</td>
										<td><input type="text" name="troveId" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
					
					<?php $apiName = "purposeTrade"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>otherUserId:</td>
										<td><input type="text" name="otherUserId" value=""></td>
								</tr>
								<tr>
										<td>desiredTroveIdList:</td>
										<td><input type="text" name="desiredTroveIdList" value=""></td>
								</tr>
								<tr>
										<td>purposedTroveIdList:</td>
										<td><input type="text" name="purposedTroveIdList" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
			
					<?php $apiName = "counterTrade"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>tradeId:</td>
										<td><input type="text" name="tradeId" value=""></td>
								</tr>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>otherUserId:</td>
										<td><input type="text" name="otherUserId" value=""></td>
								</tr>
								<tr>
										<td>desiredTroveIdList:</td>
										<td><input type="text" name="desiredTroveIdList" value=""></td>
								</tr>
								<tr>
										<td>purposedTroveIdList:</td>
										<td><input type="text" name="purposedTroveIdList" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
			
					<?php $apiName = "moderateTrade"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>tradeId:</td>
										<td><input type="text" name="tradeId" value=""></td>
								</tr>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>status:</td>
										<td>
											<select name="status" >
												<option value="1"> 1 : Accept </option>
												<option value="2"> 2 : Reject </option>
											</select>
										</td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
					<?php $apiName = "cancelTrade"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>tradeId:</td>
										<td><input type="text" name="tradeId" value=""></td>
								</tr>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
					<?php $apiName = "getSentTradeList"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>pageNo:</td>
										<td><input type="text" name="pageNo" value=""></td>
								</tr>
								<tr>
										<td>maxDataId:</td>
										<td><input type="text" name="maxDataId" value=""><br>If pageNo = 1 than maxDataId=0 <br/>else pass maxDataId from first page response</td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
			
					<?php $apiName = "getReceivedTradeList"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>pageNo:</td>
										<td><input type="text" name="pageNo" value=""></td>
								</tr>
								<tr>
										<td>maxDataId:</td>
										<td><input type="text" name="maxDataId" value=""><br>If pageNo = 1 than maxDataId=0 <br/>else pass maxDataId from first page response</td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
			
					<?php $apiName = "getMadeTradeList"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>pageNo:</td>
										<td><input type="text" name="pageNo" value=""></td>
								</tr>
								<tr>
										<td>maxDataId:</td>
										<td><input type="text" name="maxDataId" value=""><br>If pageNo = 1 than maxDataId=0 <br/>else pass maxDataId from first page response</td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
					<?php $apiName = "getUserReviewList"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>otherUserId:</td>
										<td><input type="text" name="otherUserId" value=""></td>
								</tr>
								<tr>
										<td>pageNo:</td>
										<td><input type="text" name="pageNo" value=""></td>
								</tr>
								<tr>
										<td>maxDataId:</td>
										<td><input type="text" name="maxDataId" value=""><br>If pageNo = 1 than maxDataId=0 <br/>else pass maxDataId from first page response</td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
					<?php $apiName = "addRatingToUser"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>otherUserId:</td>
										<td><input type="text" name="otherUserId" value=""></td>
								</tr>
								<tr>
										<td>rateNo:</td>
										<td>
											<select name="rateNo" >
												<option value="1"> 1 : * </option>
												<option value="2"> 2 : * * </option>
												<option value="3"> 3 : * * *</option>
												<option value="4"> 4 : * * * *</option>
												<option value="5"> 5 : * * * * *</option>
											</select>
										</td>
								</tr>
								<tr>
										<td>desc:</td>
										<td><input type="text" name="desc" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div> 
			
					<?php $apiName = "getBrowseTroveList"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>pageNo:</td>
										<td><input type="text" name="pageNo" value=""></td>
								</tr>
								<tr>
										<td>maxDataId:</td>
										<td><input type="text" name="maxDataId" value=""><br>If pageNo = 1 than maxDataId=0 <br/>else pass maxDataId from first page response</td>
								</tr>
								<tr>
										<td>searchData:</td>
										<td><input type="text" name="searchData" value=""></td>
								</tr>
								<tr>
										<td>priceRange:</td>
										<td>
											<select name="priceRange" >
												<option value="1"> (blank) : No range filter </option>
												<option value="1"> 1 : $ </option>
												<option value="2"> 2 : $ $ </option>
												<option value="3"> 3 : $ $ $</option>
												<option value="4"> 4 : $ $ $ $</option>
												<option value="5"> 5 : $ $ $ $ $</option>
											</select>
										</td>
								</tr>
								<tr>
										<td>distanceRange:</td>
										<td><input type="text" name="distanceRange" value=""></td>
								</tr>
								<tr>
										<td>categoryIdList:</td>
										<td><input type="text" name="categoryIdList" value=""><br/>Comma separate category id list</td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
					<?php $apiName = "getUserList"; ?>
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>pageNo:</td>
										<td><input type="text" name="pageNo" value=""></td>
								</tr>
								<tr>
										<td>maxDataId:</td>
										<td><input type="text" name="maxDataId" value=""><br>If pageNo = 1 than maxDataId=0 <br/>else pass maxDataId from first page response</td>
								</tr>
								<tr>
										<td>searchData:</td>
										<td><input type="text" name="searchData" value=""></td>
								</tr>
								<tr>
										<td>desireIdList:</td>
										<td><input type="text" name="desireIdList" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
					
				<?php $apiName = "uploadChatMediaData"; ?> 
					<div class="all" id="<?php echo $apiName; ?>" style="display: none;">
						<form name="apiform" id="<?php echo $apiName; ?>Form" enctype="multipart/form-data" method="post" action="api.php">
							<table>
								<tr>
										<td>userId:</td>
										<td><input type="text" name="userId" value=""></td>
								</tr>
								<tr>
										<td>conversationId:</td>
										<td><input type="text" name="conversationId" value=""></td>
								</tr>
								<tr>
									<td>mediaType:</td>
									<td>
										<select name="mediaType"  >
											<option value="1"> 1 : Image </option>
										</select>
									</td>
								</tr>
								<tr>
									<td>mediaData:</td>
									<td><input type="file" name="mediaData" value=""></td>
								</tr>
								<tr>
										<td>securityToken:</td>
										<td><input type="text" name="securityToken" value=""></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
							<input type="hidden" name="apiName" value="<?php echo $apiName; ?>">
							<a href="javascript:void(0);" class="submit submit-button" onClick="validate();"><?php echo $apiName; ?></a>
						</form>
					</div>
			
			
			
			
			
			
			
			
			
			
			
			
	
	
	
	
	
	</td>
  </tr>
	<tr>
    <td align="left" style="alignment-adjust: central " colspan="2">Response : statusCode <br> 0 = Server code error or Invalid api request<br>1 = Operation performed successfully.<br> 2 = Invalid requested data Or Api not perform what we have aim to be perform. <br> 9 = Invalid api or data access(If you will get this status then please logout user immediately).</td>
  </tr>
  </table>
</body>
</html>