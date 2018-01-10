<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;

class AdController extends BaseController {
	/*
	广告位暂定如下几种：
		1、首页顶部banner										recomended=1		70+60+50+40+30
		2、各分类页顶部banner									cg_id&istop 		30+30
		3、首页广告列表入口对应的广告列表（所有的广告都展示）			null			10

	上述3中均对应统一的广告详情页
	*/
	protected $ad_m;
	
	public function _initialize() {
		parent::_initialize();
		$this->ad_m = M('Ads');
	}

	// 广告详情
	public function getAdInfo(){
		$id = (int)I('request.id');
		if (isset($id) && !empty($id)) {
			$where['id'] = $id;
		}else{
			$this->jerror("参数缺失");
		}

		$field = 'id,shop_id,post_content,post_title,post_excerpt,post_discount,start_time,end_time,post_expire,smeta,store_name,store_addr,store_contact,store_phone,store_time,post_hits,store_lat,store_lng,altas';

		$ad = $this->ad_m->field($field)->where($where)->find();

		if ($ad['smeta']) {
			$ad['smeta'] = sp_get_image_preview_url($ad['smeta']);
		}
		if ($ad['altas']) {
			$ad['altas'] = json_decode($ad['altas'],true);
			foreach ($ad['altas'] as $key => &$value) {
				$value = sp_get_image_preview_url($value);
			}
		}
		$shop = M('Shop')->field('shop_name,shop_logo')->where(array('id'=>$ad['shop_id']))->find();
		$ad['shop_name'] = $shop['shop_name'];
		$ad['shop_logo'] = $shop['shop_logo'];
		$ad['start_time'] = substr($ad['start_time'], 0, 10);
		$ad['end_time'] = substr($ad['end_time'], 0, 10);
		$post_hits_arr = explode(',',$ad['post_hits']);
		$ad['post_hits_num'] = strlen($ad['post_hits'])>0? count($post_hits_arr): 0;
		if (strlen($ad['post_hits'])>0) {
			foreach ($post_hits_arr as $key => $value) {
				$ad['hits_list'][$key]['member_id'] = $value;
				$ad['hits_list'][$key]['username'] = M('member')->where(array('member_id'=>$value))->getField('username');
				$ad['hits_list'][$key]['userphoto'] = M('member')->where(array('member_id'=>$value))->getField('userphoto');
			}
		}

		if($ad !== false){
			$jret['flag'] = 1;
			$jret['result'] = $ad;
	        $this->ajaxReturn($jret);
	    }else {
			$this->jerror("查询失败");
		}
	}
	
	// 广告列表
	public function getAdsList(){
		$cg_id = (int)I('request.cg_id');
		$istop = (int)I('request.istop');
		$recommended = (int)I('request.recommended');
		$lastid = (int)I('request.lastid');
		$epage = (int)I('request.epage');
		// 各分类
		if (isset($cg_id) && !empty($cg_id) && isset($istop) && !empty($istop)) {
			$son_cg = M('Categorys')->where(array('parent'=>$cg_id))->getField('cg_id', true);
			if (isset($son_cg)) {
				$where['cg_id'] = array('in', implode(',', array_merge(array($cg_id),$son_cg)));
			}else{
				$where['cg_id'] = $cg_id;
			}
			
			$where['istop'] = $istop;
		}
		// 首页
		if (isset($recommended) && !empty($recommended)) {
			$where['recommended'] = $recommended;
		}
		// 所有的都需要审核通过
		$where['post_status'] = 1;

		// 排序规则：活动状态>排序数值>结束时间
		$order = 'post_expire desc,listorder desc,end_time';

		// todo：活动数量多了需要有偏移量，对应参数也需调整
		if (isset($lastid) && isset($epage)) {
			if($lastid != 0){
				$where['id'] = array('GT',$lastid);
			}
			$limit = $epage;
		}

		$field = '*';

		$list = $this->ad_m->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as &$value) {
			if ($value['smeta']) {
				$value['smeta'] = sp_get_image_preview_url($value['smeta']);
			}
			if ($value['altas']) {
				$value['altas'] = json_decode($value['altas'],true);
				foreach ($value['altas'] as $key => &$v) {
					$v = sp_get_image_preview_url($v);
				}
			}
			$value['start_time'] = substr($value['start_time'], 0, 10);
			$value['end_time'] = substr($value['end_time'], 0, 10);
			$shop = M('Shop')->field('shop_name,shop_logo')->where(array('id'=>$value['shop_id']))->find();
			$value['shop_name'] = $shop['shop_name'];
			$value['shop_logo'] = $shop['shop_logo'];
		}

		if ($list !== false) {
			$jret['flag'] = 1;
			$jret['result'] = $list;
	        $this->ajaxReturn($jret);
		}else {
			$this->jerror("查询失败");
		}
		
	}

	// 官方说明
	public function getIll(){
		$name = I('request.name');
		if (empty($name)) {
			$this->jerror('参数缺失');
		}

		$content = M('Illustrate')->field('content')->where(array('name'=>$name))->find();
		if ($content) {
			$jret['flag'] = 1;
			$jret['result'] = $content;
			$this->ajaxReturn($jret);
		} else {
			$this->jerror('请求失败');
		}
	}
}