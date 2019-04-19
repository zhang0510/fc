<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Request;
use think\Session;
use think\Db; 
use app\index\model\Types;


class Type extends Common
{
	 //渲染分类页
    public function type_add(){
        $data = db("type")->select();
        $res = $this->getCategory($data);
        return view('type/type_add',['res'=>$res]);
    }
    
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

	//列表展示
    public function type_list(){
        $uid  = Session::get('uid');
         $con = [];
            if(Request::instance()->isPost())
            {
                $name = input('post.name');
                $con['type_name'] = array('like', "%$name%");
            }
        $data = db("type")->where($con)->select();
        $res = $this->getCategory($data);
        return view('type/type_list',['res'=>$res]);
    }


    

    //分类添加
    public function typeadd(){
        $request = Request::instance();
        $data = $request->post();

        $file = request()->file('type_img');

        $info = $file->move(ROOT_PATH . 'public' .DS .'uploads');

        $db = $info->getSaveName();

        $img = str_replace("\\","/","$db");

        $data['type_img']=$img;

        $models = new types();

       if($models->getAdd($data)){
        $this->redirect('type/type_list');
       } else {
        $this->error('新增失败','type/type_add');
       }

    }

    public function delete()
    {
        $request = Request::instance();
        $type_id   = $request->get('id');
        $model = new Types();
        $data  = $model->getDele($type_id);
        if($data == 1){
             $this->error('删除成功');
        }else if($data == 0){
            $this->error('失败');
        }else{
            $this->error('一级不能删！！！！');
        }
    }


     public function type_upd(){
        $queryId = input();
        $model = new Types();
        $res = $model -> type_upd($queryId['type_id']);
        //var_dump($res);die;
        if($res['type_pid'] == 0){
            $this->error('一级不能修改');
        }else{
            return view('type/type_upd',['res'=>$res]);
        }
        
    }

    public function type_upd_do(){
        $models = new Types();
        $request = Request::instance();
        $data = $request->post();
        $file = request()->file('type_img');
        if($file){
            $info = $file->move(ROOT_PATH . 'public' .DS .'uploads');
            $db = $info->getSaveName();
            $img = str_replace("\\","/","$db");
            $data['type_img']=$img;
        }else{
            $res =  $models -> findImg($data['shop_id']);
            $data['type_img'] =$res['type_img'];
        }
        $res = $models ->type_upd_do($data);
        if($res){
            return $this->redirect('type_list');
        }
    }
	

}


?>