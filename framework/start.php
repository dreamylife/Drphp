<?php
//框架基本文件和类库 
if(!defined('APP_PATH')){
	die ('please define APP_PATH.');
}
define('DR_ROOT', dirname( $_SERVER['SCRIPT_FILENAME'] )  ) ; //G:/wamp/www/f
if (version_compare(PHP_VERSION, '5.3') <= 0) {
    define('DR_PATH',dirname(__FILE__)) ;
}else{
    define('DR_PATH',__DIR__) ;  //G:\wamp\www\f\framework
}
//root directory 
if(!defined('ROOT_PATH')) { 
    define('ROOT_PATH',dirname(DR_PATH));
} 
define('INC_PATH',DR_PATH.'/Inc') ;
define('CORE_PATH',DR_PATH.'/Core') ; 
define('DB_PATH',DR_PATH.'/Db') ; 
define('LOG_PATH',DR_PATH.'/Log') ; 
define('TPL_PATH',DR_PATH.'/Template') ; 

$GLOBALS['_config'] = array() ; 
//load frame config 
$GLOBALS['_config'] = require(INC_PATH.'/conf.php');

require_once(DR_PATH.'/autoload.php'); //加载类库

require_once(INC_PATH.'/runtime.php') ; //运行时文件

$app = new App() ;

$app->run();
