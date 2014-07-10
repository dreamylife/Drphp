<?php
/**
 * 模型类库
 * @copyright (c) Drphp All Rights Reserved
 */
class Model{

	/**
     * @var 数据库DB操作实例  
     */
    public $db = '' ; 

    /**
    * @var 事务处理状态 
    */
    protected $Autocommit = '' ; 

	function __construct() {
		$this->db = Database::getInstance();
	}

	//获取数据列表
	final public function GetList($sql,$info=array('type'=>1,'key'=>'')){
		if(empty($sql)) return false;
		if(!is_array($info)) return false;		
		$sql = self::replacesql($sql);
		$this->db->query($sql) ;
		$type = isset($info['type']) ? $info['type'] : 1;
		$key = isset($info['key']) ? $info['key'] : '';		
		return $this->db->get_fetch_type($type,$key);
	
	}
	//获取单条数据
	final public function GetOne($sql,$info=array('type'=>1)){
		if(empty($sql)) return false;
		if(!is_array($info)) return false;	
		$type = isset($info['type']) ? $info['type'] : 1;
		$sql = self::replacesql($sql);	
		$this->db->query($sql);
		return $this->db->fetch_array(NULL,$type);
	}
	//获取分页数据
	final public function GetPage($sql,$info=array('type'=>1,'key'=>'')){
		if(empty($sql)) return false;
		if(!is_array($info)) return false;		
		$page = intval($info['page']) ? intval($info['page']) : 1;
		if($page<=0) {
			$page=1;
		}
		$sql = self::replacesql($sql);
		$num = (!empty($info['num'])) ? intval($info['num']) : 20;		
		$sql = str_ireplace('limit','limit',$sql);
		$sql = explode('limit',$sql);
		$sql = trim($sql[0]);		
		$limit = " LIMIT ".($page-1)*$num.",".$num;
		$sql = $sql.$limit;			
		$this->db->query($sql);
		$type = isset($info['type']) ? $info['type'] : 1;
		$key = isset($info['key']) ? $info['key'] : '';	
		return $this->db->get_fetch_type($type,$key);
	}
	//获取数据总数1
	final public function GetCount($sql){	
		if(empty($sql)) return false;		
		$sql = self::replacesql($sql);
		$sql = preg_replace ("/^SELECT (.*) FROM/i", "SELECT COUNT(*) FROM",$sql);		
		$lastresult = $this->db->execute($sql);
		return $this->db->num_count($lastresult);
	}
	//获取数据总数2
	final public function GetNum($sql){
		if(empty($sql)) return false;
		$sql = self::replacesql($sql);
		$lastresult = $this->db->execute($sql);
		return $this->db->num_rows($lastresult);
	}
	//替换sql 
	final static private function replacesql($sql){
		return $sql;
	}
	//返回查询资源结果集
	public function Query($sql){
		if(empty($sql)) return false ;
		$sql = self::replacesql($sql) ;
		$this->db->query($sql) ; 
		return $this->db->result ;
	}

	//返回插入最后一次的ID
	final public function insert_id(){	
		return $this->db->insert_id();
	}

	//影响的行数
	final public function affected_rows($link=null){
		if(empty($link))
			return $this->db->affected_rows();
		else		
			return mysql_affected_rows($link);
	}

	final public function Autocommit_off(){		
		$this->db->query('SET AUTOCOMMIT=1');
	}
	final public function Autocommit_no(){		
		$this->db->query('SET AUTOCOMMIT=0');
	}	
	//开启事务
	final public function Autocommit_start(){		
		$this->db->query('START TRANSACTION');
		$this->Autocommit = 'start';
	}
	//成功执行
	final public function Autocommit_commit(){		
		$this->db->query('COMMIT');
		$this->Autocommit = 'commit';
	}	
	//回滚事务
	final public function Autocommit_rollback(){
		$this->db->query('ROLLBACK');
		$this->Autocommit = 'rollback';
	}
	//析构函数 
	public function __destruct(){ 
		//mysql_close();
	}

}
