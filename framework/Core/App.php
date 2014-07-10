<?php
/*
* app类 gate of framework ....
*/
class App{

	protected $method ;
	protected $uri ; 
	protected $docroot ; //根目录
	protected $requri ;  
	protected $param ; 
	protected $urlmode ; //urlmode 1为Index.php?s=xx&b=xxx 2为index.php/user/all 

	public function __construct(){
		$this->method 	= strtolower($_SERVER['REQUEST_METHOD']);
		$this->uri 		= $_SERVER['REQUEST_URI'] ;
		$this->docroot  = $_SERVER['DOCUMENT_ROOT'] ; 
		$this->init();
	}
	public function init(){ 
		$script_name = $_SERVER['SCRIPT_NAME'] ; //  /f/index.php 
		if(strpos( $this->uri,$script_name) === 0){
			$uri = ltrim(str_replace($script_name,'', $this->uri ),'/') ;  
		}else{
			$uri = str_replace(rtrim($script_name,'index.php'),'', $this->uri ) ;  
		} 
		$uri = $uri == '' ? '/' : $uri ;  
		if( strpos($uri,'?') === 0 )  $this->urlmode = 1 ; 
		$uri = strpos($uri,'?') === 0 ? '/' : $uri ;     
		$this->parseUri( $uri ) ;  
	} 
	//parse the request uri 
	public function parseUri($uri){
		if($uri == '/'){
			$this->requri = $uri ; 
		}
		$method = $_SERVER['REQUEST_METHOD'] ;  
		//请求类型 HTTP_ACCEPT text/html 
		if( $this->urlmode == 1){
			$this->param = $_SERVER['QUERY_STRING'] ; //s=xxx
			$tr = array() ; 
			foreach ($_REQUEST as $key => $value){
				$tr[$key] = urldecode($value) ;
 			}
 			if(isset($_REQUEST['m'])){
 				$this->requri = isset($_REQUEST['c']) ? $_REQUEST['m'].'/'.$_REQUEST['c'] : $_REQUEST['m'].'/index'; 
 			}else{
 				$this->requri = 'index/index' ;  //default route..
 			}
 			//reset get/post variables
 			$_GET 	= $this->method == 'get' ? $tr : null ; 
 			$_POST 	= $this->method == 'post' ? $tr : null ;  
		}else{
			$nr = array_filter(explode('/', $uri)) ;
			if(count($nr) > 1){
				$this->requri = $nr[0].'/'.$nr[1] ;  // blog/all
			}else{ 
				$this->requri = $uri.'/index' ;  // blog/
			}
			//do with param options
			$pr = array() ;
			for ($i = 2; $i < count($nr) ; $i++) { 
				$k = $nr[$i] ;  
				if($i+1 < count($nr))  $pr[$k] = urldecode( $nr[$i+1] ) ; 
				else  $pr[$k] = '' ;   
				$i++ ;   
			}
			$_GET 	= $this->method == 'get' ? $pr : null ; 
 			$_POST 	= $this->method == 'post' ? $pr : null ;  
		}
		$_REQUEST =  $this->method == 'get' ? $_GET : $_POST ; 
		//print_r($_REQUEST) ; 
 		//print_r($this->requri) ;
	}
	//run the app.
	public function run(){ 
		//实例化Route...
		$Route =  new Route($this->requri,$this->method) ; 
		//dispater...........
		$Route->dispatcher() ; 
		//print_r(Route::all()) ;
	}
}