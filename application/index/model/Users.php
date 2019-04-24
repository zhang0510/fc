<?php  
namespace app\index\model;

use think\Model;
use think\Request;
use think\Db;

class Users extends Model
{
	//展示
	public function getrole()
    {
     $con['role_name'] = ['neq','超级管理员'];
     return Db::table('role')->where($con)->select();
    }

	public function getUserInfoName($uname){
	  return Db::table('user')->where('uname',$uname)->find();
    }

    public function useradd_do($addarray)
    {
     $res  = Db::table('user')->insert($addarray);
     return $res;
    }
    
   
	//删除.
	public function getDele($user_id,$uid){
		
		$data = Db::table('user')->where('uid','=',$user_id)->find();
		if($data['uname'] == 'admin'){
			$del = Db::table('user')->where('uid',$uid)->delete();
			if($del){
				return 1; 
			}else{
				return 0;   //删除失败；
			}
			
		}else{
			return 2;
		}
		
	}
	
}
?>