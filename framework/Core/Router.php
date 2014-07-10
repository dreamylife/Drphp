<?php
class Route{
	 /**
     * @var string
     */
    private $path = '/';

    /**
     * @var string
     */
    private $host = '';

    /**
     * @var string
     */
    private $method = '' ;

    /*
    *@var routs define 
    */

    private static $routes = array();

    /**
     * Constructor.
     */
    public function __construct($uri='',$method=''){
        if($uri){
            $this->setPaths($uri) ;
        }
        if ($method) {
            $this->setMethods($method);
        }
    }
    //dispatcher
    public function dispatcher(){ 
        if(in_array($this->path,self::$routes[$this->method])){
            $nowopt =  self::$routes[$this->method][$this->path] ; 
        }else{
            if(preg_match("/^[a-zA-z]+/", $this->path)) {
              $nowopt =  $this->path ; //no route rules . 
            } 
        }  
        if(!isset($nowopt)) $nowopt = 'Index/index' ; //default actions.
        //找到对应控制器 
        $ptr = array_filter(explode('/', $nowopt)) ; 
        $action = ucfirst($ptr[0]) ; 
        $fcs    = $ptr[1] ; 
        $Acontroller = $action.'Controller' ;  
        //check controller 
        if(class_exists($Acontroller)){
            $act = new $Acontroller(); 
            if(method_exists($act,$fcs)){ 
                $act->$fcs() ;
            }else{ 
                //throw exception ....
                drMsg('no method '.$fcs.' exists in Class '.get_class( $act) );
            } 
        }else{
            //throw exception
            //..............
            die('no class '.$Acontroller.' exists in lib ' );
        }
    }
    //set methods
    private function setMethods($name){
        $this->method = $name ; 
    }
    //set paths
    private function setPaths($path){
        $this->path = $path ; 
    }
    //register get request
    public static function get($rquest,$respose){
    	self::register($rquest,$respose,'get');
    }
    //registet post request 
    public static function post($rquest,$respose){
    	self::register($rquest,$respose,'post');
    }
    //register
    private static function register($rquest,$respose,$method){
    	self::$routes[$method][$rquest] = $respose ; 
    }
    //get route priciple
    public function getRoute($name,$method){
    	return isset(self::$routes[$method][$name]) ? self::$routes[$method][$name] : null;
    }
    //count number 
    public function count(){
        return count(self::$routes);
    }
    //get all defined routes
    public function all(){
        return self::$routes;
    }
    //remove signal routes 
    public function remove($name,$method){
        unset(self::$routes[$method][$name]);
    }

}