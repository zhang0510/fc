<?php  
namespace app\index\model;

use think\Model;
use think\Request;
use think\Db;

class Mys extends Model
{
    //查询数据
	public function findUserId($old_pwd,$uid){
        $sql = "select uid from user where uid = '".$uid."' and upwd = '".$old_pwd."'";
        $data = Db::query($sql);
        if(empty($data)){
            return false;
        }else{
            return true;
        }
        
    }

    //修改密码
    public function changePwd($id,$new_pwd){
        $sql = "update user set upwd = '".$new_pwd."' where uid = ".$id;
        $data = Db::query($sql);
        return $data;
    }

}
?>