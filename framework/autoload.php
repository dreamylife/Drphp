<?php
//加载框架文件
if(defined(DR_PATH)){
	die('the framework library are not set correct!');
}
require_once(CORE_PATH.'/Router.php') ; 
require_once(CORE_PATH.'/Cache.php') ; 
require_once(CORE_PATH.'/Controller.php') ; 
require_once(CORE_PATH.'/Exception.php') ; 
require_once(CORE_PATH.'/Model.php') ; 
require_once(CORE_PATH.'/View.php') ; 
require_once(CORE_PATH.'/App.php') ; 
//DB CORE class 
require_once(CORE_PATH.'/Db.php') ; 
//include functions 
require_once(INC_PATH.'/common.php') ; 
//log record 
require_once(CORE_PATH.'/Log.php') ; 
//tpl compile model
require_once(TPL_PATH.'/Compile.php') ; 
//database driver 加载
db_driver_autoload();
