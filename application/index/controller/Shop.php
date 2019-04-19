<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Request;
use think\Session;
use think\Db; 
use app\index\model\Shops;


class Shop extends Common
{
    public function shop_add(){
        $data = db("type")->select();
        $res = $this->getCategory($data);
        return view('shop/shop_add',['res'=>$res]);
    }
    
    //递归
    public function getCategory($data,$type_pid=0,$level=1){
        static $new_arr = array();
        foreach ($data as $k=>$v){
            if($v['type_pid'] == $type_pid){
                $v['level'] = $level;
                $new_arr[] = $v;
                $this->getCategory($data,$v['type_id'],$level+1);
            }
        }
        return $new_arr;
    }


    //商品添加
    public function shopadd(){
    	$request = Request::instance();
        $data = $request->post();

        $file = request()->file('shop_img');

        $info = $file->move(ROOT_PATH . 'public' .DS .'uploads');

        $db = $info->getSaveName();

        $img = str_replace("\\","/","$db");

        $data['shop_img']=$img;

        $models = new Shops();

       if($models->getAdd($data)){
        $this->redirect('shop/shop_list');
       } else {
        $this->error('新增失败','shop/shop_add');
       }
    }

    //商品展示
	public function shop_list(){
        $uid  = Session::get('uid');
         $con = [];
            if(Request::instance()->isPost())
            {
                $name = input('post.name');
                $con['shop_name'] = array('like', "%$name%");
            }
		
        $data = db("shop")->where($con)->join('type', 'shop.type_id=type.type_id')->paginate(3);
        return view('shop/shop_list',['data'=>$data]);
	}

    
    //商品删除
	public function delete()
    {
        $request = Request::instance();
        $shop_id = $request->post('shop_id');
        $model = new Shops();
        if($model->getDele($shop_id)){
            echo 1;
        }else{
            echo 0;
        }
    }

    //修改页面
    public function shop_upd(){
        $queryId = input();
        $model = new Shops();
        $res = $model -> shop_upd($queryId['shop_id']);
        return view('shop/shop_upd',['res'=>$res]);
    }

    //修改内容
    public function shop_upd_do(){
        $models = new Shops();
        $request = Request::instance();
        $data = $request->post();
        $file = request()->file('shop_img');
        if($file){
            $info = $file->move(ROOT_PATH . 'public' .DS .'uploads');
            $db = $info->getSaveName();
            $img = str_replace("\\","/","$db");
            $data['shop_img']=$img;
        }else{
            $res =  $models -> findImg($data['shop_id']);
            $data['shop_img'] =$res['shop_img'];
        }
        $res = $models -> shop_upd_do($data);
        if($res){
            return $this->redirect('shop_list');
        }
    }

}
