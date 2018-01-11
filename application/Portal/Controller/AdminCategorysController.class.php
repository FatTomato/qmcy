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

class AdminCategorysController extends AdminbaseController {
	
	protected $categorys_model;
	protected $types=array("0"=>"信息","1"=>"圈子","2"=>"店铺","3"=>"公告");
	
	function _initialize() {
		parent::_initialize();
		$this->categorys_model = D("Portal/Categorys");
		$this->assign("types",$this->types);
	}
	
	// 后台文章分类列表
    public function index(){
    	$param_type = I("request.type",0,'intval');
    	$type = strlen($param_type) !== 0? $param_type: 0;
		$result = $this->categorys_model->where(array('type'=>$type))->order(array("listorder"=>"asc"))->select();
		
		$tree = new \Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		foreach ($result as $r) {
			$r['str_manage'] = '<a href="' . U("AdminCategorys/add", array("parent" => $r['cg_id'], "type" => $r['type'])) . '">添加子类</a> | <a href="' . U("AdminCategorys/edit", array("id" => $r['cg_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminCategorys/delete", array("id" => $r['cg_id'], "type" => $r['type'])) . '">删除</a> ';
			$r['types'] = $this->types[$r['type']];
			$r['id']=$r['cg_id'];
			$r['parentid']=$r['parent'];
			$r['icon'] = sp_get_image_preview_url($r['icon']);
			$array[] = $r;
		}
		
		$tree->init($array);
		$str = "<tr>
					<td><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input input-order'></td>
					<td>\$id</td>
					<td>\$spacer \$name</a></td>
	    			<td>\$types</td>
	    			<td><img src='\$icon' width='50px'/></td>
					<td>\$str_manage</td>
				</tr>";
		$taxonomys = $tree->get_tree(0, $str);
		$this->assign("type", $type);
		$this->assign("taxonomys", $taxonomys);
		$this->display();
	}
	
	// 文章分类添加
	public function add(){
	 	$parentid = I("get.parent",0,'intval');
	 	$type = I("get.type",0,'intval');
	 	$tree = new \Tree();
	 	$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
	 	$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
	 	$categorys = $this->categorys_model->order(array("path"=>"asc"))->select();
	 	
	 	$new_categorys=array();
	 	foreach ($categorys as $r) {
	 		$r['id']=$r['cg_id'];
	 		$r['parentid']=$r['parent'];
	 		$r['selected']= (!empty($parentid) && $r['cg_id']==$parentid)? "selected":"";
	 		$new_categorys[] = $r;
	 	}
	 	$tree->init($new_categorys);
	 	$tree_tpl="<option value='\$id' \$selected>\$spacer\$name</option>";
	 	$tree=$tree->get_tree(0,$tree_tpl);
	 	
	 	$this->assign("categorys_tree",$tree);
	 	$this->assign("parent",$parentid);
	 	$this->assign("type",$type);
	 	$this->display();
	}
	
	// 文章分类添加提交
	public function add_post(){
		if (IS_POST) {
			$_POST['icon'] = sp_asset_relative_url($_POST['icon']);
			if ($this->categorys_model->create()!==false) {
				if ($this->categorys_model->add()!==false) {
				    F('all_categorys',null);
					$this->success("添加成功！",U("AdminCategorys/index?type=".$_POST['type'].""));
				} else {
					$this->error("添加失败！");
				}
			} else {
				$this->error($this->categorys_model->getError());
			}
		}
	}
	
	// 文章分类编辑
	public function edit(){
		$id = I("get.id",0,'intval');
		$data=$this->categorys_model->where(array("cg_id" => $id))->find();
		$tree = new \Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$categorys = $this->categorys_model->where(array("cg_id" => array("NEQ",$id), "path"=>array("notlike","%-$id-%")))->order(array("path"=>"asc"))->select();
		
		$new_categorys=array();
		foreach ($categorys as $r) {
			$r['id']=$r['cg_id'];
			$r['parentid']=$r['parent'];
			$r['selected']=$data['parent']==$r['cg_id']?"selected":"";
			$new_categorys[] = $r;
		}
		
		$tree->init($new_categorys);
		$tree_tpl="<option value='\$id' \$selected>\$spacer\$name</option>";
		$tree=$tree->get_tree(0,$tree_tpl);
		
		$this->assign("categorys_tree",$tree);
		$this->assign("data",$data);
		$this->display();
	}
	
	// 文章分类编辑提交
	public function edit_post(){
		if (IS_POST) {
			$_POST['icon'] = sp_asset_relative_url($_POST['icon']);
			if ($this->categorys_model->create()!==false) {
				if ($this->categorys_model->save()!==false) {
				    F('all_categorys',null);
					$this->success("修改成功！",U("AdminCategorys/index?type=".$_POST['type'].""));
				} else {
					$this->error("修改失败！");
				}
			} else {
				$this->error($this->categorys_model->getError());
			}
		}
	}
	
	// 文章分类排序
	public function listorders() {
		$type = I("request.type",0,'intval');
		$status = parent::_listorders($this->categorys_model);
		if ($status) {
			$this->success("排序更新成功！",U("AdminCategorys/index?type=".$type.""));
		} else {
			$this->error("排序更新失败！",U("AdminCategorys/index?type=".$type.""));
		}
	}
	
	// 删除文章分类
	public function delete() {
		$id = I("get.id",0,'intval');
		$type = I("get.type",0,'intval');
		$count = $this->categorys_model->where(array("parent" => $id))->count();
		
		if ($count > 0) {
			$this->error("该菜单下还有子类，无法删除！");
		}
		
		if ($this->categorys_model->delete($id)!==false) {
			$this->success("删除成功！",U("AdminCategorys/index?type=".$type.""));
		} else {
			$this->error("删除失败！",U("AdminCategorys/index?type=".$type.""));
		}
	}
	
}