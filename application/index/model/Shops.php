<?php  
namespace app\index\model;

use think\Model;
use think\Request;
use think\Db;

class Shops extends Model
{
	//展示
	protected $table='shop';
	
	//添加
	public function getAdd($data){
		return Db::table($this->table)->insertGetId($data);
	}
	
	//删除
	public function getDele($shop_id){
		return Db::table('shop')->where('shop_id','=',$shop_id)->delete();
	}

	public function shop_upd($shop_id){
        return Db::table('shop')
                    ->join('type', 'shop.type_id=type.type_id')
                    ->where('shop_id','=',$shop_id)
                    ->find();
    }

    public function shop_upd_do($data){
	    return Db::table($this -> table)->where('shop_id','=',$data['shop_id'])->update($data);
    }
    
    public function findImg($shop_id){
	    return Db::table($this -> table)->field("shop_img")->where('shop_id','=',$shop_id)->find();
    }

}
?>