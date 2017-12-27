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
	protected $ads_relationships_model;
	protected $categroys_model;
	
	function _initialize() {
		parent::_initialize();
		$this->ads_model = D("Portal/Ads");
		$this->categroys_model = D("Portal/Categorys");
		$this->ads_relationships_model = D("Portal/AdsRelationships");
	}
	
	// 后台文章管理列表
	public function index(){
		$this->_lists();
		$this->_getTree();
		$this->display();
	}
	
	// 文章添加
	public function add(){

		$this->_getTermTree();

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
			$key = C('TXMAP');
			$addr = $article['store_addr'];
			$url = 'http://apis.map.qq.com/ws/geocoder/v1/?address='.$addr.'&key='.$key;
			$lat_lng = http_get($url);
			if($lat_lng['status'] !== 0){
				$this->error("位置解析失败！");
			}
			$article['store_lat'] = $lat_lng['result']['location']['lat'];
			$article['store_lng'] = $lat_lng['result']['location']['lng'];
			$article['post_status']=0;
			$article['shop_id'] = M('Shop')->where(array('shop_name'=>$article['store_name']))->getField('id');
			if (!$article['shop_id']) {
				$this->error("店铺不存在！");
			}
			$result=$this->ads_model->add($article);
			M('Shop')->where(array('shop_name'=>$article['store_name']))->save(array('is_sale'=>1));
			if ($result) {
				
				$re = $this->ads_relationships_model->add(array("cg_id"=>intval($_POST['cg_id']),"object_id"=>$result));
				
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
	
	// 文章编辑
	public function edit(){
		$id=  I("get.id",0,'intval');
		
		$ads_relationship = M('AdsRelationships')->where(array("object_id"=>$id,"status"=>1))->getField("cg_id",true);
		$this->_getTermTree($ads_relationship);
		$post=$this->ads_model->where("id=$id")->find();
		$this->assign("post",$post);
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
			$key = C('TXMAP');
			$addr = $article['store_addr'];
			$url = 'http://apis.map.qq.com/ws/geocoder/v1/?address='.$addr.'&key='.$key;
			$lat_lng = http_get($url);
			if($lat_lng['status'] !== 0){
				$this->error("位置解析失败！");
			}
			$article['store_lat'] = $lat_lng['result']['location']['lat'];
			$article['store_lng'] = $lat_lng['result']['location']['lng'];
			$result=$this->ads_model->save($article);
			if ($result!==false) {
				$re = $this->ads_relationships_model->where(array('object_id'=>$_POST['post']['id']))->save(array('cg_id'=>$_POST['cg_id']));
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
	
	// 文章排序
	public function listorders() {
		$status = parent::_listorders($this->ads_relationships_model);
		if ($status) {
			$this->success("排序更新成功！");
		} else {
			$this->error("排序更新失败！");
		}
	}
	
	/**
	 * 文章列表处理方法,根据不同条件显示不同的列表
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
			
		$this->ads_model
		->alias("a")
		->where($where);
		
		
		$this->ads_model->join("__ADS_RELATIONSHIPS__ b ON a.id = b.object_id");
		
		
		$count=$this->ads_model->count();
			
		$page = $this->page($count, 20);
			
		$this->ads_model
		->alias("a")
		->join("__USERS__ c ON a.post_author = c.id")
		->where($where)
		->limit($page->firstRow , $page->listRows)
		->order("a.post_date DESC");
		
	    $this->ads_model->field('a.*,c.user_login,c.user_nicename,b.listorder,b.adsid');
	    $this->ads_model->join("__ADS_RELATIONSHIPS__ b ON a.id = b.object_id");
		
		$posts=$this->ads_model->select();
		foreach($posts as &$v){
            $v['post_hits'] = !empty($v['post_hits'])? count(explode(',', $v['post_hits'])): 0;
            $v['post_like'] = !empty($v['post_like'])? count(explode(',', $v['post_like'])): 0;
        }

		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("posts",$posts);
	}
	
	// 获取文章分类树结构 select 形式
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
	
	// 获取文章分类树结构 
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
	
	// 清除已经删除的文章
	public function delete(){
		if(isset($_POST['ids'])){
			$ids = I('post.ids/a');
			$ids = array_map('intval', $ids);
			$status=$this->ads_model->where(array("id"=>array('in',$ids)))->delete();
			$this->ads_relationships_model->where(array('object_id'=>array('in',$ids)))->delete();
			
			if ($status!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			if(isset($_GET['id'])){
				$id = I("get.id",0,'intval');
				$status=$this->ads_model->where(array("id"=>$id))->delete();
				$this->ads_relationships_model->where(array('object_id'=>$id))->delete();
				
				if ($status!==false) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
			}
		}
	}
	
}