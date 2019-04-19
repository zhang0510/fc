<?php  
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

/**
 * 
 */
class Common extends Controller {
	/*构造方法*/
	function __construct() {
		parent::__construct();
		$this->checkLogin();
	}
	/*验证登录操作*/
	protected function checkLogin() {
		/*如果登录标志不存在*/
		if(!Session::has('uid')) {
			$this->success('请登录',url('Login/login'));
		}
	}
}
?>