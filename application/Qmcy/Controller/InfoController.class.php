<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;

class InfoController extends BaseController {

	protected $info_m;
	
	public function _initialize() {
		parent::_initialize();
		$this->info_m = M('Infos');
	}

	// 信息详情
	public function getInfo(){
		$id = (int)I('request.id');
		if (isset($id) && !empty($id)) {
			$where['a.id'] = $id;
		}else{
			$this->jerror("参数缺失");
		}

		$join = '__MEMBER__ b ON a.post_author = b.member_id';

		$field = 'a.id,a.post_addr,a.post_date,a.post_content,a.smeta,a.post_like,a.stars,b.userphoto,b.username,b.member_id';

		$info = $this->info_m->alias('a')->join($join)->field($field)->where($where)->find();
		if(!empty($this->user_result['member_id'])){
			$post_like = explode(',', $info['post_like']);
			$stars = explode(',', $info['stars']);
			$info['is_like'] = in_array($this->user_result['member_id'], $post_like)? true: false;
			$info['is_star'] = in_array($this->user_result['member_id'], $stars)? true: false;
			$info['id_del'] = $this->user_result['member_id'] == $info['member_id']? true: false;
		}
		
		$info['post_like'] = $post_like == ['']? 0: count($post_like);
		$info['stars'] = $stars == ['']? 0: count($stars);
		$info['comment_count'] = M('info_comments')->where(array('post_id'=>$id, 'status'=>1))->count();
		$info['smeta'] = json_decode($info['smeta'],true);

		$comments = M('info_comments')->where(array('post_id'=>$id, 'status'=>1))->order('createtime desc')->select();
		foreach ($comments as  &$value) {
			if ($value['to_mid'] ==0) {
				unset($value['to_mid']);
				unset($value['to_name']);
				unset($value['to_userphoto']);
			}
			if(!empty($this->user_result['member_id'])){
				$value['is_del'] = $this->user_result['member_id'] == $value['from_mid']? true: false;
			}
		}
		$info['comments'] = $comments;

		if($info !== false){
			$jret['flag'] = 1;
			$jret['result'] = $info;
	        $this->ajaxreturn($jret);
	    }else {
			$this->jerror("查询失败");
		}
	}
	
	// 信息列表
	public function getInfoList(){
		$cg_id = (int)I('request.cg_id');
		$post = I('request.post');
		$member_id = I('request.member_id');
		$star = I('request.star');
		$pagination = (array)I('request.pagination');
		$type = I('request.type');

		$join1 = '__MEMBER__ c ON a.post_author = c.member_id';
		$join2 = '__INFOS_RELATIONSHIPS__ b ON a.id = b.object_id';

		$field = 'a.id,a.post_addr,a.post_date,a.post_content,a.smeta,a.post_like,a.stars,b.status,c.userphoto,c.username,c.member_id';
		
		$where['b.status'] = 1;
		// 各分类下的信息列表
		if (isset($cg_id) && !empty($cg_id)) {
			$where['b.cg_id'] = $cg_id;
		}
		// 我的发布
		if ($post == 'true') {
			if (empty($this->user_result['member_id'])) {
				$this->jerror('u have to auth!');
			}
			if ($member_id == $this->user_result['member_id']) {
				unset($where['b.status']);
			}
			$where['a.post_author']=$member_id;
		}
		// 我的收藏
		if ($star == 'true') {
			if (empty($this->user_result['member_id'])) {
				$this->jerror('u have to auth!');
			}
			$where['a.stars']=array('like',"%".$this->user_result['member_id']."%");
		}
		// 
		if ($type == 'false') {
			$where['a.type'] = 0;
		}elseif ($type == 'true') {
			$where['a.type'] = 1;
		}
		
		// 排序规则：置顶>发布时间
		$order = 'a.istop,a.post_date DESC';

		// todo：活动数量多了需要有偏移量，对应参数也需调整
		if (count($pagination) == 2) {
			$where['a.id'] = array('GT',(int)$pagination['id']);
			$limit = (int)$pagination['epage'];
		}

		$list = $this->info_m->alias('a')->join($join1)->join($join2)->field($field)->where($where)->order($order)->limit($limit)->select();

		foreach ($list as $key => &$value) {
			if(!empty($this->user_result['member_id'])){
				$post_like = explode(',', $value['post_like']);
				$stars = explode(',', $value['stars']);
				$value['is_like'] = in_array($this->user_result['member_id'], $post_like)? true: false;
				$value['is_star'] = in_array($this->user_result['member_id'], $stars)? true: false;
				$value['is_del'] = $this->user_result['member_id'] == $value['member_id']? true: false;
			}
			
			$value['post_like'] = $post_like == ['']? 0: count($post_like);
			$value['stars'] = $stars == ['']? 0: count($stars);
			$value['status'] = (bool)$value['status'];
			$value['comment_count'] = M('info_comments')->where(array('post_id'=>$value['id'], 'status'=>1))->count();
			$value['smeta'] = json_decode($value['smeta'],true);
		}

		if ($list !== false) {
			$jret['flag'] = 1;
			$jret['result'] = $list;
	        $this->ajaxreturn($jret);
		}else {
			$this->jerror("查询失败");
		}
	}

	// 信息发布
	public function addInfo(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$cg_id = (int)I('request.cg_id');
		$post_content = (string)I('request.post_content');
		$post_addr = (string)I('request.post_addr');
		$smeta = (array)I('request.smeta');

		if (empty($cg_id) || empty($post_content) || empty($post_addr)) {
			$this->jerror("参数缺失");
		}
		$cate = M('Categorys')->field('type,name')->where(array('cg_id'=>$cg_id))->find();
		if ($cate['type'] == 1) {
			// 判断发布频率，圈子发布需大于30min
			$post_date = $this->info_m->where(array('type'=>1, 'post_author'=>$this->user_result['member_id']))->order('post_date desc')->getField('post_date');
			$d = time()-strtotime($post_date);
			if ($d < 1800) {
				$minute = ceil((1800-$d)/60);
				$this->jerror("发布过于频繁，请".$minute."分钟后再试！");
			}
		}elseif ($cate['type'] == 0) {
			$point = M('Member')->where(array('member_id'=>$this->user_result['member_id']))->getField('point');
			if ($point < 100) {
				$this->jerror("积分不足，请增加活跃度来获取积分！");
			}
		}
		

		$info['post_author'] = $this->user_result['member_id'];
		$info['post_date'] = date('Y-m-d H:i:s');
		$info['post_content'] = $post_content;
		$info['post_addr'] = $post_addr;
		$info['type'] = $cate['type'];
		$info['cg_name'] = $cate['name'];

		if( count($smeta)){
			$info['smeta'] = json_encode($smeta);
		}

		$result = $this->info_m->add($info);

		if ($result) {
			$data['object_id'] = $result;
			$data['cg_id'] = $cg_id;
			$data['cg_name'] = $cate['name'];
			$re = M('InfosRelationships')->add($data);
			if ($re) {
				$point = [];
				$point['action'] = $cate['type'] == 1? '0': '4';
				$point['point'] = $cate['type'] == 1? '30': '-100';
				$point['member_id'] = $this->user_result['member_id'];
				$point['addtime'] = date('Y-m-d H:i:s');
				$point['daily_date'] = date('Y-m-d 00:00:00');
				$point['daily_m'] = M('daily_points');
				$point['weekly_date'] = date('Y-m-d 00:00:00',strtotime(date("Y-m-d")." -".(date('w',strtotime(date("Y-m-d"))) ? date('w',strtotime(date("Y-m-d"))) - 1 : 6).' days'));
				$point['weekly_m'] = M('weekly_points');
				A('Point')->setPoint($point);
				
				$jret['flag'] = 1;
	        	$this->ajaxreturn($jret);
			}else{
				$this->jerror('发布失败');
			}
		}else{
			$this->jerror('发布失败');
		}
	}

	public function upPic(){
		
	    $savepath='qmcy/'.date('Ymd').'/';
	    $config=array(
        		'rootPath' => './'.C("UPLOADPATH"),
        		'savePath' => $savepath,
        		'maxSize' => 2097152,
        		'saveName'   =>    array('uniqid',''),
        		'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
        		'autoSub'    =>    false,
        );
		$upload = new \Think\Upload($config);// 
		$info=$upload->upload();
		if ($info) {
			foreach ($info as $key => $value) {
				$filepath[$key] = $savepath.$value['savename'];
			}
			$jret['flag'] = 1;
			$jret['result'] = $filepath;
	        $this->ajaxreturn($jret);
        } else {
            $this->jerror($upload->getError());
        }
	}

	// 评论
	public function setComment(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$id = (int)I('request.id');
		$to_mid = (int)I('request.to_mid');
		$to_name = (string)I('request.to_name');
		$to_userphoto = (string)I('request.to_userphoto');
		$content = (string)I('request.content');

		if (empty($id) || empty($content)) {
			$this->jerror("参数缺失");
		}
		
		$where['id'] = $id;
		$post_author = $this->info_m->where($where)->getField('post_author');
		if ($post_author == $this->user_result['member_id'] && empty($to_mid)) {
			$this->jerror('不可以给自己评论');
		}

		if ($to_mid == $this->user_result['member_id']) {
			$this->jerror('不可以给自己回复');
		}

		$data['post_id'] = $id;

		$data['from_mid'] = $this->user_result['member_id'];
		$data['from_name'] = $this->user_result['username'];
		$data['from_userphoto'] = $this->user_result['userphoto'];
		$data['createtime'] = date('Y-m-d H:i:s');
		$data['content'] = $content;
		if (!empty($to_mid) && !empty($to_name) && !empty($to_userphoto)) {
			$data['to_mid'] = $to_mid;
			$data['to_name'] = $to_name;
			$data['to_userphoto'] = $to_userphoto;
		}
		$comment_id = M('info_comments')->where(array('post_id'=>$id, 'from_mid'=>$this->user_result['member_id']))->getField('id');
		$re = M('info_comments')->add($data);

		if ($re) {
			if(!isset($comment_id) && ($post_author !== $this->user_result['member_id'])){
				$point['action'] = '2';
				$point['point'] = '10';
				$point['member_id'] = $this->user_result['member_id'];
				$point['addtime'] = date('Y-m-d H:i:s');
				$point['daily_date'] = date('Y-m-d 00:00:00');
				$point['daily_m'] = M('daily_points');
				$point['weekly_date'] = date('Y-m-d 00:00:00',strtotime(date("Y-m-d")." -".(date('w',strtotime(date("Y-m-d"))) ? date('w',strtotime(date("Y-m-d"))) - 1 : 6).' days'));
				$point['weekly_m'] = M('weekly_points');
				A('Point')->setPoint($point);
			}

			$jret['flag'] = 1;
	        $this->ajaxreturn($jret);
		}else{
			$msg = isset($to_mid)? '回复失败': '评论失败';
			$this->jerror($msg);
		}
	}

	// 点赞&&取消点赞
	public function setLikeStatus(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$action = I('request.action');
		$id = (int)I('request.id');
		if (isset($id) && !empty($id)) {
			$where['id'] = $id;
		}else{
			$this->jerror("参数缺失");
		}

		$post_like = $this->info_m->where($where)->field('post_author,post_like')->find();
		$post_like_arr = strlen($post_like['post_like'])>0? explode(',', $post_like['post_like']): [];

		if ($action == 'true') {
			if ($post_like['post_author'] == $this->user_result['member_id']) {
				$this->jerror('不可以给自己点赞');
			}
			if (in_array($this->user_result['member_id'], $post_like_arr)) {
				$this->jerror("已点赞，不可重复点赞");
			}else{
				$post_like_arr[] = $this->user_result['member_id'];
				$data['post_like'] = implode(',', $post_like_arr);
				$re = $this->info_m->where($where)->save($data);
			}
			// 点赞被赞人获得积分
			$point['action'] = '1';
			$point['point'] = '10';
			$point['member_id'] = $post_like['post_author'];
			$point['addtime'] = date('Y-m-d H:i:s');
			$point['daily_date'] = date('Y-m-d 00:00:00');
			$point['daily_m'] = M('daily_points');
			$point['weekly_date'] = date('Y-m-d 00:00:00',strtotime(date("Y-m-d")." -".(date('w',strtotime(date("Y-m-d"))) ? date('w',strtotime(date("Y-m-d"))) - 1 : 6).' days'));
			$point['weekly_m'] = M('weekly_points');
			A('Point')->setPoint($point);

		}elseif ($action == 'false') {
			if (!in_array($this->user_result['member_id'], $post_like_arr)) {
				$this->jerror("未点赞，不可取消点赞");
			}else{
				array_splice($post_like_arr, array_search($this->user_result['member_id'], $post_like_arr), 1);
				$data['post_like'] = implode(',', $post_like_arr);
				$re = $this->info_m->where($where)->save($data);
			}
		}

		if ($re !== false) {
			$jret['flag'] = 1;
	        $this->ajaxreturn($jret);
		}else{
			$this->jerror('点赞失败');
		}
	}

	// 收藏&&取消收藏
	public function setStarStatus(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$action = I('request.action');
		$id = (int)I('request.id');
		if (isset($id) && !empty($id)) {
			$where['id'] = $id;
		}else{
			$this->jerror("参数缺失");
		}

		$stars = $this->info_m->where($where)->field('post_author,stars')->find();
		$stars_arr = strlen($stars['stars'])>0? explode(',', $stars['stars']): [];
		if ($action == 'true') {
			if ($stars['post_author'] == $this->user_result['member_id']) {
				$this->jerror('不可以收藏自己的信息');
			}
			if (in_array($this->user_result['member_id'], $stars_arr)) {
				$this->jerror("已收藏，不可重复收藏");
			}else{
				$stars_arr[] = $this->user_result['member_id'];
				$data['stars'] = implode(',', $stars_arr);
				$re = $this->info_m->where($where)->save($data);
			}
		}elseif ($action == 'false') {
			if (!in_array($this->user_result['member_id'], $stars_arr)) {
				$this->jerror("未收藏，不可取消收藏");
			}else{
				array_splice($stars_arr, array_search($this->user_result['member_id'], $stars_arr), 1);
				$data['stars'] = implode(',', $stars_arr);
				$re = $this->info_m->where($where)->save($data);
			}
		}
		
		if ($re !== false) {
			$jret['flag'] = 1;
	        $this->ajaxreturn($jret);
		}else{
			$this->jerror('收藏失败');
		}
	}

	// 信息删除
	public function delInfo(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$id = (int)I('request.id');
		if(empty($id)){
			$this->jerror('参数缺失');
		}
		$post_author = $this->info_m->where(array('id'=>$id))->getField('post_author');
		if ($post_author == $this->user_result['member_id']) {
			$re1 = $this->info_m->where(array('id'=>$id))->delete();
			$re2 = M('InfosRelationships')->where(array('object_id'=>$id))->delete();
		}else{
			$this->jerror("只可以删除自己发布的信息");
		}
		if ($re1 !== false && $re2 !== false) {
			$jret['flag'] = 1;
	        $this->ajaxreturn($jret);
		}else{
			$this->jerror('删除失败');
		}
	}

	// 评论删除
	public function delComment(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$id = (int)I('request.id');
		if(empty($id)){
			$this->jerror('参数缺失');
		}
		$from_mid = M('InfoComments')->where(array('id'=>$id))->getField('from_mid');
		if ($from_mid == $this->user_result['member_id']) {
			$re = M('InfoComments')->where(array('id'=>$id))->delete();
		}else{
			$this->jerror("只可以删除自己发布的评论");
		}
		if ($re !== false) {
			$jret['flag'] = 1;
	        $this->ajaxreturn($jret);
		}else{
			$this->jerror('删除失败');
		}
	}

	// 信息举报
	public function tipOff(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$post_id = (int)I('request.post_id');
		$type = (int)I('request.type');
		$content = (int)I('request.content');

		if (isset($post_id) && !empty($post_id) && isset($type) && !empty($type) && isset($content) && !empty($content)) {
			$data['member_id'] = $this->user_result['member_id'];
			$data['content'] = $content;
			$data['type'] = $type;
			$data['post_id'] = $post_id;
			$data['createtime'] = date('Y-m-d H:i:s');
			$re = M('InfoTipoff')->add($data);
		}else{
			$this->jerror('参数缺失');
		}
		if ($re !== false) {
			$jret['flag'] = 1;
	        $this->ajaxreturn($jret);
		}else{
			$this->jerror('举报失败');
		}
	}

}