<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;

class ShopController extends BaseController {

	protected $shop_m;
	
	public function _initialize() {
		parent::_initialize();
		$this->shop_m = M('Shop');
	}

	// shop detail
	public function getShopDetail(){
		$id = I('request.id');
		if (isset($id) && !empty($id)) {
			$where['id'] = $id;
		}else{
			$this->jerror("参数缺失");
		}
		
		$field = 'id,shop_name,shop_addr,shop_major,shop_time,shop_phone,shop_detail,is_shiti,is_new,shop_pic,shop_contact,shop_phone,lat,lng';

		$shop = $this->shop_m->field($field)->where($where)->find();

		$shop['shop_pic'] = sp_get_image_preview_url($shop['shop_pic']);

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
		$pagination = I('request.pagination');

		if (isset($cg_id) && !empty($cg_id)) {
			$where['b.cg_id'] = $cg_id;
		}

		$where['a.status'] = 1;

		$order = 'a.istop desc,b.listorder desc,a.add_time asc';

		if (isset($pagination) && !empty($pagination)) {
			$where['a.id'] = array('GT',(int)$pagination['id']);
			$limit = (int)$pagination['epage'];
		}

		$join = '__SHOP_RELATIONSHIPS__ b ON a.id = b.shop_id';

		$field = 'a.id,a.shop_name,a.shop_addr,a.shop_major,a.shop_time,a.is_shiti,a.is_new,a.shop_pic,a.lng,a.lat';

		$list = $this->shop_m->alias('a')->join($join)->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as &$value) {
			$value['shop_pic'] = sp_get_image_preview_url($value['shop_pic']);
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
		$pagination = I('request.pagination');

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

		if (isset($pagination) && !empty($pagination)) {
			$where['a.id'] = array('GT',(int)$pagination['id']);
			$limit = (int)$pagination['epage'];
		}else{
			$limit = 10;
		}

		$join = '__SHOP_RELATIONSHIPS__ b ON a.id = b.shop_id';

		$field = 'a.id,a.shop_name,a.shop_addr,a.shop_major,a.shop_time,a.is_shiti,a.is_new,a.shop_pic,a.lng,a.lat';

		$list = $this->shop_m->alias('a')->join($join)->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as &$value) {
			$value['shop_pic'] = sp_get_image_preview_url($value['shop_pic']);
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