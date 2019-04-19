<?php
namespace app\index\controller;

use  think\Controller;
use  think\config;
use  think\Db;
use  think\Session;

class index extends Controller
{
	public function index(){
		echo md5('111111');
	}
}