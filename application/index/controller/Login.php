<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db; 
use think\Session;


class Login extends Controller
{
	//渲染登录页面
	public function login(){
		Session::delete('uid');
		return view("login/login");
	}

	//登录
	public function login_do(){

		$name = Request::instance()->post('uname','','htmlspecialchars');
		$upwd = md5(Request::instance()->post('upwd','','htmlspecialchars'));
		$where = " AND role_id in (1,2)";
		//判断用户名是否存在
		$uname = Db::query("select uid,uname,upwd from user where uname ='$name'" . $where);

		if(empty($uname)){
            echo json_encode(['code'=>'1','msg'=>'用户名不存在']);exit;
        }

		$res = Db::query("select uid from user where uname = '".$name."' and upwd = '".$upwd."'" . $where);
		if(empty($res)){
            echo json_encode(['code'=>'2','msg'=>'密码不正确']);exit;
        }else{
		    $uid = $res['0']['uid'];
            Session::set('uid',$uid);
            echo json_encode(['code'=>'0','msg'=>'登录成功']);
        }

	}
}
?>