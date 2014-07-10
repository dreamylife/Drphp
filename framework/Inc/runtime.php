<?php
//runtime 
if(!defined('DR_PATH')){
	die('the framework library are not set correct!');
}
if(!defined('INC_PATH')){
	die('INC_PATH not defined!');
}
require_once(INC_PATH.'/common.php') ;

//application library  path defined
define('APP_CACHE_DIR',APP_PATH.'/cache');
define('APP_CONFIG_DIR',APP_PATH.'/config');
define('APP_LIB_DIR',APP_PATH.'/controllers');
define('APP_LANG_DIR',APP_PATH.'/lang');
define('APP_MODLE_DIR',APP_PATH.'/models');
define('APP_TPL_DIR',APP_PATH.'/views') ;
define('APP_INC_DIR',APP_PATH.'/include') ;

if(!defined('__PUBLIC__')){
	define('__PUBLIC__',ROOT_PATH.'Public');
}
//import applocation runtime file
foreach (glob(APP_LIB_DIR.'/*Controller.php') as $key => $value) {
	require_once($value) ;
}
//model lib
foreach (glob(APP_MODLE_DIR.'/*.php') as $key => $value) {
	require_once($value) ;
}
//read config file <parrten return array...>
foreach (glob(APP_CONFIG_DIR.'/*Conf.php') as $key => $value) {
	$GLOBALS['_config'] = array_merge($GLOBALS['_config'],require($value)) ; 
}
//custom include functions 
foreach (glob(APP_INC_DIR.'/*Inc.php') as $key => $value) {
	require_once($value) ;
}

require(APP_PATH.'/routes.php') ; //load router 
