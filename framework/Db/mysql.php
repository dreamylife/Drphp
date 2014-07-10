<?php
/**
 * MySQL数据库操作类
 *
 * @copyright (c) DrPHP All Rights Reserved
 */

class MySql {

	/**
	 * 查询次数
	 * @var int
	 */
	private $queryCount = 0;

	/**
	 * 内部数据连接对象
	 * @var resourse
	 */
	private $conn;

	/**
	 * 内部数据结果
	 * @var resourse
	 */
	private $result;

	/**
	 * 上一条sql语句
	 * @var <String> lastsql
	 */
	private $lastsql;

	/**
	 * 内部实例对象
	 * @var object MySql
	 */
	private static $instance = null;

	/*
	* 数据库名字
	*/
	private $dbname = '' ; 
	/*
	* 数据表前缀
	*/
	private $prefix = '' ; 

	/*
	* 执行操作影响的行数
	*/
	private $numRows = '' ; 

	/*  最后插入ID  
	*/
	private $lastInsID = null ;  

	/*
	* 单例模式 构造数据库连接实例
	*/
	private function __construct($dbconf) {

		if (!function_exists('mysql_connect')) {
			drMsg('服务器空间PHP不支持MySql数据库');
		}
		if (!$this->conn = @mysql_connect($dbconf['host'], $dbconf['username'], $dbconf['password'])) {
            switch ($this->geterrno()) {
                case 2005:
                    drMsg("连接数据库失败，数据库地址错误或者数据库服务器不可用");
                    break;
                case 2003:
                    drMsg("连接数据库失败，数据库端口错误");
                    break;
                case 2006:
                    drMsg("连接数据库失败，数据库服务器不可用");
                    break;
                case 1045:
                    drMsg("连接数据库失败，数据库用户名或密码错误");
                    break;
                default :
                    drMsg("连接数据库失败，请检查数据库信息。错误编号：" . $this->geterrno());
                    break;
            }
		}
		$this->prefix = $dbconf['prefix'] ;  //get table prefix
		$this->dbname = $dbconf['database'] ; // get database name 
		if ($this->getMysqlVersion() > '4.1') {
			mysql_query("SET NAMES 'utf8'");
		}
		@mysql_select_db($dbconf['database'], $this->conn) OR drMsg("连接数据库失败，未找到您填写的数据库");
	}

	/**
	 * 静态方法，返回数据库连接实例
	 * $dbconf 为数据库连接配置参数 默认为数组 
	 */
	public static function getInstance($dbconf) {
		if (self::$instance == null) {
			self::$instance = new MySql($dbconf);
		}
		return self::$instance;
	}

	/**
	 * 关闭数据库连接
	 */
	function close() {
		return mysql_close($this->conn);
	}

	/**
     * 取得数据库的表信息
     * @access public
     * @return array
     */
    public function tables($dbName='') {
        if(!empty($dbName)) {
           $sql    = 'SHOW TABLES FROM '.$dbName;
        }else{
           $sql    = 'SHOW TABLES ';
        }
        $info   =   array();
        $result =   $this->query($sql);
        while($ret = mysql_fetch_assoc($result)) {
			$info[] = $ret['Tables_in_'.$this->dbname];
		}  
        return $info;
    }

    /**
     * 执行查询 返回数据集
     * @access public
     * @param string $str  sql指令
     * @return mixed
     */
    public function query($sql) { 
        if ( !$this->conn ) return false;
        $this->lastsql = $sql ;  
        //释放前次的查询结果
        if ( $this->result ) {    $this->free();    } 
        $this->result = @mysql_query($str, $this->conn); 
        if ( false === $this->result ) { 
            $this->error();
            return false;
        } else {
            $this->numRows = mysql_num_rows($this->result);
            return $this->result ;
        }
    } 
	
     /**
     * 获得所有的查询数据
     * @access private
     * @return array
     */
    public function select($sql) {
    	$this->lastsql = $sql ;  
    	$this->result = @mysql_query($sql, $this->conn); 
    	$this->numRows = mysql_num_rows($this->result); 
        //返回数据集
        $result = array();
        if($this->numRows >0) {
            while($row = mysql_fetch_assoc($this->result)){
                $result[]   =   $row;
            }
            mysql_data_seek($this->result,0); 
        }
        return $result;
    }
 
    /**
     * 获得查询数据的一行记录
     * @access private
     * @return array
     */
    public function find($sql) {
    	$this->lastsql = $sql ;  
    	$this->result = @mysql_query($sql, $this->conn); 
    	$this->numRows = mysql_num_rows($this->result);
        //返回数据集
        $row = null ; 
        if($this->numRows >0) { 
            $row = mysql_fetch_assoc($this->result);
            mysql_data_seek($this->result,0); 
        }
        return $row;
    }

    /**
     * 执行语句
     * @access public
     * @param string $str  sql指令
     * @return integer|false
     */
    public function execute($sql) {
    	$this->lastsql = $sql ;  
        if ( !$this->result ) return false; 
        //释放前次的查询结果
        if ( $this->result ) { $this->free(); }    
        $result =  @mysql_query($sql, $this->conn) ; 
        if ( false === $result) {
            $this->error();
            return false;
        } else {
            $this->numRows = mysql_affected_rows($this->conn);
            $this->lastInsID = mysql_insert_id($this->conn);
            return $this->numRows;
        }
    }

    /**
     * 插入记录
     * @access public
     * @param mixed $datas 数据
     * @param array $options 参数表达式
     * @param boolean $replace 是否replace
     * @return false | integer
     */
    public function insert($tableName,$datas,$replace=false) {
        if(!is_array($datas[0])) return false;
        $fields = array_keys($datas[0]); 
        array_walk($fields, array($this, 'parseKey'));
        $values  =  array();
        foreach ($datas as $data){
            $value   =  array();
            foreach ($data as $key=>$val){
                $val   =  $this->parseValue($val);
                if(is_scalar($val)) { // 过滤非标量数据
                    $value[]   =  $val;
                }
            }
            $values[]    = '('.implode(',', $value).')';
        }
        $sql   =  ($replace?'REPLACE':'INSERT').' INTO '.$this->parseTable($tableName).' ('.implode(',', $fields).') VALUES '.implode(',',$values);
        return $this->execute($sql);
    }

    /*格式化表名操作*/
    private function parseTable($tableName){
    	return $this->prefix.$tableName ; //对表名进行装箱处理
    }
    /**
     * 字段名分析
     * @access protected
     * @param string $key
     * @return string
     */
    protected function parseKey(&$key) {
        return $key;
    }

    /**
     * value分析
     * @access protected
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value) {
        if(is_string($value)) {
            $value =  '\''.$this->escapeString($value).'\'';
        }elseif(isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp'){
            $value =  $this->escapeString($value[1]);
        }elseif(is_array($value)) {
            $value =  array_map(array($this, 'parseValue'),$value);
        }elseif(is_bool($value)){
            $value =  $value ? '1' : '0';
        }elseif(is_null($value)){
            $value =  'null';
        }
        return $value;
    }

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */
    public function escapeString($str) {
        if($this->conn) {
            return mysql_real_escape_string($str,$this->conn);
        }else{
            return mysql_escape_string($str);
        }
    }

	/**
	 * 取得上一步 INSERT 操作产生的 ID
	 */
	public function insert_id() {
		return mysql_insert_id($this->conn);
	}

	/**
     * 数据库错误信息
     * 并显示当前的SQL语句
     * @access public
     * @return string
     */
    public function error() {
        $this->error = mysql_errno().':'.mysql_error($this->conn);
        if('' != $this->lastsql){
            $this->error .= "\n [ SQL语句 ] : ".$this->lastsql;
        }
        return $this->error;
    }  

    /**
	 * 获取mysql错误编码
	 */
	function geterrno() {
		return mysql_errno();
	} 

	/**
	 * 取得数据库版本信息
	 */
	function getMysqlVersion() {
		return mysql_get_server_info();
	}  

	/**
	 * 释放查询资源
	 * @return void
	 */
	public function free() {
		if(is_resource($this->result)) {
			mysql_free_result($this->result);
			$this->result = null;
		}
	}

	/*
	* Return last sql  返回执行的最后一条sql语句
	*/
	function lastsql(){
		return $this->lastsql ; 
	}

}
