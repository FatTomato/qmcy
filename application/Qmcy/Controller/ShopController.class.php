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
			$where['s.id'] = $id;
		}else{
			$this->jerror("参数缺失");
		}
		
		$field = 's.*';
		$join = '__MEMBER__ m ON m.member_id = s.member_id';
		$shop = $this->shop_m->alias('s')->join($join)->field($field)->where($where)->find();
		if ( $shop['status']!=1 ) {
			$this->jerror('该店铺已下架！');
		}

		$shop['is_new'] = (bool)$shop['is_new'];//新店
		$shop['is_brand'] = (bool)$shop['is_brand'];//连锁
		$shop['is_recruit'] = (bool)$shop['is_recruit'];//招聘
		$shop['deposit'] = $shop['deposit']==1?true:false;//保证金
		$shop['is_sale'] = (bool)$shop['is_sale'];//活动
		$shop['shop_property'] = (bool)$shop['shop_property'];//店铺性质
		$shop['probation'] = (bool)$shop['probation'];//是否体验豪华版

		$shop['cg_name'] = M('Categorys')->where(array('cg_id'=>$shop['cg_id']))->getField('name');

		if ($this->user_result['member_id'] == $shop['member_id']) {
			$shop['is_owner'] = true;
		}else{
			if($shop['is_new']==1 && $shop['check']==0){unset($shop['is_new']);}
			if($shop['is_brand']==1 && $shop['check']==0){unset($shop['is_brand']);}
		}
		if ($this->user_result['member_id']) {
			$res = $this->shop_star_m->where(array('shop_id'=>$id, 'member_id'=>$this->user_result['member_id']))->find();
			$shop['is_star'] = $res['status']? true: false;//收藏
			$shop['is_like'] = $res['thumbup']? true: false;//点赞
		}

		// 是否有活动列表
		if($shop['is_sale'] == 1){
			$order = 'post_expire desc,listorder desc,end_time';
			$field = '*';
			$shop['ad_list'] = M('Ads')->field($field)->where(array('shop_id'=>$id, 'post_status'=>1))->order($order)->select();
			foreach ($shop['ad_list'] as &$value) {
				if ($value['altas']) {
					$value['altas'] = json_decode($value['altas'],true);
					$value['smeta'] = sp_get_image_preview_url($value['altas'][0]);
				}
				$value['start_time'] = substr($value['start_time'], 0, 10);
				$value['end_time'] = substr($value['end_time'], 0, 10);
			}
		}

		// 是否续费
		$remainder_time = strtotime($shop['vip_time']) - time();
		if ( ($shop['level']==0 && $shop['probation']==1) || ($shop['level']==1 && $remainder_time<=604800) ) {
			$shop['is_remind'] = 1;
		}
		
		// 是否有招聘信息
		if($shop['is_recruit'] == 1){
			$shop['recruit_list'] = $this->recruit_m->where(array('shop_id'=>$id))->order('id asc')->select();
		}
		

		$shop['shop_logo_show'] = sp_get_image_preview_url($shop['shop_logo']);
		if(strlen($shop['shop_pic']) > 0){
			$shop['shop_pic'] = explode(',', $shop['shop_pic']);
			foreach ($shop['shop_pic'] as $v) {
				$shop['shop_pic_show'][] = sp_get_image_preview_url($v);
			}
		}else{
			unset($shop['shop_pic']);
		}

		if($shop !== false){
			$this->jret['flag'] = 1;
			$this->shop_m->where(array('id'=>$id))->setInc('hits',1);
			$this->jret['result'] = $shop;
	        $this->ajaxReturn($this->jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 店铺列表
	public function getShopList(){
		$cg_id = (int)I('request.cg_id');
		$is_sale = I('request.is_sale');
		$is_recruit = I('request.is_recruit');
		$is_deposit = I('request.is_deposit');
		$star = I('request.star');
		$lastid = (int)I('request.lastid');
		$epage = (int)I('request.epage');

		if (isset($cg_id) && !empty($cg_id)) {
			$where['s.cg_id'] = $cg_id;
		}
		if ($is_sale == 'true') {
			$where['s.is_sale'] = 1;
		}
		if ($is_recruit == 'true') {
			$where['s.is_recruit'] = 1;
		}
		if ($is_deposit == 'true') {
			$where['s.deposit'] = 1;
		}
		// 我的收藏
		if ($star == 'true') {
			if (empty($this->user_result['member_id'])) {
				$this->jerror('您还没有登录！');
			}
			$star_ids = $this->shop_star_m->where(array('member_id'=>$this->user_result['member_id'], 'status'=>1))->getField('shop_id', true);
			if ($star_ids) {
				$where['s.id'] = array('in', $star_ids);
			} else {
				$this->jret['flag'] = 1;
				$this->jret['result'] = [];
		        $this->ajaxReturn($this->jret);
			}
		}

		$where['s.status'] = 1;

		// $order = 'deal_time desc';
		$order = 's.id desc';

		if (isset($lastid) && isset($epage)) {
			if($lastid != 0){
				// $last_deal_time = $this->shop_m->where(array('id'=>$lastid))->getField('deal_time');
				// $where['s.deal_time'] = array('LT',$last_deal_time);
				$where['s.id'] = array('LT', $lastid);
			}
			$limit = $epage;
		}

		$field = 's.*';
		$join = '__MEMBER__ m ON m.member_id = s.member_id';
		$list = $this->shop_m->alias('s')->join($join)->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as &$value) {
			if($value['check']==1){
				$value['is_new'] = (bool)$value['is_new'];
				$value['is_brand'] = (bool)$value['is_brand'];
			}else{
				$value['is_new'] = false;
				$value['is_brand'] = false;
			}

			if($value['is_recruit']){
				$value['recruit_num'] = $this->recruit_m->where(array('shop_id'=>$value['id']))->count();
			}
			unset($value['is_recruit']);

			if($value['is_sale']){
				$order = 'post_expire desc,listorder desc,end_time';
				$field = '*';
				$value['ad_list'] = M('Ads')->field($field)->where(array('shop_id'=>$value['id'], 'post_status'=>1))->order($order)->limit('1')->select();
			}
			unset($value['is_sale']);

			$value['deposit'] = $value['deposit']==1?true:false;//保证金
			$value['shop_property'] = (bool)$value['shop_property'];
			$value['shop_logo'] = sp_get_image_preview_url($value['shop_logo']);
		}

		if ($list !== false) {
			$this->jret['flag'] = 1;
			$this->jret['result'] = $list;
	        $this->ajaxReturn($this->jret);
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
			$map['s.shop_name']  = array('like', '%'.$kword.'%');
			$map['s.shop_major']  = array('like','%'.$kword.'%');
			$map['_logic'] = 'or';
			$where['_complex'] = $map;
		}else{
			$this->jerror('kword can`t be null!');
		}

		$where['s.status'] = 1;

		$order = 'deal_time';

		if (isset($lastid) && isset($epage)) {
			if($lastid != 0){
				$last_deal_time = $this->shop_m->where(array('id'=>$lastid))->getField('deal_time');
				$where['s.deal_time'] = array('LT',$last_deal_time);
			}
			$limit = $epage;
		}

		$field = 's.*';
		$join = '__MEMBER__ m ON m.member_id = s.member_id';
		$list = $this->shop_m->alias('s')->join($join)->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as &$value) {
			if($value['check']==1){
				$value['is_new'] = (bool)$value['is_new'];
				$value['is_brand'] = (bool)$value['is_brand'];
			}else{
				$value['is_new'] = false;
				$value['is_brand'] = false;
			}
			if($value['is_recruit']){
				$value['recruit_num'] = $this->recruit_m->where(array('shop_id'=>$value['id']))->count();
			}
			unset($value['is_recruit']);
			if($value['is_sale']){
				$order = 'post_expire desc,listorder desc,end_time';
				$field = '*';
				$value['ad_list'] = M('Ads')->field($field)->where(array('shop_id'=>$id, 'post_status'=>1))->order($order)->limit('1')->select();
			}
			unset($value['is_sale']);
			$value['deposit'] = $value['deposit']==1?true:false;//保证金
			$value['shop_property'] = (bool)$value['shop_property'];
			$value['shop_logo'] = sp_get_image_preview_url($value['shop_logo']);
		}

		if ($list !== false) {
			$this->jret['flag'] = 1;
			$this->jret['result'] = $list;
	        $this->ajaxReturn($this->jret);
		}else {
			$this->jerror("查询失败");
		}
	}

	// 编辑店铺
	public function editShop(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
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
			'shop_addr_name'=>'shop_addr_name',
			'lat'=>'lat',
			'lng'=>'lng',
			'province'=>'province',
			'city'=>'city',
			'district'=>'district',
		];
		$shop = [];
		foreach ($post_data as $key => $value) {
			$shop[$key] = I('request.'.$key);
			if (empty($shop[$key])) {
				$this->jerror($value.'不能为空');
			}
		}
		// 字数限制
		if (mb_strlen($shop['shop_name'])>12) {
			$this->jerror('店铺名称不可超过12个字');
		}
		if (mb_strlen($shop['shop_major'])>24) {
			$this->jerror('主营业务不可超过24个字');
		}
		if (mb_strlen($shop['shop_detail'])>500) {
			$this->jerror('店铺详情不可超过500个字');
		}

		$shop['member_id'] = $this->user_result['member_id'];
		$shop['add_time'] = date('Y-m-d H:i:s');
		// $shop['deal_time'] = date('Y-m-d H:i:s');
		$shop['is_new'] = $shop['is_new'] == 'true'? 1: 0;
		$shop['is_brand'] = $shop['is_brand'] == 'true'? 1: 0;
		$shop['shop_property'] = $shop['shop_property'] == 'true'? 1: 0;
		
		$shop_id = $this->shop_m->where(array('member_id'=>$this->user_result['member_id']))->getField('id');
		if ($shop_id) {
			$result = $this->shop_m->where(array('member_id'=>$this->user_result['member_id']))->save($shop);
		}else{
			// 店铺名不可相同
			$shop_name = $this->shop_m->where(array('shop_name'=>$shop['shop_name']))->find();
			if ($shop_name!=false) {
				$this->jerror('店铺名称已存在！');
			}
			$result = $this->shop_m->add($shop);
		}

		if ($result) {
			$this->jret['flag'] = 1;
	    	$this->ajaxReturn($this->jret);
		}else{
			$this->jerror('更新店铺失败！');
		}
	}

	// 体验豪华版
	public function setLevel(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$id = I('request.id');
		if (isset($id) && !empty($id)) {
			$where['id'] = $id;
		}else{
			$this->jerror("参数缺失");
		}
		$member_id = $this->shop_m->where($where)->getField('member_id');
		if ($this->user_result['member_id'] != $member_id) {
			$this->jerror('只可以操作自己的店铺！');
		}
		$data = [];
		$data['level'] = 1;
		$data['vip_time'] = date('Y-m-d H:i:s',strtotime("+15 day"));
		$data['probation'] = 1;
		$re = $this->shop_m->where($where)->save($data);

		if($re !== false){
			$this->jret['flag'] = 1;
	        $this->ajaxReturn($this->jret);
	    }else {
			$this->jerror("体验失败");
		}
	}

	// 收藏/取消收藏店铺
	public function setRelationship(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$action = I('request.action');
		$shop_id = (int)I('request.shop_id');

		if (empty($shop_id) || empty($action)) {
			$this->jerror('参数缺失');
		}

		$star = $this->shop_star_m->where(array('shop_id'=>$shop_id, 'member_id'=>$this->user_result['member_id']))->find();
		
		if ($action == 'false') {
			// 取消收藏
			if (isset($star) && $star['status'] == 1) {
				$re = $this->shop_star_m->where(array('shop_id'=>$shop_id, 'member_id'=>$this->user_result['member_id']))->save(array('status'=>0));
			}
		}elseif ($action == 'true') {
			// 设置收藏
			if (isset($star) && $star['status'] == 0) {
				$re = $this->shop_star_m->where(array('shop_id'=>$shop_id, 'member_id'=>$this->user_result['member_id']))->save(array('status'=>1));
			}elseif (is_null($star)) {
				$re = $this->shop_star_m->add(array('shop_id'=>$shop_id, 'member_id'=>$this->user_result['member_id'], 'status'=>1));
			}
		}
		if($re !== false){
			$this->jret['flag'] = 1;
	        $this->ajaxReturn($this->jret);
	    }else {
	    	$msg = $action == 'false'? '店铺取消收藏失败': '店铺设置收藏失败';
			$this->jerror($msg);
		}
	}

	// 给店铺点赞
	public function thumbUp(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$shop_id = (int)I('request.shop_id');

		if (empty($shop_id)) {
			$this->jerror('参数缺失');
		}

		$star = $this->shop_star_m->where(array('shop_id'=>$shop_id, 'member_id'=>$this->user_result['member_id']))->find();

		if (isset($star) && $star['thumbup'] == 0) {
			$re = $this->shop_star_m->where(array('shop_id'=>$shop_id, 'member_id'=>$this->user_result['member_id']))->save(array('thumbup'=>1));
		}elseif (is_null($star)) {
			$re = $this->shop_star_m->add(array('shop_id'=>$shop_id, 'member_id'=>$this->user_result['member_id'], 'thumbup'=>1));
		}

		if($re){
			$this->jret['flag'] = 1;
			$this->shop_m->where(array('id'=>$shop_id))->setInc('star_num', 1);
	        $this->ajaxReturn($this->jret);
	    }else {
			$this->jerror('店铺设置喜欢失败');
		}
	}

	// 添加单条招聘
	public function addRecruit(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
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
		// 字数限制
		if ( mb_strlen($recruit['jd'])>20 ) {
			$this->jerror('招聘职位字数不可超过20字');
		}
		if ( (int)($recruit['num'])>20 ) {
			$this->jerror('招聘人数不可超过20人');
		}
		if ( mb_strlen($recruit['salary'])>20 ) {
			$this->jerror('薪资位字数不可超过20个字');
		}
		if ( mb_strlen($recruit['benefits'])>100 ) {
			$this->jerror('福利字数不可超过100个字');
		}
		if ( mb_strlen($recruit['demand'])>100 ) {
			$this->jerror('职位要求字数不可超过100个字');
		}

		$recruit_num = $this->recruit_m->where(array('shop_id'=>$recruit['shop_id']))->count();
		if ($recruit_num > 9) {
			$this->jerror('最多添加10条招聘信息');
		}

		$result = $this->recruit_m->add($recruit);
		$this->shop_m->where(array('id'=>$recruit['shop_id']))->save(['is_recruit'=>1]);

		if ($result) {
			$this->jret['flag'] = 1;
	    	$this->ajaxReturn($this->jret);
		}else{
			$this->jerror('添加招聘失败！');
		}
	}

	// 编辑单条招聘
	public function editRecruit(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
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
			$this->jret['flag'] = 1;
	    	$this->ajaxReturn($this->jret);
		}else{
			$this->jerror('修改招聘失败！');
		}

	}

	// 删除单条招聘
	public function delRecruit(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$recruit_id = I('request.id');
		if (empty($recruit_id)) {
			$this->jerror('参数缺失');
		} else {
			$where['id'] = $recruit_id;
		}
		$shop_id = $this->recruit_m->where($where)->getField('shop_id');
		$re = $this->recruit_m->where($where)->delete();

		$recruit_num = $this->recruit_m->where(array('shop_id'=>$shop_id))->count();
		if ($recruit_num == 0) {
			$this->shop_m->where(array('id'=>$shop_id))->save(['is_recruit'=>0]);
		}

		if ($re) {
			$this->jret['flag'] = 1;
			$this->ajaxReturn($this->jret);
		} else {
			$this->jerror('删除失败');
		}
	}

	// 是否有店铺
	public function getShopId(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$this->jret['flag'] = 1;
		$this->jret['result'] = $this->shop_m->where(array('member_id'=>$this->user_result['member_id']))->getField('id');
		$this->ajaxReturn($this->jret);
	}

	// 5 9 * * * curl "localhost:8837/jserv/hdiCheck" -o a
	public function upShopStatus(){
		// 统计过期条数
		$where['vip_time'] = array('ELT',date('Y-m-d H:i:s'));
		$where['level'] = 1;
		$ids = $this->shop_m->where($where)->getField('id', true);

		$save_data['level'] = 0;
		$save_data['vip_type'] = 0;
		$save_where['id'] = array('IN', $ids);
		$this->shop_m->where($save_where)->save($save_data);
	}
}