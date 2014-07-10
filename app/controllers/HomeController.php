<?php

class HomeController extends BaseController {
 	
 	public function index(){
 		echo 'HomeController'.'_index';
 	}
	public function showWelcome()
	{
		return View::make('hello');
	}

}