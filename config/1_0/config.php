<?php
date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', '-1');

define('PRODUCTION_ENVIRONMENT', 'false');  // 'true', 'false'

define('DOCROOT',realpath(dirname(__FILE__).'/../../')."/");
if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'localhost')
{
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    define('DB_SERVER', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_DATABASE', 'test_t');
    define('DB_PORT', '3306');
    define('DOMAINURL','http://localhost/test/');
    define('PROJ_FOLDERNAME','trademade');
    define('LOG_REQUEST_ON',"true");    // true or false
}
else
{
    define('DOMAINURL','http://52.8.137.106');  
    define('DB_SERVER', 'localhost');
    define('DB_USER', 'trademade_app');
    define('DB_PASS', '2W9dLhzRMnbbtdMt');
    define('DB_DATABASE', 'trademade_common');
    define('DB_PORT', '3306');
    define('PROJ_FOLDERNAME','');
    define('LOG_REQUEST_ON',"true");    // true or false
}

define('CARBON_ADD_FIRST_TIME_NEW_TROVE',"10");

define('IS_IOS_PUSH_ON',"true");
define('IS_PUSH_LIVE','true'); // 1 : yes and 2: no
define('IOS_PUSH_PEM_PATH',DOCROOT."pem/production_ios.pem"); // PROD

define('BASEURL',DOMAINURL.PROJ_FOLDERNAME);

define('OPENFIRE_USERSERVICE_URL','http://52.8.137.106:9090/plugins/userService/userservice?');
define('OPENFIRE_USERSERVICE_SECRET_KEY','Fa6B4Kv4bF6R');

define('EJABBERD_HOST_URL','52.8.137.106');
define('EJABBERD_MOD_REST_URL','http://localhost:5285/rest');

define('CHAT_JID_SUFFIX', '@52.8.137.106');
define('CHAT_GROUP_NAME', 'TradeMadeAppGroup');
define('XMPP_USER_PREFIX', 'trademade_');

/**** DATABASE TABLE NAME CONSTANT DEFINE ***********/

define('TBL_CATEGORIES', 'a_categories');
define('TBL_CATEGORIES_AS', '`a_categories` as cat');
define('TBL_CHAT_CONVERSATIONS', 'a_chat_conversations');
define('TBL_CHAT_DATAS', 'a_chat_datas');
define('TBL_CHAT_FILES', 'a_chat_files');
define('TBL_CITIES', 'a_cities');
define('TBL_CITIES_AS', '`a_cities` as ct');
define('TBL_MATERIALS', 'a_materials');
define('TBL_MATERIALS_AS', '`a_materials` as mtl');
define('TBL_TROVES', 'a_troves');
define('TBL_TROVES_AS', '`a_troves` as trv');
define('TBL_REPORTED_USERS', 'a_reported_users');
define('TBL_REPORTED_USERS_AS', '`a_reported_users` as rpt');
define('TBL_REPORTED_TROVES', 'a_reported_troves');
define('TBL_REPORTED_TROVES_AS', '`a_reported_troves` as rptTrv');
define('TBL_TRADES', 'a_trades');
define('TBL_TRADES_AS', '`a_trades` as trd');
define('TBL_TRADE_REVIEW_REMAINS', 'a_trade_review_remains');
define('TBL_TRADE_REVIEW_REMAINS_AS', '`a_trade_review_remains` as rvwRmn');
define('TBL_USERS', 'a_users');
define('TBL_USERS_AS', '`a_users` as u');
define('TBL_SLOT_PURCHASES', 'a_slot_purchases');
define('TBL_SLOT_PURCHASES_AS', '`a_slot_purchases` as sltPrcs');
define('TBL_USER_DESIRES', 'a_user_desires');
define('TBL_USER_DESIRES_AS', '`a_user_desires` as uDsr');

define('TBL_SOCIAL_FRIENDS', 'a_social_friends');
define('TBL_SOCIAL_FRIENDS_AS', '`a_social_friends` as scFrd');

define('TBL_USER_DEVICE_TOKENS', 'a_user_device_tokens');
define('TBL_USER_REVIEWS', 'a_user_reviews');
define('TBL_USER_REVIEWS_AS', '`a_user_reviews` as uRvw');
define('TBL_Z_REQUEST_LOG', 'a_z_request_log');

/******** WEB PAGE URL ********/

define('MAIL_ADMIN_NOTIFY', "support@trademade-app.com");
define('MAIL_HOST', "email-smtp.us-west-2.amazonaws.com");
define('MAIL_USER', "AKIAIBE2BPEEYFYFBF4Q");
define('MAIL_PASSWORD', "Ao26KevYB8QQm+1ILxpF1fH2jPXwNA8aluZqtKdjgveC");
define('MAIL_FROM', "support@trademade-app.com"); 

define('THUMB_FLD_NAME','thumb/');

define('USER_THUMB_WIDTH','250');
define('USER_THUMB_HEIGHT','250');
define('USER_THUMB_TYPE','crop'); // Resize image (options: exact, portrait, landscape, auto, crop)
define('USER_THUMB_IMG_RESOLUTION','75');

define('TROVE_THUMB_WIDTH','250');
define('TROVE_THUMB_HEIGHT','250');
define('TROVE_THUMB_TYPE','crop'); // Resize image (options: exact, portrait, landscape, auto, crop)
define('TROVE_THUMB_IMG_RESOLUTION','75');

define('API_USER_ABS_IMGPATH',BASEURL.'/uploads/user/');
define('API_USER_REL_IMGPATH','../../uploads/user/');

define('API_CATEGORY_ABS_IMGPATH',BASEURL.'/uploads/category/');
define('API_CATEGORY_REL_IMGPATH','../../uploads/category/');

define('API_TROVE_ABS_IMGPATH',BASEURL.'/uploads/trove/');
define('API_TROVE_REL_IMGPATH','../../uploads/trove/');

define('API_CHAT_ABS_MEDIAPATH',BASEURL.'/uploads/conversation/');
define('API_CHAT_REL_MEDIAPATH','../../uploads/conversation/');
define('MEDIA_THUMB_FLD_NAME','thumb/');


// ADMIN IMAGE PATH


define('gmapkey', "");
$config = array();
/**** Resize Image Thumb *****/

function fetchConfig()
{
    return $config;
}

$GLOBALS['config'] = $config;
/* End of file config.php */

/* Location: /<root>/www/config/config.php */
?>