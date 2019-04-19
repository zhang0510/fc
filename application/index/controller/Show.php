<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db; 
use think\Session;

/**
 * 
 */
class Show extends Common
{
	public function index(){
		return view("show/index");
	}
}
?>