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
        $res = db("type")->where("type_pid=0")->select();

        return view('shop/shop_add',['res'=>$res]);
    }
    
    //点击出现下一级
    public function Addre(){
        $region_id = input('get.region_id');
       
        $data = db('type')->where("type_pid = '$region_id'")->select();

        echo json_encode($data);
    }


    //商品添加
    public function shopadd(){
    	$request = Request::instance();
        $data = $request->post();
         //var_dump($data);die;   
        $file = request()->file('shop_img');
        if (empty($file)) { 
          $this->error('请选择上传文件'); die;
        } 

        $validata = ['size' => 500*1024, 'ext' => 'jpg,png,gif,jpeg'];
        $info = $file->validate($validata)->move(ROOT_PATH . 'public' . DS . 'uploads');
        if(!$info){
            // 上传失败获取错误信息
            $msg = $file->getError();
            if( $msg == '上传文件大小不符！')
            {
                $this->error('文件大小不能超过500kb!');die;
            }
            elseif( $msg == '上传文件后缀不允许')
            {
                $this->error('文件格式错误!');die;
            }
            else
            {
                $this->error('上传失败!');die;
            }
        }

        $db = $info->getSaveName();

        $img = str_replace("\\","/","$db");

        $data['shop_img']=$img;
        $data['shop_status'] = 1;
        
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
		
        $data = db("shop")->where($con)->join('type', 'shop.type_id=type.type_id')->order('shop_id desc')->paginate(10);
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

    public function shop_upd(){
        $queryId = input();
        $model = new Shops();
        $res = $model -> shop_upd($queryId['shop_id']);
        $type_child = Db::table('type')->where('type_id','=',$res['type_id'])->find();
        $type = Db::table('type')->where('type_id','=',$type_child['type_pid'])->find();
        $type_arr = ['type' =>$type['type_pid'], 'type_one' =>$type_child['type_pid'], 'type_two' =>$type_child['type_id']];
        $type_one = Db::table('type')->where('type_pid','=','0')->select();
        $type_two = Db::table('type')->where('type_pid','=',$type_arr['type'])->select();
        $type_three = Db::table('type')->where('type_pid','=',$type_arr['type_one'])->select();

        return view('shop/shop_upd',['res'=>$res,'type_arr'=>$type_arr,'type_one'=>$type_one,'type_two'=>$type_two,'type_three'=>$type_three]);
    }

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
        $res = $models ->shop_upd_do($data);
        if($res){
            return $this->redirect('shop_list');
        }
    }


    public function shop_edit(){
        $shop_id = $_GET['shop_id'];
        $shop_status = $_GET['shop_status'];

        $res = db::execute("update shop set shop_status = '$shop_status' where shop_id = '$shop_id'");
        if($res){
            echo 0;
        }
        else{
            echo 1;
        }
    }

}
