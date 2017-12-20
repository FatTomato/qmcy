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

	// 店铺详情
	public function getShopDetail(){
		$id = I('request.id');
		if (isset($id) && !empty($id)) {
			$where['id'] = $id;
		}else{
			$this->jerror("参数缺失");
		}
		
		$field = '*';

		$shop = $this->shop_m->field($field)->where($where)->find();

		if ($this->user_result['member_id'] == $shop['member_id']) {
			$shop['is_owner'] = true;
		}
		
		if($shop['is_new']==1 && $shop['check']==0){unset($shop['is_new']);}
		if($shop['is_brand']==1 && $shop['check']==0){unset($shop['is_brand']);}
		$shop['shop_logo'] = sp_get_image_preview_url($shop['shop_logo']);
		$shop['shop_pic'] = explode(',', $shop['shop_pic']);
		foreach ($shop['shop_pic'] as &$value) {
			$value = sp_get_image_preview_url($value);
		}

		if($shop !== false){
			$jret['flag'] = 1;
			$jret['result'] = $shop;
	        $this->ajaxreturn($jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 店铺列表
	public function getShopList(){
		$cg_id = I('request.cg_id');
		$lastid = (int)I('request.lastid');
		$epage = (int)I('request.epage');

		if (isset($cg_id) && !empty($cg_id)) {
			$where['cg_id'] = $cg_id;
		}

		$where['status'] = 1;

		$order = 'istop desc,listorder desc,add_time desc';

		if (isset($lastid) && isset($epage)) {
			if($lastid != 0){
				$where['id'] = array('GT',$lastid);
			}
			$limit = $epage;
		}

		$join = '__SHOP_RELATIONSHIPS__ b ON a.id = b.shop_id';

		$field = '*';

		$list = $this->shop_m->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as &$value) {
			if($value['is_new']==1 && $value['check']==0){unset($value['is_new']);}
			if($value['is_brand']==1 && $value['check']==0){unset($value['is_brand']);}
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

	// 店铺搜索
	public function searchShopList(){
		$kword = I('request.kword');
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

		$where['status'] = 1;

		$order = 'istop desc,listorder desc,add_time desc';

		if (isset($lastid) && isset($epage)) {
			if($lastid != 0){
				$where['id'] = array('GT',$lastid);
			}
			$limit = $epage;
		}

		$field = '*';

		$list = $this->shop_m->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as &$value) {
			if($value['is_new']==1 && $value['check']==0){unset($value['is_new']);}
			if($value['is_brand']==1 && $value['check']==0){unset($value['is_brand']);}
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

	// 编辑店铺
	public function editShop(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		
		$post_data = [
			'shop_name'=>'店铺名称',
			'shop_logo'=>'店铺logo',
			'shop_major'=>'主营业务',
			'is_brand'=>'是否连锁店铺',
			'shop_pic'=>'商品图片',
			'is_new'=>'是否新店',
			'shop_addr'=>'店铺位置',
			'shop_phone'=>'店铺电话',
			'shop_property'=>'店铺性质',
			'shop_detail'=>'店铺详情',
			'cg_id'=>'店铺分类',
			'start_time'=>'开店时间',
			'end_time'=>'闭店时间',
		];
		$shop = [];
		foreach ($post_data as $key => $value) {
			$shop[$key] = I('request.'.$key);
			if (empty($shop[$key])) {
				$this->jerror($value.'缺失');
			}
		}

		$shop['member_id'] = $this->user_result['member_id'];
		$shop['add_time'] = date('Y-m-d H:i:s');
		$shop['is_new'] = $shop['is_new'] == 'true'? 1: 0;
		$shop['is_brand'] = $shop['is_brand'] == 'true'? 1: 0;
		$shop['shop_property'] = $shop['shop_property'] == 'true'? 1: 0;
		
		$shop_id = $this->shop_m->where(array('member_id'=>$this->user_result['member_id']))->getField('id');
		if ($shop_id) {
			$result = $this->shop_m->where(array('member_id'=>$this->user_result['member_id']))->save($shop);
		}else{
			$result = $this->shop_m->add($shop);
		}

		if ($result) {
			$jret['flag'] = 1;
	    	$this->ajaxreturn($jret);
		}else{
			$this->jerror('更新店铺失败！');
		}
	}

	// 体验豪华版
	public function setLevel(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$id = I('request.id');
		if (isset($id) && !empty($id)) {
			$where['id'] = $id;
		}else{
			$this->jerror("参数缺失");
		}
		$member_id = $this->shop_m->where($where)->getField('member_id');
		if ($this->user_result['member_id'] !== $member_id) {
			$this->jerror('只可以操作自己的店铺！');
		}
		$data = [];
		$data['level'] = 1;
		$data['vip_time'] = date('Y-m-d H:i:s',time()+7*86400);
		$re = $this->shop_m->where($where)->save($data);

		if($re !== false){
			$jret['flag'] = 1;
	        $this->ajaxreturn($jret);
	    }else {
			$this->jerror("体验失败");
		}
	}

	// 设置喜欢/不喜欢店铺
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
				$re = $this->shop_star_m->where(array('shop_id'=>$shop_id, 'member_id'=>$this->user_result['member_id']))->delete();
			}else {
				$this->jerror('您未设置喜欢该店铺，不可取消设置！');
			}
		}elseif ($action == 'true') {
			// 设置喜欢
			if ($id) {
				$this->jerror('您已设置喜欢该店铺，不可重复设置！');
			}else {
				$re = $this->shop_star_m->add();
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