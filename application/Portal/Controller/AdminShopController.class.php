<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;

use Common\Controller\AdminbaseController;

class AdminShopController extends AdminbaseController {
    
	protected $shop_model;
	protected $categorys_model;
	
	function _initialize() {
		parent::_initialize();
		$this->shop_model = D("Portal/Shop");
		$this->categorys_model = D("Portal/Categorys");
	}
	
	// 后台店铺管理列表
	public function index(){
		$this->_lists();
		$this->cg_id = $this->categorys_model->where(array('type'=>2))->order(array("listorder"=>"asc"))->getField('cg_id,name');
		$this->level = [1=>'初级版',2=>'豪华版',3=>'已过期'];
		$this->check = [1=>'未审核',2=>'已审核'];
		$this->is_recruit = [1=>'无',2=>'有'];
		$this->is_sale = [1=>'无',2=>'有'];
		$this->deposit = [1=>'未缴纳',2=>'缴纳'];
		$this->display();
	}
	
	// 店铺编辑
	public function edit(){
		$id=  I("get.id",0,'intval');
		$this->cg_id = $this->categorys_model->where(array('type'=>2))->order(array("listorder"=>"asc"))->getField('cg_id,name');
		$shop=$this->shop_model->where("id=$id")->find();
		$shop['shop_pic'] = $shop['shop_pic'] !== ''? explode(',', $shop['shop_pic']): [];
		$shop['shop_contact'] = M('Member')->where(array('member_id'=>$shop['member_id']))->getField('username');
		$this->assign("shop",$shop);
		$this->display();
	}
	
	private function _lists($where=array()){
		// 店铺分类
		$cg_id=I('request.cg_id',0,'intval');
		if(!empty($cg_id)){
		    $where['cg_id']=$cg_id;
		}
		// 是否豪华版
		$level=I('request.level',0,'intval');
		if($level == 1){
		    $where['vip_time'] = '1000-01-01 00:00:00';
		}elseif($level == 2){
			$where['level'] = 1;
		}elseif($level == 3){
			$map['level']  = array('eq', '0');
			$map['vip_time']  = array('neq','1000-01-01 00:00:00');
			$map['_logic'] = 'and';
			$where['_complex'] = $map;
		}
		// 是否审核
		$check=I('request.check',0,'intval');
		if(!empty($check)){
		    $where['check']=$check==1?0:1;
		}
		// 是否活动
		$is_sale=I('request.is_sale',0,'intval');
		if(!empty($is_sale)){
		    $where['is_sale']=$is_sale==1?0:1;
		}
		// 是否招聘
		$is_recruit=I('request.is_recruit',0,'intval');
		if(!empty($is_recruit)){
		    $where['is_recruit']=$is_recruit==1?0:1;
		}
		// 是否押金
		$deposit=I('request.deposit',0,'intval');
		if(!empty($deposit)){
		    $where['deposit']=$deposit==1?0:1;
		}
		// 快到期的
		$vip_time=I('request.vip_time');
		if(!empty($vip_time)){
		    $now = date('Y-m-d H:i:s');
		    $where['vip_time'] = array(array('gt',$now),array('elt',$vip_time)) ;
		}
		// 关键词查询
		$keyword=I('request.keyword');
		if(!empty($keyword)){
		    $map1['shop_name']  = array('like', '%'.$keyword.'%');
			$map1['shop_major']  = array('like','%'.$keyword.'%');
			$map1['_logic'] = 'or';
			$where['_complex'] = $map1;
		}
			
		$this->shop_model->where($where);
		
		$count=$this->shop_model->count();
			
		$page = $this->page($count, 20);
			
		$this->shop_model
		->where($where)
		->limit($page->firstRow , $page->listRows)
		->order("add_time desc");
		
	    $this->shop_model->field('*');
		
		$shops=$this->shop_model->select();

		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("shops",$shops);
	}
	
	// 店铺排序
	public function listorders() {
		$ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['listorder'] = $r;
            $this->shop_model->where(array('id' => $key))->save($data);
        }

		$this->success("排序更新成功！");
	}
	
	// 店铺审核
	public function check(){
		if(isset($_POST['ids']) && $_GET["check"]){
		    $ids = I('post.ids/a');
			
			if ( $this->shop_model->where(array('id'=>array('in',$ids)))->save(array('check'=>1)) !== false ) {
				$this->success("审核成功！");
			} else {
				$this->error("审核失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["uncheck"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->shop_model->where(array('id'=>array('in',$ids)))->save(array('check'=>0)) !== false) {
				$this->success("取消审核成功！");
			} else {
				$this->error("取消审核失败！");
			}
		}
	}
	
	// 店铺置顶
	public function top(){
		if(isset($_POST['ids']) && $_GET["top"]){
			$ids = I('post.ids/a');
			
			if ( $this->shop_model->where(array('id'=>array('in',$ids)))->save(array('istop'=>1))!==false) {
				$this->success("置顶成功！");
			} else {
				$this->error("置顶失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["untop"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->shop_model->where(array('id'=>array('in',$ids)))->save(array('istop'=>0))!==false) {
				$this->success("取消置顶成功！");
			} else {
				$this->error("取消置顶失败！");
			}
		}
	}
	
	// 店铺推荐
	public function recommend(){
		if(isset($_POST['ids']) && $_GET["recommend"]){
			$ids = I('post.ids/a');
			
			if ( $this->shop_model->where(array('id'=>array('in',$ids)))->save(array('recommended'=>1))!==false) {
				$this->success("推荐成功！");
			} else {
				$this->error("推荐失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["unrecommend"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->shop_model->where(array('id'=>array('in',$ids)))->save(array('recommended'=>0))!==false) {
				$this->success("取消推荐成功！");
			} else {
				$this->error("取消推荐失败！");
			}
		}
	}

	// 店铺上架|下架
	public function setstatus(){
		if(isset($_POST['ids']) && $_GET["status"]){
			$ids = I('post.ids/a');
			
			if ( $this->shop_model->where(array('id'=>array('in',$ids)))->save(array('status'=>1))!==false) {
				$this->success("上架成功！");
			} else {
				$this->error("上架失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["unstatus"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->shop_model->where(array('id'=>array('in',$ids)))->save(array('status'=>0))!==false) {
				$this->success("下架成功！");
			} else {
				$this->error("下架失败！");
			}
		}
	}
	
}