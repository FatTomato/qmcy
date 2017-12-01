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
	protected $shop_relationships_model;
	protected $categroys_model;
	
	function _initialize() {
		parent::_initialize();
		$this->shop_model = D("Portal/Shop");
		$this->categroys_model = D("Portal/Categorys");
		$this->shop_relationships_model = D("Portal/ShopRelationships");
	}
	
	// 后台店铺管理列表
	public function index(){
		$this->_lists();
		$this->_getTree();
		$this->display();
	}
	
	// 店铺添加
	public function add(){

		$this->_getTermTree();

		$this->display();
	}
	
	// 店铺添加提交
	public function add_post(){
		if (IS_POST) {

			$shop=I("post.post");
			$member_id = M('Member')->where(array('phone'=>$shop['phone']))->getField('member_id');
			$shop['member_id'] = isset($member_id)? $member_id: 0;
			$shop['shop_pic'] = sp_asset_relative_url($_POST['shop_pic']);
			$shop['shop_detail'] = htmlspecialchars_decode($shop['shop_detail']);
			$key = C('TXMAP');
			$addr = $shop['shop_addr'];
			$url = 'http://apis.map.qq.com/ws/geocoder/v1/?address='.$addr.'&key='.$key;
			$lat_lng = http_get($url);
			if($lat_lng['status'] !== 0){
				$this->error("位置解析失败！");
			}
			$shop['lat'] = $lat_lng['result']['location']['lat'];
			$shop['lng'] = $lat_lng['result']['location']['lng'];
			$shop['add_time'] = date('Y-m-d H:i:s');
			$shop['post_status']=0;
			$result=$this->shop_model->add($shop);
			if ($result) {
				
				$re = $this->shop_relationships_model->add(array("cg_id"=>intval($_POST['cg_id']),"shop_id"=>$result));
				
				if($re){
					$this->success("添加成功！");
				} else {
					$this->error("添加失败！");
				}
			}else {
					$this->error("添加失败！");
				}
			 
		}
	}
	
	// 店铺编辑
	public function edit(){
		$id=  I("get.id",0,'intval');
		
		$shop_relationship = M('ShopRelationships')->where(array("shop_id"=>$id,"status"=>1))->getField("cg_id",true);
		$this->_getTermTree($shop_relationship);
		$shop=$this->shop_model->where("id=$id")->find();
		$shop['phone'] = M('Member')->where(array('member_id'=>$shop['member_id']))->getField('phone');
		$this->assign("shop",$shop);
		$this->display();
	}
	
	// 店铺编辑提交
	public function edit_post(){
		if (IS_POST) {

			$shop=I("post.post");
			$shop['shop_pic'] = sp_asset_relative_url($_POST['shop_pic']);
			$shop['shop_detail']=htmlspecialchars_decode($shop['shop_detail']);
			$key = C('TXMAP');
			$addr = $shop['shop_addr'];
			$url = 'http://apis.map.qq.com/ws/geocoder/v1/?address='.$addr.'&key='.$key;
			$lat_lng = http_get($url);
			if($lat_lng['status'] !== 0){
				$this->error("位置解析失败！");
			}
			$shop['lat'] = $lat_lng['result']['location']['lat'];
			$shop['lng'] = $lat_lng['result']['location']['lng'];
			$result=$this->shop_model->save($shop);
			if ($result!==false) {
				$re = $this->shop_relationships_model->where(array('shop_id'=>$_POST['post']['id']))->save(array('cg_id'=>$_POST['cg_id']));
				if($re !== false){
					$this->success("保存成功！");
				} else {
					$this->error("保存sss失败！");
				}
			} else {
				$this->error("保存失败！");
			}
		}
	}
	
	// 店铺排序
	public function listorders() {
		$status = parent::_listorders($this->shop_relationships_model);
		if ($status) {
			$this->success("排序更新成功！");
		} else {
			$this->error("排序更新失败！");
		}
	}
	
	/**
	 * 店铺列表处理方法,根据不同条件显示不同的列表
	 * @param array $where 查询条件
	 */
	private function _lists($where=array()){
		$cg_id=I('request.cg_id',0,'intval');
		
		if(!empty($cg_id)){
		    $where['b.cg_id']=$cg_id;
		}
		
		$start_time=I('request.start_time');
		if(!empty($start_time)){
		    $where['end_time']=array(
		        array('EGT',$start_time)
		    );
		}
		
		$end_time=I('request.end_time');
		if(!empty($end_time)){
		    if(empty($where['end_time'])){
		        $where['end_time']=array();
		    }
		    array_push($where['end_time'], array('ELT',$end_time));
		}
		
		$keyword=I('request.keyword');
		if(!empty($keyword)){
		    $where['post_title']=array('like',"%$keyword%");
		}
			
		$this->shop_model
		->alias("a")
		->where($where);
		
		
		$this->shop_model->join("__SHOP_RELATIONSHIPS__ b ON a.id = b.shop_id");
		
		
		$count=$this->shop_model->count();
			
		$page = $this->page($count, 20);
			
		$this->shop_model
		->alias("a")
		// ->join("__USERS__ c ON a.post_author = c.id")
		->where($where)
		->limit($page->firstRow , $page->listRows)
		->order("a.add_time");
		
	    $this->shop_model->field('a.*,b.listorder,b.sid');
	    $this->shop_model->join("__SHOP_RELATIONSHIPS__ b ON a.id = b.shop_id");
		
		$shops=$this->shop_model->select();

		foreach ($shops as &$value) {
			$value['mamber_name'] = M('Member')->where(array('member_id'=>$value['member_id']))->getField('username');
		}

		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("shops",$shops);
	}
	
	// 获取店铺分类树结构 select 形式
	private function _getTree(){
		$cg_id=empty($_REQUEST['cg_id'])?0:intval($_REQUEST['cg_id']);
		$result = $this->categroys_model->order(array("listorder"=>"asc"))->select();
		
		$tree = new \Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		foreach ($result as $r) {
			$r['str_manage'] = '<a href="' . U("AdminTerm/add", array("parent" => $r['cg_id'])) . '">添加子类</a> | <a href="' . U("AdminTerm/edit", array("id" => $r['cg_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminTerm/delete", array("id" => $r['cg_id'])) . '">删除</a> ';
			$r['visit'] = "<a href='#'>访问</a>";
			$r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
			$r['id']=$r['cg_id'];
			$r['parentid']=$r['parent'];
			$r['selected']=$cg_id==$r['cg_id']?"selected":"";
			$array[] = $r;
		}
		
		$tree->init($array);
		$str="<option value='\$id' \$selected>\$spacer\$name</option>";
		$taxonomys = $tree->get_tree(0, $str);
		$this->assign("taxonomys", $taxonomys);
	}
	
	// 获取店铺分类树结构 
	private function _getTermTree($term=array()){
		$result = $this->categroys_model->order(array("listorder"=>"asc"))->select();
		
		$tree = new \Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		foreach ($result as $r) {
			$r['str_manage'] = '<a href="' . U("AdminTerm/add", array("parent" => $r['cg_id'])) . '">添加子类</a> | <a href="' . U("AdminTerm/edit", array("id" => $r['cg_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminTerm/delete", array("id" => $r['cg_id'])) . '">删除</a> ';
			$r['visit'] = "<a href='#'>访问</a>";
			$r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
			$r['id']=$r['cg_id'];
			$r['parentid']=$r['parent'];
			$r['selected']=in_array($r['cg_id'], $term)?"selected":"";
			$r['checked'] =in_array($r['cg_id'], $term)?"checked":"";
			$array[] = $r;
		}
		
		$tree->init($array);
		$str="<option value='\$id' \$selected>\$spacer\$name</option>";
		$taxonomys = $tree->get_tree(0, $str);
		$this->assign("taxonomys", $taxonomys);
	}
	
	// 店铺审核
	public function check(){
		if(isset($_POST['ids']) && $_GET["check"]){
		    $ids = I('post.ids/a');
			
			if ( $this->shop_model->where(array('id'=>array('in',$ids)))->save(array('status'=>1)) !== false ) {
				$this->success("审核成功！");
			} else {
				$this->error("审核失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["uncheck"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->shop_model->where(array('id'=>array('in',$ids)))->save(array('status'=>0)) !== false) {
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
	
	// 清除已经删除的店铺
	public function delete(){
		// if(isset($_POST['ids'])){
		// 	$ids = I('post.ids/a');
		// 	$ids = array_map('intval', $ids);
		// 	$status=$this->shop_model->where(array("id"=>array('in',$ids)))->delete();
		// 	$this->shop_relationships_model->where(array('object_id'=>array('in',$ids)))->delete();
			
		// 	if ($status!==false) {
		// 		$this->success("删除成功！");
		// 	} else {
		// 		$this->error("删除失败！");
		// 	}
		// }else{
		// 	if(isset($_GET['id'])){
		// 		$id = I("get.id",0,'intval');
		// 		$status=$this->shop_model->where(array("id"=>$id))->delete();
		// 		$this->shop_relationships_model->where(array('object_id'=>$id))->delete();
				
		// 		if ($status!==false) {
		// 			$this->success("删除成功！");
		// 		} else {
		// 			$this->error("删除失败！");
		// 		}
		// 	}
		// }
	}
	
}