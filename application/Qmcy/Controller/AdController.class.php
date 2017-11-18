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

		$field = 'id,shop_id,post_content,post_title,post_excerpt,post_discount,start_time,end_time,post_expire,smeta,store_name,store_addr,store_contact,store_phone,store_time,post_hits';

		$ad = $this->ad_m->field($field)->where($where)->find();

		$ad['smeta'] = sp_get_image_preview_url($ad['smeta']);
		$ad['start_time'] = substr($ad['start_time'], 0, 10);
		$ad['end_time'] = substr($ad['end_time'], 0, 10);
		$post_hits_arr = explode(',',$ad['post_hits']);
		$ad['post_hits_num'] = strlen($ad['post_hits'])>0? count($post_hits_arr): 0;
		if (strlen($ad['post_hits'])>0) {
			foreach ($post_hits_arr as $key => $value) {
				$ad['hits_list'][$key]['user_id'] = $value;
				$ad['hits_list'][$key]['username'] = M('member')->where(array('user_id'=>$value))->getField('username');
				$ad['hits_list'][$key]['userphoto'] = M('member')->where(array('user_id'=>$value))->getField('userphoto');
			}
		}

		if($ad !== false){
			$jret['flag'] = 1;
			$jret['result'] = $ad;
	        $this->ajaxreturn($jret);
	    }else {
			$this->jerror("查询失败");
		}
	}
	
	// 广告列表
	public function getAdsList(){
		$cg_id = (int)I('request.cg_id');
		$istop = (int)I('request.istop');
		$recommended = (int)I('request.recommended');
		$pagination = (array)I('request.pagination');
		// 各分类
		if (isset($cg_id) && !empty($cg_id) && isset($istop) && !empty($istop)) {
			$where['b.cg_id'] = $cg_id;
			$where['a.istop'] = $istop;
		}
		// 首页
		if (isset($recommended) && !empty($recommended)) {
			$where['a.recommended'] = $recommended;
		}
		// 所有的都需要审核通过
		$where['a.post_status'] = 1;

		// 排序规则：活动状态>排序数值>结束时间
		$order = 'a.post_expire desc,b.listorder desc,a.end_time';

		// todo：活动数量多了需要有偏移量，对应参数也需调整
		if (count($pagination) == 2) {
			$where['a.id'] = array('GT',(int)$pagination['id']);
			$limit = (int)$pagination['epage'];
		}

		$join = '__ADS_RELATIONSHIPS__ b ON a.id = b.object_id';

		$field = 'a.post_title,a.post_discount,a.start_time,a.end_time,a.id,a.smeta,a.post_expire';

		$list = $this->ad_m->alias('a')->join($join)->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as &$value) {
			$value['smeta'] = sp_get_image_preview_url($value['smeta']);
			$value['start_time'] = substr($value['start_time'], 0, 10);
			$value['end_time'] = substr($value['end_time'], 0, 10);
		}

		if ($list !== false) {
			$jret['flag'] = 1;
			$jret['result'] = $list;
	        $this->ajaxreturn($jret);
		}else {
			$this->jerror("查询失败");
		}
		
	}
}