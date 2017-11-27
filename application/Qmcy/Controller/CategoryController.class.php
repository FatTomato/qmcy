<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;

class CategoryController extends BaseController {

	protected $cg_m;
	
	public function _initialize() {
		parent::_initialize();
		$this->cg_m = M('Categorys');
	}

	//圈子详情 
	public function getCicleInfo(){

		$cg_id = I("request.cg_id");
		if (isset($cg_id) && !empty($cg_id)) {
			$where['cg_id'] = $cg_id;
		}else{
			$this->jerror("参数缺失");
		}

		$field = 'cg_id,name,icon,description';

		$cicle_info = $this->cg_m->where($where)->field($field)->find();

		$cicle_info['info_num'] = M('InfosRelationships')->where(array('cg_id'=>$cg_id))->count();
		$cicle_info['follow_num'] = M('CiclesRelationships')->where(array('cg_id'=>$cg_id))->count();
		
		if(!empty($this->user_result['member_id'])){
			$status = M('CiclesRelationships')->where(array('cg_id'=>$cg_id,'member_id'=>$this->user_result['member_id']))->find();
			$cicle_info['status'] = isset($status)? true: false;
		}

		$cicle_info['icon'] = sp_get_image_preview_url($cicle_info['icon']);

		if ($cicle_info !== false) {
			$jret['flag'] = 1;
			$jret['result'] = $cicle_info;
	        $this->ajaxreturn($jret);
		}else {
			$this->jerror("查询失败");
		}
	}

	//分类列表 
	public function getCgList(){
		$type = I("request.type");
		$parent_id = I("request.parent_id");

		$where['type'] = (int)$type;
		$where['parent'] = (int)$parent_id;

		$order = 'listorder desc';
		$field = 'cg_id,name,icon,description';
		$limit = '10';

		$cg_list = $this->cg_m->where($where)->field($field)->order($order)->limit($limit)->select();

		if ($type == 1) {
			foreach ($cg_list as &$value) {
				$value['info_num'] = M('InfosRelationships')->where(array('cg_id'=>$value['cg_id']))->count();
				$value['follow_num'] = M('CiclesRelationships')->where(array('cg_id'=>$value['cg_id']))->count();
				if (!empty($this->user_result['member_id'])) {
					$status = M('CiclesRelationships')->where(array('cg_id'=>$value['cg_id'],'member_id'=>$this->user_result['member_id']))->find();
					$value['status'] = isset($status)? true: false;
				}
				
				$value['icon'] = sp_get_image_preview_url($value['icon']);
				$son_num = $this->cg_m->where(array('parent'=>$value['cg_id']))->count();
				$value['is_parent'] = $son_num>0? 1: 0;
			}
		}elseif ($type == 0) {
			foreach ($cg_list as &$value) {
				$value['icon'] = sp_get_image_preview_url($value['icon']);
				$son_num = $this->cg_m->where(array('parent'=>$value['cg_id']))->count();
				$value['is_parent'] = $son_num>0? 1: 0;
			}
		}

		if ($cg_list !== false) {
			$jret['flag'] = 1;
			$jret['result'] = $cg_list;
	        $this->ajaxreturn($jret);
		}else {
			$this->jerror("查询失败");
		}
	}
}