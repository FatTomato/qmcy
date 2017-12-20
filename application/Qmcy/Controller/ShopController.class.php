<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;

class ShopController extends BaseController {

	protected $shop_m;
	
	public function _initialize() {
		parent::_initialize();
		$this->shop_m = M('Shop');
		$this->shop_star_m = M('ShopStar');
	}

	// shop detail
	public function getShopDetail(){
		$id = I('request.id');
		if (isset($id) && !empty($id)) {
			$where['id'] = $id;
		}else{
			$this->jerror("参数缺失");
		}
		
		// $field = 'id,shop_name,shop_addr,shop_major,shop_time,shop_phone,shop_detail,is_shiti,is_new,shop_pic,shop_contact,shop_phone,lat,lng';

		$shop = $this->shop_m
				// ->field($field)
				->where($where)->find();

		$shop['shop_logo'] = sp_get_image_preview_url($shop['shop_logo']);

		if($shop !== false){
			$jret['flag'] = 1;
			$jret['result'] = $shop;
	        $this->ajaxreturn($jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// shop list
	public function getShopList(){
		$cg_id = I('request.cg_id');
		// $pagination = I('request.pagination');
		$lastid = (int)I('request.lastid');
		$epage = (int)I('request.epage');

		if (isset($cg_id) && !empty($cg_id)) {
			$where['b.cg_id'] = $cg_id;
		}

		$where['a.status'] = 1;

		$order = 'a.istop desc,b.listorder desc,a.add_time asc';

		if (isset($lastid) && isset($epage)) {
			if($lastid != 0){
				$where['a.id'] = array('GT',$lastid);
			}
			$limit = $epage;
		}

		$join = '__SHOP_RELATIONSHIPS__ b ON a.id = b.shop_id';

		$field = 'a.*';

		$list = $this->shop_m->alias('a')->join($join)->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as &$value) {
			$value['shop_logo'] = sp_get_image_preview_url($value['shop_logo']);
		}

		if ($list !== false) {
			$jret['flag'] = 1;
			$jret['result'] = $list;
	        $this->ajaxreturn($jret);
		}else {
			$this->jerror("查询失败");
		}
	}

	// shop search
	public function searchShopList(){
		$kword = I('request.kword');
		// $pagination = I('request.pagination');
		$lastid = (int)I('request.lastid');
		$epage = (int)I('request.epage');

		if (isset($kword) && !empty($kword)) {
			$map['shop_name']  = array('like', '%'.$kword.'%');
			$map['shop_major']  = array('like','%'.$kword.'%');
			$map['_logic'] = 'or';
			$where['_complex'] = $map;
		}else{
			$this->jerror('kword can`t be null!');
		}

		$where['a.status'] = 1;

		$order = 'a.istop desc,b.listorder desc,a.add_time asc';

		if (isset($lastid) && isset($epage)) {
			if($lastid != 0){
				$where['a.id'] = array('GT',$lastid);
			}
			$limit = $epage;
		}

		$join = '__SHOP_RELATIONSHIPS__ b ON a.id = b.shop_id';

		$field = 'a.id,a.shop_name,a.shop_addr,a.shop_major,a.shop_time,a.is_shiti,a.is_new,a.shop_pic,a.lng,a.lat';

		$list = $this->shop_m->alias('a')->join($join)->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as &$value) {
			$value['shop_logo'] = sp_get_image_preview_url($value['shop_logo']);
		}

		if ($list !== false) {
			$jret['flag'] = 1;
			$jret['result'] = $list;
	        $this->ajaxreturn($jret);
		}else {
			$this->jerror("查询失败");
		}
	}

	public function addShop(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$shop_name = I('request.shop_name');
		$shop_logo = I('request.shop_logo');
		$shop_major = I('request.shop_major');
		$is_shiti = I('request.is_shiti');
		$is_brand = I('request.is_brand');
		$shop_pic = I('request.shop_pic');
		$is_new = I('request.is_new');
		$shop_addr = I('request.shop_addr');
		// $shop_time = I('request.shop_time');
		$shop_phone = I('request.shop_phone');
		$shop_property = I('request.shop_property');
		$shop_detail = I('request.shop_detail');
		$cg_id = I('request.cg_id');
		$start_time = I('request.start_time');
		$end_time = I('request.end_time');
		if (empty($shop_name) || empty($shop_logo) || empty($shop_major) || empty($is_shiti) || empty($is_brand) || empty($shop_pic) || empty($is_new) || empty($shop_addr) || empty($shop_phone) || empty($shop_property) || empty($shop_detail) || empty($start_time) || empty($end_time)) {
			$this->jerror('参数缺失');
		}

		$shop['member_id'] = $this->user_result['member_id'];
		$shop['add_time'] = date('Y-m-d H:i:s');
		$shop['shop_name'] = $shop_name;
		$shop['shop_logo'] = $shop_logo;
		$shop['shop_major'] = $shop_major;
		$shop['is_shiti'] = $is_shiti;
		$shop['is_brand'] = $is_brand;
		$shop['shop_pic'] = $shop_pic;
		$shop['is_new'] = $is_new;
		$shop['shop_addr'] = $shop_addr;
		$shop['shop_time'] = $shop_time;

		$result = $this->info_m->add($shop);

		if ($result) {
			$jret['flag'] = 1;
	    	$this->ajaxreturn($jret);
		}else{
			$this->jerror('更新店铺失败！');
		}
	}

	public function setRelationship(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$action = I('request.action');
		$shopid = (int)I('request.shopid');

		if (empty($shopid)) {
			$this->jerror('参数缺失');
		}

		$id = $this->shop_star_m->where(array('shop_id'=>$shop_id, 'member_id'=>$this->user_result['member_id']))->getField('id');
		
		if ($action == 'false') {
			// 取消喜欢
			if ($id) {
				$this->shop_star_m->where(array('shop_id'=>$shop_id, 'member_id'=>$this->user_result['member_id']))->delete();
			}else {
				$re = $this->jerror('您未设置喜欢该店铺，不可取消设置！');
			}
		}elseif ($action == 'true') {
			// 设置喜欢
			if ($id) {
				$re = $this->jerror('您已设置喜欢该店铺，不可重复设置！');
			}else {
				$this->shop_star_m->add();
			}
		}

		if($re){
			$jret['flag'] = 1;
	        $this->ajaxreturn($jret);
	    }else {
	    	$msg = $action == 'false'? '店铺取消喜欢失败': '店铺设置喜欢失败';
			$this->jerror($msg);
		}
	}

	// todo 押金
}