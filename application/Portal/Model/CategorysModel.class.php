<?php
namespace Portal\Model;

use Common\Model\CommonModel;

class CategorysModel extends CommonModel {
	
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
		array('name', 'require', '分类名称不能为空！', 1, 'regex', 3),
	);
	
	protected function _after_insert($data,$options){
		parent::_after_insert($data,$options);
		$cg_id=$data['cg_id'];
		$parent_id=$data['parent'];
		if($parent_id==0){
			$d['path']="0-$cg_id";
		}else{
			$parent=$this->where("cg_id=$parent_id")->find();
			$d['path']=$parent['path'].'-'.$cg_id;
		}
		$this->where("cg_id=$cg_id")->save($d);
	}
	
	protected function _after_update($data,$options){
		parent::_after_update($data,$options);
		if(isset($data['parent'])){
			$cg_id=$data['cg_id'];
			$parent_id=$data['parent'];
			if($parent_id==0){
				$d['path']="0-$cg_id";
			}else{
				$parent=$this->where("cg_id=$parent_id")->find();
				$d['path']=$parent['path'].'-'.$cg_id;
			}
			$result=$this->where("cg_id=$cg_id")->save($d);
			if($result){
				$children=$this->where(array("parent"=>$cg_id))->select();
				foreach ($children as $child){
					$this->where(array("cg_id"=>$child['cg_id']))->save(array("parent"=>$cg_id,"cg_id"=>$child['cg_id']));
				}
			}
		}
		
	}
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
	}
	

}