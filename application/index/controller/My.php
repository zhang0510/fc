<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Request;
use think\Session;
use think\Db; 
use app\index\model\Mys;

/**
 * 修改密码
 */
class My extends Common
{
	 //渲染页面
    public function myupd(){
        return view('my/myupd');
    }

    //修改密码
    public function changePassword(){
        
        $old_pwd   =  md5($_POST['old_pwd']);
        $new_pwd   =  md5($_POST['new_pwd']);
        $check_pwd =  md5($_POST['check_pwd']);
        $uid  = Session::get('uid');
        //echo $uid;exit;

        if($new_pwd != $check_pwd){
            echo json_encode(['code'=>1001,'msg'=>"两次密码不一致"],JSON_UNESCAPED_UNICODE);exit;
        }
        $model = new Mys();
        $id = $model -> findUserId($old_pwd,$uid);

        if($id){
            $res = $model -> changePwd($uid,$new_pwd);

            if($res){
                echo json_encode(['code'=>2000,'msg'=>"修改失败"],JSON_UNESCAPED_UNICODE);exit;
            }else{
                echo json_encode(['code'=>0,'msg'=>"修改成功"],JSON_UNESCAPED_UNICODE);exit;
            }
        }else{
            echo json_encode(['code'=>1002,'msg'=>"请输入正确的旧密码"],JSON_UNESCAPED_UNICODE);exit;
        }
    }

}


?>