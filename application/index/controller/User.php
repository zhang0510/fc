<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Request;
use think\Session;
use think\Db; 
use app\index\model\Users;

/**
 * 
 */
class User extends Common
{
	
    //用户展示
	public function user_list(){
        $uid  = Session::get('uid');
        $con['uname'] = ['neq','admin'];
            if(Request::instance()->isPost())
            {
                $name = input('post.name');
                $con['uname'] = array('like', "%$name%");
            }
        $data = db("user")->where($con)->join('role', 'user.role_id=role.role_id')->paginate(10);
        return view('user/user_list',['data'=>$data]);
    }

	
    public function user_add(){
        $model = new Users();
        $data = $model->getrole();
        return view('user/user_add',['data'=>$data]);
    }

   
    public function useradd(){
        $uname = $_POST['uname'];
        $upwd = md5($_POST['upwd']);
        $openid = '';
        $role_id = $_POST['role_id'];

        $addarray = array(
            'uid'=>null,
            'uname'=>$uname,
            'upwd'=>$upwd,
            'openid'=>$openid,
            'role_id'=>$role_id,
            );

        $model = new Users();
        $data = $model->getUserInfoName($uname);
         if (!empty($data)){
            $this->error('该用户名已存在','user/useradd');
        } else{
            $res = $model->useradd_do($addarray);

           if($res){
		      $this->redirect('user/user_list');
		    } else {
		      $this->error('新增失败','type/useradd');
		    }
        }


    }

    public function delete()
    {
        $user_id  = Session::get('uid');
    
        $request = Request::instance();
        $uid   = $request->get('id');
        $model = new Users();
        $data  = $model->getDele($user_id,$uid);
        
        if($data == 1){
             $this->error('删除成功');
        }else if($data == 0){
            $this->error('失败');
        }else{
            $this->error('您无权限删除！！！！');
        }
    }

	

}


?>