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
     return Db::table('role')->select();
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