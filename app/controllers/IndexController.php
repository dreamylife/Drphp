<?php

class IndexController extends BaseController {

	public function index(){
		$sql = 'SELECT * FROM l_users ' ;
		//$ret = $this->db->select($sql); 
		$ret = $this->db->find($sql) ; 
		print_r($ret) ;
		echo '<h1>这里是首页控制器</h1>' ; 
	}
	public function showWelcome()
	{
		echo 'showWelcome function.....' ;
	}

}