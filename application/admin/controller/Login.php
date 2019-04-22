<?php
namespace app\admin\controller;

use  think\Controller;
use  think\config;
use  think\Db;
use  think\Session;

class Login extends Controller
{
	/**
	  * 登陆
	*/
	public function login(){
		$uname = input('post.uname');
		$upwd = md5(input('post.upwd'));
		$thirdSession = input('post.thirdSession');
		$isset_session = Session::has('openid');
		$openid = Session::get('openid');
		$result = Db::table('user')->field('uid,uname,role_id')->where(['uname'=>$uname,'upwd'=>$upwd])->find();
		if( $result ){
			Db::table('user')->where('openid',$openid)->update(['openid'=>'']);
			Db::table('user')->where('uid',$result['uid'])->update(['openid'=>$openid]);
			return json(['code'=>'1','message'=>'登录成功',"result"=>$result['role_id']]);
		}else{
			return json(['code'=>'2','message'=>'登录失败',"result"=>'']);
		}
	}

	/**
	  * 退出登陆
	*/
	public function backlogin(){
		$thirdSession = input('post.thirdSession');
		$openid = Session::get('openid');
		Db::table('user')->where('openid',$openid)->update(['openid'=>null]);
		$result = Db::table('user')->field('uid')->where('openid',$openid)->find();
		if( empty($result) ){
			return json(['code'=>'1','message'=>'退出成功',"result"=>$result['uname']]);
		}else{
			return json(['code'=>'2','message'=>'退出失败',"result"=>'']);
		}
	}

	/**
	  * 检测是否绑定
	*/
	public function opuser(){
		$thirdSession = input('post.thirdSession');
		$isset_session = Session::has('openid');
		$openid = Session::get('openid');
		if($isset_session){
			$result = Db::table('user')->field('role_id,uname')->where('openid',$openid)->find();
			if( !empty( $result ) ){
				return json(['code'=>'1001','message'=>'已绑定',"result"=>$result]);
			}else{
				return json(['code'=>'1002','message'=>'未绑定',"result"=>'']);
			}
		}else{
			return json(['code'=>'2001','message'=>'未登录',"result"=>'']);
		}
		
	}
}