<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;
use Qmcy\Lib\WXBizDataCrypt;

class MemberController extends BaseController {

	protected $mr_m;
	protected $m_m;
	
	public function _initialize() {
		parent::_initialize();
		$this->mr_m = M('MembersRelationships');
		$this->m_m = M('Member');
	}

	// 我的粉丝列表
	public function getFans(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$fans = $this->mr_m->field('fan_id, fan_name, fan_photo')->where(array('follow_id'=>$this->user_result['member_id']))->select();

		foreach ($fans as &$value) {
			$id = $this->mr_m->where(array('fan_id'=>$this->user_result['member_id'], 'follow_id'=>$value['fan_id']))->getField('id');
			$value['is_follow'] = isset($id)? 1: 0;
			$value['id'] = $value['fan_id'];
			$value['name'] = $value['fan_name'];
			$value['photo'] = $value['fan_photo'];
		}

		if($fans !== false){
			$jret['flag'] = 1;
			$jret['result'] = $fans;
	        $this->ajaxreturn($jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 我的关注列表
	public function getFollows(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$follows = $this->mr_m->field('follow_id, follow_name, follow_photo')->where(array('fan_id'=>$this->user_result['member_id']))->select();

		foreach ($follows as &$value) {
			$value['id'] = $value['follow_id'];
			$value['name'] = $value['follow_name'];
			$value['photo'] = $value['follow_photo'];
		}

		if($follows !== false){
			$jret['flag'] = 1;
			$jret['result'] = $follows;
	        $this->ajaxreturn($jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 关注&&取关
	public function setRelationship(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$action = I('request.is_follow');
		$member_id = (int)I('request.member_id');
		$name = (string)I('request.name');
		$photo = (string)I('request.photo');

		if (empty($member_id)) {
			$this->jerror("参数缺失");
		}
		
		if ($action == 'false') {
			// 取关
			$re = $this->mr_m->where(array('fan_id'=>$this->user_result['member_id'], 'follow_id'=>$member_id))->delete();
		}elseif ($action == 'true') {
			// 关注
			if (empty($name) || empty($photo)) {
				$this->jerror("参数缺失");
			}
			$data['follow_id'] = $member_id;
			$data['follow_name'] = $name;
			$data['follow_photo'] = $photo;
			$data['fan_id'] = $this->user_result['member_id'];
			$data['fan_name'] = $this->member_name;
			$data['fan_photo'] = $this->member_photo;
			$data['addtime'] = date('Y-m-d h:i:s');
			$re = $this->mr_m->add($data);
		}

		if($re){
			$jret['flag'] = 1;
	        $this->ajaxreturn($jret);
	    }else {
	    	$msg = $action == 'false'? '取关失败': '关注失败';
			$this->jerror($msg);
		}
	}

	// 进圈&&退圈
	public function setCicleStatus(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		$status = I('request.status');
		$cg_id = (int)I('request.cg_id');

		if (empty($cg_id)) {
			$this->jerror("参数缺失");
		}
		
		if ($status == 'false') {
			// 取关
			$re = M('CiclesRelationships')->where(array('member_id'=>$this->user_result['member_id'], 'cg_id'=>$cg_id))->delete();
		}elseif ($status == 'true') {
			// 关注
			$cg_name = I('request.cg_name');
			$data['member_id'] = $this->user_result['member_id'];
			$data['cg_id'] = $cg_id;
			$data['cg_name'] = $cg_name;
			$data['addtime'] = date('Y-m-d h:i:s');
			$re = M('CiclesRelationships')->add($data);
		}

		if($re){
			$jret['flag'] = 1;
	        $this->ajaxreturn($jret);
	    }else {
	    	$msg = $action == 'false'? '退出圈子失败': '加入圈子失败';
			$this->jerror($msg);
		}
	}

	public function checkPoint(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('u have to auth!');
		}
		
		$point = M('Member')->where(array('member_id'=>$this->user_result['member_id']))->getField('point');

		if($point !== false){
			$jret['flag'] = 1;
			$is_enough = $point > 100?true:false;
			$jret['result']['point'] = $point;
			$jret['result']['is_enough'] = $is_enough;
	        $this->ajaxreturn($jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 基本信息
	public function getMemberInfo(){
		$member_id = I('request.member_id');
		if (empty($member_id) || $this->user_result['member_id'] == $member_id) {
			$memberinfo['id'] = $this->user_result['member_id'];
			$memberinfo['name'] = $this->user_result['username'];
			$memberinfo['photo'] = $this->user_result['userphoto'];
			$memberinfo['point'] = $this->m_m->where(array('member_id'=>$this->user_result['member_id']))->getField('point');
			$memberinfo['post_num'] = M('Infos')->where(array('post_author'=>$this->user_result['member_id']))->count();
			$memberinfo['follow_num'] = $this->mr_m->where(array('fan_id'=>$this->user_result['member_id']))->count();
			$memberinfo['fan_num'] = $this->mr_m->where(array('follow_id'=>$this->user_result['member_id']))->count();
			$join = '__CATEGORYS__ b ON a.cg_id = b.cg_id';
			$field = 'b.cg_id,b.name,b.icon';
			$cicles = M('CiclesRelationships')->alias('a')->join($join)->field($field)->where(array('a.member_id'=>$this->user_result['member_id'], 'a.status'=>1))->select();
			if (count($cicles) > 0) {
				foreach ($cicles as &$value) {
					$value['icon'] = sp_get_image_preview_url($value['icon']);
				}
			}
			$memberinfo['cicles'] = $cicles;
		}else{
			$info = $this->m_m->field('member_id,username,userphoto,point')->where(array('member_id'=>$member_id))->find();
			$memberinfo['member_id'] = $info['member_id'];
			$memberinfo['name'] = $info['username'];
			$memberinfo['photo'] = $info['userphoto'];
			$memberinfo['point'] = $info['point'];
			$memberinfo['follow_num'] = $this->mr_m->where(array('fan_id'=>$member_id))->count();
			$memberinfo['fan_num'] = $this->mr_m->where(array('follow_id'=>$member_id))->count();
			if (!empty($this->user_result['member_id'])) {
				$re = $this->mr_m->where(array('fan_id'=>$this->user_result['member_id'],'follow_id'=>$member_id))->find();
				$memberinfo['is_follow'] = $re? true: false;
			}
		}
		if ($memberinfo) {
			$jret['flag'] = 1;
			$jret['result'] = $memberinfo;
		    $this->ajaxreturn($jret);
		}
	}

	// 授权登陆
	public function onReg(){
		$qm_code = I('request.code');
		// $signature = I('request.signature');
		// $rawData = I('request.rawData');

		if (isset($qm_code) && !empty($qm_code)) {
			$appid = C('APPID');
			$secret = C('SECRET');
			$url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$qm_code.'&grant_type=authorization_code';

			$re = http_get($url);

			if(!isset($re['session_key'])) {
				$this->jerror('curl error');
			}

			$sessionKey = $re['session_key'];
			$openId = $re['openid'];
		}else{
			$this->jerror('参数缺失');
		}

		$member = $this->m_m->where( array('openId'=>$openId) )->find();

		if (empty($member)) {
			$encryptedData = I('request.encryptedData');
			$iv = I('request.iv');

			// $signature2 = sha1($rawData . $sessionKey);
			//  		if ($signature2 !== $signature) {
			//        $this->jerror("sign Not Match");
			//    }
			$pc = new WXBizDataCrypt($appid, $sessionKey);
		    $errCode = $pc->decryptData($encryptedData, $iv, $data);

		    if ($errCode !== 0) {
		        $this->jerror("encryptData Not Match");
		    }

		    $data = json_decode($data, true);
		    $memberinfo['username'] = $data['nickName'];
		    $memberinfo['userphoto'] = $data['avatarUrl'];
		    $memberinfo['sex'] = $data['gender'];
		    $memberinfo['openId'] = $data['openId'];
		    $memberinfo['language'] = $data['language'];
		    $memberinfo['unionId'] = $data['unionId'];
		    $memberinfo['city'] = $data['city'];
		    $memberinfo['province'] = $data['province'];
		    $memberinfo['country'] = $data['country'];
		    // todo   邀请人
		    // $memberinfo['invite_userid'] = $data['nickName'];
		    $memberinfo['addtime'] = date('Y-m-d h:i:s');
		    $member_id = $this->m_m->add($memberinfo);
		}

		if (!$member_id) {
	        $this->jerror("reg false!");
	    }

	    $save_data = array(
            'logintime' => $u['logintime']+1,
            'last_login_time' => date("Y-m-d H:i:s", time())
        );
        $this->m_m->where(array('member_id'=>$member_id) )->save($save_data);

	    // $session3rd = md5(time());//randomFromDev(16);

	    // S($session3rd, $openId, 86400*7);

	    $this->jret['flag'] = 1;
	    // $this->jret['reset']['session3rd'] = $session3rd;
	    $this->ajaxReturn($this->jret);
	}

	// update session3rd
	public function onLogin(){
		$qm_code = I('request.code');

		if (isset($qm_code) && !empty($qm_code)) {
			$appid = C('APPID');
			$secret = C('SECRET');
			$url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$qm_code.'&grant_type=authorization_code';

			$re = http_get($url);

			if(!isset($re['session_key'])) {
				$this->jerror('curl error');
			}

			$sessionKey = $re['session_key'];
			$openId = $re['openid'];
		}else{
			$this->jerror('参数缺失');
		}

		$member = M('Member')->where(array('openId'=>$openId))->find();
        $this->jret['reset']['is_reg'] = empty($member)? 0: 1;

		$session3rd = md5(time());//randomFromDev(16);
		S($session3rd, $openId, 86400*7);

	    $this->jret['flag'] = 1;
	    $this->jret['reset']['session3rd'] = $session3rd;
	    $this->ajaxReturn($this->jret);
	}
	
	// 手机验证
	public function checkPhone(){
		// todo
		
	}

}