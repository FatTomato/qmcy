<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;

class ShopController extends BaseController {

	protected $shop_m;
	
	public function _initialize() {
		parent::_initialize();
		$this->shop_m = M('Shop');
		$this->shop_star_m = M('ShopStar');
		$this->recruit_m = M('ShopRecruit');
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
			$shop['is_get_vip'] = $shop['vip_time'] == '1000-01-01 00:00:00'? false: true;
			$shop['is_new'] = (bool)$shop['is_new'];
			$shop['is_brand'] = (bool)$shop['is_brand'];
		}else{
			if($shop['is_new']==1 && $shop['check']==0){unset($shop['is_new']);}
			if($shop['is_brand']==1 && $shop['check']==0){unset($shop['is_brand']);}
		}
		if ($this->user_result['member_id']) {
			$res = $this->shop_star_m->where(array('member_id'=>$this->user_result['member_id'], 'shop_id'=>$id))->getField('id');
			$shop['is_star'] = $res? true: false;
		}
		if($shop['is_sale'] == 1){
			$order = 'a.post_expire desc,b.listorder desc,a.end_time';
			$join = '__ADS_RELATIONSHIPS__ b ON a.id = b.object_id';
			$field = 'a.post_title,a.post_discount,a.start_time,a.end_time,a.id,a.smeta,a.post_expire,a.store_lng,a.store_lat';
			$shop['ad_list'] = M('Ads')->alias('a')->join($join)->field($field)->where(array('shop_id'=>$id))->order($order)->select();
			foreach ($shop['ad_list'] as &$value) {
				$value['smeta'] = sp_get_image_preview_url($value['smeta']);
				$value['start_time'] = substr($value['start_time'], 0, 10);
				$value['end_time'] = substr($value['end_time'], 0, 10);
			}
		}
		$shop['is_sale'] = (bool)$shop['is_sale'];
		if($shop['is_recruit'] == 1){
			$shop['recruit_list'] = $this->recruit_m->where(array('shop_id'=>$id,'status'=>1))->order('id asc')->select();
		}
		$shop['is_recruit'] = (bool)$shop['is_recruit'];
		$shop['is_new'] = (bool)$shop['is_new'];
		$shop['recommended'] = (bool)$shop['recommended'];
		$shop['deposit'] = (bool)$shop['deposit'];
		$shop['is_brand'] = (bool)$shop['is_brand'];

		$shop['shop_logo'] = sp_get_image_preview_url($shop['shop_logo']);
		if(strlen($shop['shop_pic']) > 0){
			$shop['shop_pic'] = explode(',', $shop['shop_pic']);
			foreach ($shop['shop_pic'] as &$value) {
				$value = sp_get_image_preview_url($value);
			}
		}else{
			unset($shop['shop_pic']);
		}

		$shop['stars'] = $this->shop_star_m->where(array('shop_id'=>$shop['id']))->count();

		if($shop !== false){
			$jret['flag'] = 1;
			$this->shop_m->where(array('id'=>$id))->setInc('hits',1);
			$jret['result'] = $shop;
	        $this->ajaxreturn($jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 店铺列表
	public function getShopList(){
		$cg_id = (int)I('request.cg_id');
		$is_sale = I('request.is_sale');
		$is_recruit = I('request.is_recruit');
		$lastid = (int)I('request.lastid');
		$epage = (int)I('request.epage');

		if (isset($cg_id) && !empty($cg_id)) {
			$where['cg_id'] = $cg_id;
		}
		if ($is_sale == 'true') {
			$where['is_sale'] = 1;
		}
		if ($is_recruit == 'true') {
			$where['is_recruit'] = 1;
		}

		$where['status'] = 1;

		$order = 'deposit desc,istop desc,listorder desc,add_time desc';

		if (isset($lastid) && isset($epage)) {
			if($lastid != 0){
				$where['id'] = array('LT',$lastid);
			}
			$limit = $epage;
		}

		$join = '__SHOP_RELATIONSHIPS__ b ON a.id = b.shop_id';

		$field = '*';

		$list = $this->shop_m->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as &$value) {
			if($value['check']==1){
				$value['is_new'] = (bool)$value['is_new'];
				$value['is_brand'] = (bool)$value['is_brand'];
			}else{
				$value['is_new'] = false;
				$value['is_brand'] = false;
			}
			$value['is_recruit'] = (bool)$value['is_recruit'];
			if($value['is_recruit']){
				$value['recruit_num'] = $this->recruit_m->where(array('shop_id'=>$value['id'],'status'=>1))->count();
			}
			$value['is_sale'] = (bool)$value['is_sale'];
			if($shop['is_sale']){
				$order = 'a.post_expire desc,b.listorder desc,a.end_time';
				$join = '__ADS_RELATIONSHIPS__ b ON a.id = b.object_id';
				$field = 'a.post_title,a.post_discount,a.start_time,a.end_time,a.id,a.smeta,a.post_expire,a.store_lng,a.store_lat';
				$shop['ad_list'] = M('Ads')->alias('a')->join($join)->field($field)->where(array('shop_id'=>$id))->order($order)->limit('1')->select();
			}
			$value['deposit'] = (bool)$value['deposit'];
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

		$order = 'deposit desc,istop desc,listorder desc,add_time desc';

		if (isset($lastid) && isset($epage)) {
			if($lastid != 0){
				$where['id'] = array('LT',$lastid);
			}
			$limit = $epage;
		}

		$field = '*';

		$list = $this->shop_m->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as &$value) {
			if($value['check']==1){
				$value['is_new'] = (bool)$value['is_new'];
				$value['is_brand'] = (bool)$value['is_brand'];
			}else{
				$value['is_new'] = false;
				$value['is_brand'] = false;
			}
			$value['is_recruit'] = (bool)$value['is_recruit'];
			if($value['is_recruit']){
				$value['recruit_num'] = $this->recruit_m->where(array('shop_id'=>$value['id'],'status'=>1))->count();
			}
			$value['is_sale'] = (bool)$value['is_sale'];
			if($shop['is_sale']){
				$order = 'a.post_expire desc,b.listorder desc,a.end_time';
				$join = '__ADS_RELATIONSHIPS__ b ON a.id = b.object_id';
				$field = 'a.post_title,a.post_discount,a.start_time,a.end_time,a.id,a.smeta,a.post_expire,a.store_lng,a.store_lat';
				$shop['ad_list'] = M('Ads')->alias('a')->join($join)->field($field)->where(array('shop_id'=>$id))->order($order)->limit('1')->select();
			}
			$value['deposit'] = (bool)$value['deposit'];
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
				$this->jerror($value.'不能为空');
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
		$shop_id = (int)I('request.shop_id');

		if (empty($shop_id)) {
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
				$re = $this->shop_star_m->add(array('shop_id'=>$shop_id, 'member_id'=>$this->user_result['member_id']));
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

	// 添加单条招聘
	public function addRecruit(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$post_data = [
			'shop_id'=>'shop_id',
			'jd'=>'职位',
			'num'=>'人数',
			'salary'=>'薪资',
			'demand'=>'要求',
			'benefits'=>'福利',
		];
		$recruit = [];
		foreach ($post_data as $key => $value) {
			$recruit[$key] = I('request.'.$key);
			if (empty($recruit[$key])) {
				$this->jerror($value.'不能为空');
			}
		}

		$recruit_num = $this->recruit_m->where(array('shop_id'=>$recruit['shop_id']))->count();
		if ($recruit_num > 9) {
			$this->jerror('最多添加10条招聘信息');
		}

		$result = $this->recruit_m->add($recruit);
		$this->shop_m->where(array('id'=>$recruit['shop_id']))->save(['is_recruit'=>1]);

		if ($result) {
			$jret['flag'] = 1;
	    	$this->ajaxreturn($jret);
		}else{
			$this->jerror('添加招聘失败！');
		}
	}

	// 编辑单条招聘
	public function editRecruit(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$post_data = [
			'id'=>'recruit_id',
			'jd'=>'职位',
			'num'=>'人数',
			'salary'=>'薪资',
			'demand'=>'要求',
			'benefits'=>'福利',
		];
		$recruit = [];
		foreach ($post_data as $key => $value) {
			$recruit[$key] = I('request.'.$key);
			if (empty($recruit[$key])) {
				$this->jerror($value.'不能为空');
			}
		}

		$result = $this->recruit_m->save($recruit);

		if ($result !== false) {
			$jret['flag'] = 1;
	    	$this->ajaxreturn($jret);
		}else{
			$this->jerror('修改招聘失败！');
		}

	}

	// 删除单条招聘
	public function delRecruit(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$recruit_id = I('request.id');
		if (empty($recruit_id)) {
			$this->jerror('参数缺失');
		}
		$re = $this->recruit_m->where(array('id'=>$recruit_id))->delete();

		$recruit_num = $this->recruit_m->where(array('shop_id'=>$recruit['shop_id']))->count();
		if ($recruit_num == 0) {
			$this->shop_m->where(array('id'=>$recruit['shop_id']))->save(['is_recruit'=>0]);
		}

		if ($re) {
			$jret['flag'] = 1;
			$this->ajaxreturn($jret);
		}else{
			$this->jerror('删除失败');
		}
	}

	// todo 押金
}