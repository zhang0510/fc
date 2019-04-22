<?php
namespace app\admin\controller;

use Wxdev\WXBizDataCrypt;
use think\config;
use think\Session;
use think\Db;

class Wx
{
    public function index()
    {
    	$code = input('get.code');
        //appid
		$APPID = config::get("wechat.wx_appid");
		//appscret
		$AppSecret = config::get("wechat.wx_appsecret"); 
		//接口域名
		$wx_request_url = config::get("wechat.wx_request_url"); 
		//调用微信接口参数
		$param = array( 
	        'appid' => $APPID, 
	        'secret' => $AppSecret, 
	        'js_code' => $code, 
	        'grant_type' => 'authorization_code'
	      ); 
		$arr = http_send($wx_request_url, $param, 'post'); 
		$arr = json_decode($arr,true);
		if(isset($arr['errcode']) && !empty($arr['errcode'])){
		  return json(['code'=>'2','message'=>$arr['errmsg'],"result"=>null]);
		}
		$openid = $arr['openid'];
		$session_key = $arr['session_key'];

		// 数据签名校验
		$signature = input("signature");
		$signature2 = sha1($_GET['rawData'].$session_key);  //框架自带的input会过滤掉必要的数据
		if ($signature != $signature2) {
		  $msg = "shibai 1";
		  return json(['code'=>'2','message'=>'获取失败',"result"=>$msg]);
		}

		//开发者如需要获取敏感数据，需要对接口返回的加密数据( encryptedData )进行对称解密
		$encryptedData = $_GET['encryptedData'];
		$iv = $_GET['iv'];
		include_once (EXTEND_PATH. 'wxdev/wxBizDataCrypt.php');
		$pc = new \WXBizDataCrypt($APPID, $session_key);
		$errCode = $pc->decryptData($encryptedData, $iv, $data);  //其中$data包含用户的所有数据
		if ($errCode != 0) {
		  return json(['code'=>'2','message'=>'获取失败',"result"=>null]);
		}
		//生成第三方3rd_session
		$session3rd  = null;
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($strPol)-1;
		for($i=0;$i<16;$i++){
		  $session3rd .=$strPol[rand(0,$max)];
		}
		//设置session
		Session::set('openid',$openid);
		$openid = Session::get('openid');
		return json(['code'=>'1','message'=>$openid,"result"=>$openid]);

    }

    public function gettype(){
    	$serch = input('post.serch');
    	$type = input('post.type');
    	$type = Db::table('type')->where('type_pid',$type)->select();
    	foreach( $type as $k=>&$v ){
    		$map['type_pid'] = array('eq',$v['type_id']);
    		if($serch != ''){
    			$map['type_name'] = array('like','%'.$serch.'%');
    		}
    		$child = Db::table('type')->where($map)->select();
    		$v['children'] = $child;
    		$v['ishaveChild'] = empty($child)?false:true;
    	}
    	return json_encode(['code'=>'1','message'=>'获取成功',"result"=>$type]);
    }

    public function getshop(){
    	$serch = input('post.serch');
    	$typeid = input('post.typeid');
    	$map['type_id'] = array('eq',$typeid);
    	$map['shop_status'] = array('eq','1');
		if($serch != ''){
			$map['shop_name'] = array('like','%'.$serch.'%');
		}
    	$list = Db::table('shop')->field('shop_id,shop_name,shop_img')->where($map)->select();
    	if( $list ){
    		$ishave = empty($list)?'false':'true';
    		return json_encode(['code'=>'1','message'=>'获取成功',"result"=>['ishave'=>$ishave,'list'=>$list]]);
    	}else{
    		return json_encode(['code'=>'2','message'=>'获取失败',"result"=>'']);
    	}
    	
    }

    public function getdetails(){
    	$shop_id = input('post.shopid');
    	$map['shop_id'] = array('eq',$shop_id);
    	$shop_details = Db::table('shop')->where($map)->value('shop_details');
    	if( $shop_details ){
    		return json_encode(['code'=>'1','message'=>'获取成功',"result"=>$shop_details]);
    	}else{
    		return json_encode(['code'=>'2','message'=>'获取失败',"result"=>'']);
    	}
    	
    }
}
