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

class AdminAdsController extends AdminbaseController {
    
	protected $ads_model;
	protected $categorys_model;
	
	function _initialize() {
		parent::_initialize();
		$this->ads_model = D("Portal/Ads");
		$this->categorys_model = D("Portal/Categorys");
		$this->cg_id = $this->categorys_model->where(array('type'=>2))->order(array("listorder"=>"asc"))->getField('cg_id,name');
	}
	
	// 后台文章管理列表
	public function index(){
		$this->_lists();
		$this->display();
	}
	
	// 文章添加
	public function add(){
		$this->display();
	}
	
	// 文章添加提交
	public function add_post(){
		if (IS_POST) {
			
			$_POST['post']['post_modified']=date("Y-m-d H:i:s",time());
			$_POST['post']['post_author']=get_current_admin_id();
			$article=I("post.post");
			$article['smeta'] = sp_asset_relative_url($_POST['smeta']);
			$article['post_content']=htmlspecialchars_decode($article['post_content']);
			$article['post_status']=0;
			if ($article['store_name'] !== '官方') {
				$article['shop_id'] = M('Shop')->where(array('shop_name'=>$article['store_name']))->getField('id');
				if (!$article['shop_id']) {
					$this->error("店铺不存在！");
				}
			}
			// 相册图集
			if(!empty($_POST['photos_url'])){
				foreach ($_POST['photos_url'] as $key=>$url){
					$photourl=sp_asset_relative_url($url);
					$_POST['altas'][]=$photourl;
				}
				$article['altas']=json_encode($_POST['altas']);
			}
			$result=$this->ads_model->add($article);
			if ($article['store_name'] !== '官方') {
				M('Shop')->where(array('shop_name'=>$article['store_name']))->save(array('is_sale'=>1));
			}
			if ($result) {
				$this->success("添加成功！");
			}else {
					$this->error("添加失败！");
			}
			 
		}
	}
	
	// 文章编辑
	public function edit(){
		$id=  I("get.id",0,'intval');
		
		$post=$this->ads_model->where("id=$id")->find();
		$this->assign("post",$post);
		$this->assign("altas",json_decode($post['altas'],true));
		$this->display();
	}
	
	// 文章编辑提交
	public function edit_post(){
		if (IS_POST) {
			
			unset($_POST['post']['post_author']);
			$_POST['post']['post_modified']=date("Y-m-d H:i:s",time());
			$article=I("post.post");
			$article['smeta'] = sp_asset_relative_url($_POST['smeta']);
			$article['post_content']=htmlspecialchars_decode($article['post_content']);
			if(!empty($_POST['photos_url'])){
				foreach ($_POST['photos_url'] as $key=>$url){
					$photourl=sp_asset_relative_url($url);
					$_POST['altas'][]=$photourl;
				}
				$article['altas']=json_encode($_POST['altas']);
			}
			$result=$this->ads_model->save($article);
			if ($result!==false) {
				$this->success("保存成功！");
			} else {
				$this->error("保存失败！");
			}
		}
	}
	
	// 文章排序
	public function listorders() {
		$ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['listorder'] = $r;
            $this->ads_model->where(array('id' => $key))->save($data);
        }
		$this->success("排序更新成功！");
	}
	
	/**
	 * 文章列表处理方法,根据不同条件显示不同的列表
	 * @param array $where 查询条件
	 */
	private function _lists($where=array()){
		$cg_id=I('request.cg_id',0,'intval');
		
		if(!empty($cg_id)){
		    $where['a.cg_id']=$cg_id;
		}
		
		$start_time=I('request.start_time');
		if(!empty($start_time)){
		    $where['a.end_time']=array(
		        array('EGT',$start_time)
		    );
		}
		
		$end_time=I('request.end_time');
		if(!empty($end_time)){
		    if(empty($where['a.end_time'])){
		        $where['a.end_time']=array();
		    }
		    array_push($where['a.end_time'], array('ELT',$end_time));
		}
		
		$keyword=I('request.keyword');
		if(!empty($keyword)){
		    $where['a.post_title']=array('like',"%$keyword%");
		}
			
		$this->ads_model
		->alias("a")
		->where($where);
		
		$count=$this->ads_model->count();
			
		$page = $this->page($count, 20);
			
		$this->ads_model
		->alias("a")
		->join("__USERS__ c ON a.post_author = c.id")
		->where($where)
		->limit($page->firstRow , $page->listRows)
		->order("a.post_date DESC");
		
	    $this->ads_model->field('a.*,c.user_login,c.user_nicename');
		
		$posts=$this->ads_model->select();
		foreach($posts as &$v){
            $v['post_hits'] = !empty($v['post_hits'])? count(explode(',', $v['post_hits'])): 0;
            $v['post_like'] = !empty($v['post_like'])? count(explode(',', $v['post_like'])): 0;
        }

		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("posts",$posts);
	}
	
	// 文章审核
	public function check(){
		if(isset($_POST['ids']) && $_GET["check"]){
		    $ids = I('post.ids/a');
			
			if ( $this->ads_model->where(array('id'=>array('in',$ids)))->save(array('post_status'=>1)) !== false ) {
				$this->success("审核成功！");
			} else {
				$this->error("审核失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["uncheck"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->ads_model->where(array('id'=>array('in',$ids)))->save(array('post_status'=>0)) !== false) {
				$this->success("取消审核成功！");
			} else {
				$this->error("取消审核失败！");
			}
		}
	}
	
	// 文章置顶
	public function top(){
		if(isset($_POST['ids']) && $_GET["top"]){
			$ids = I('post.ids/a');
			
			if ( $this->ads_model->where(array('id'=>array('in',$ids)))->save(array('istop'=>1))!==false) {
				$this->success("置顶成功！");
			} else {
				$this->error("置顶失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["untop"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->ads_model->where(array('id'=>array('in',$ids)))->save(array('istop'=>0))!==false) {
				$this->success("取消置顶成功！");
			} else {
				$this->error("取消置顶失败！");
			}
		}
	}
	
	// 文章推荐
	public function recommend(){
		if(isset($_POST['ids']) && $_GET["recommend"]){
			$ids = I('post.ids/a');
			
			if ( $this->ads_model->where(array('id'=>array('in',$ids)))->save(array('recommended'=>1))!==false) {
				$this->success("推荐成功！");
			} else {
				$this->error("推荐失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["unrecommend"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->ads_model->where(array('id'=>array('in',$ids)))->save(array('recommended'=>0))!==false) {
				$this->success("取消推荐成功！");
			} else {
				$this->error("取消推荐失败！");
			}
		}
	}
	
	// 删除的文章
	public function delete(){
		if(isset($_POST['ids'])){
			$ids = I('post.ids/a');
			$ids = array_map('intval', $ids);
			$status=$this->ads_model->where(array("id"=>array('in',$ids)))->delete();
			
			if ($status!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			if(isset($_GET['id'])){
				$id = I("get.id",0,'intval');
				$status=$this->ads_model->where(array("id"=>$id))->delete();
				
				if ($status!==false) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
			}
		}
	}
	
}