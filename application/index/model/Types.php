<?php  
namespace app\index\model;

use think\Model;
use think\Request;
use think\Db;

class Types extends Model
{
	//展示
	protected $table='type';
	
	
	//添加
	public function getAdd($data){
		return Db::table($this->table)->insertGetId($data);
	}
	
    //删除.
	public function getDele($type_id){
		$del = Db::table('type')->where('type_id',$type_id)->delete();
		if($del){
			return 1; 
		}else{
			return 0;   //删除失败；
		}
	}

	public function type_upd($type_id){
        return Db::table('type')
                         ->where('type_id','=',$type_id)
                         ->find();
    }

    public function type_upd_do($data){
	    return Db::table($this -> table)->where('type_id','=',$data['type_id'])->update($data);
    }
    
    public function findImg($type_id){
	    return Db::table($this -> table)->field("type_img")->where('type_id','=',$type_id)->find();
    }
}
?>