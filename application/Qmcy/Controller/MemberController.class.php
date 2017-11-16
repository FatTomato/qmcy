<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;
use Qmcy\Lib\WXBizDataCrypt;

class MemberController extends BaseController {

	protected $mr_m;
	protected $m_m;
	
	public function _initialize() {
		$this->mr_m = M('MembersRelationships');
		$this->m_m = M('Member');
	}

	// 我的粉丝列表
	public function getFans(){
		$fans = $this->mr_m->field('fan_id, fan_name, fan_photo')->where(array('follow_id'=>$this->user_result['user_id']))->select();

		foreach ($fans as &$value) {
			$id = $this->mr_m->where(array('fan_id'=>$this->user_result['user_id'], 'follow_id'=>$value['fan_id']))->getField('id');
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
		$follows = $this->mr_m->field('follow_id, follow_name, follow_photo')->where(array('fan_id'=>$this->user_result['user_id']))->select();

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
		$action = I('request.action');
		$member_id = (int)I('request.member_id');
		$name = (string)I('request.name');
		$photo = (string)I('request.photo');

		if (empty($member_id)) {
			$this->jerror("参数缺失");
		}
		
		if ($action == 'false') {
			// 取关
			$re = $this->mr_m->where(array('fan_id'=>$this->user_result['user_id'], 'follow_id'=>$member_id))->delete();
		}elseif ($action == 'true') {
			// 关注
			if (empty($name) || empty($photo)) {
				$this->jerror("参数缺失");
			}
			$data['follow_id'] = $member_id;
			$data['follow_name'] = $name;
			$data['follow_photo'] = $photo;
			$data['fan_id'] = $this->user_result['user_id'];
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
		$status = I('request.status');
		$cg_id = (int)I('request.cg_id');

		if (empty($cg_id)) {
			$this->jerror("参数缺失");
		}
		
		if ($status == 'false') {
			// 取关
			$re = M('CiclesRelationships')->where(array('member_id'=>$this->user_result['user_id'], 'cg_id'=>$cg_id))->delete();
		}elseif ($status == 'true') {
			// 关注
			$cg_name = I('request.cg_name');
			$data['member_id'] = $this->user_result['user_id'];
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

	// 基本信息
	public function getMemberInfo(){
		$memberid = I('memberid');
		if (isset($memberid) && !empty($memberid)) {
			$info = $this->m_m->field('username,userphoto,exp')->where(array('user_id'=>$memberid))->find();
			$memberinfo['name'] = $info['username'];
			$memberinfo['photo'] = $info['userphoto'];
			$memberinfo['point'] = $info['exp'];
			$memberinfo['follow_num'] = $this->mr_m->where(array('fan_id'=>$memberid))->count();
			$memberinfo['fan_num'] = $this->mr_m->where(array('follow_id'=>$memberid))->count();
			$re = $this->mr_m->where(array('fan_id'=>$this->user_result['user_id'],'follow_id'=>$memberid))->find();
			$memberinfo['is_follow'] = $re? true: false;
		}else{
			$memberinfo['id'] = $this->user_result['user_id'];
			$memberinfo['name'] = $this->user_result['username'];
			$memberinfo['photo'] = $this->user_result['userphoto'];
			$memberinfo['point'] = $this->m_m->where(array('user_id'=>$this->user_result['user_id']))->getField('exp');
			$memberinfo['post_num'] = M('Infos')->where(array('post_author'=>$this->user_result['user_id']))->count();
			$memberinfo['follow_num'] = $this->mr_m->where(array('fan_id'=>$this->user_result['user_id']))->count();
			$memberinfo['fan_num'] = $this->mr_m->where(array('follow_id'=>$this->user_result['user_id']))->count();
			$memberinfo['cicles'] = M('CiclesRelationships')->where(array('member_id'=>$this->user_result['user_id'], 'status'=>1))->select();
		}
		if ($memberinfo) {
			$jret['flag'] = 1;
			$jret['result'] = $memberinfo;
		    $this->ajaxreturn($jret);
		}
	}

	// 授权登陆
	public function onLogin(){
		$qm_code = I('request.code');
		$signature = I('request.signature');
		$rawData = I('request.rawData');
		$encryptedData = I('request.encryptedData');
		$iv = I('request.iv');

		if (isset($qm_code) && !empty($qm_code) && isset($signature) && !empty($signature) && isset($encryptedData) && !empty($encryptedData) && isset($iv) && !empty($iv)) {
			$appid = C('APPID');
			$secret = C('SECRET');
			$url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$qm_code.'&grant_type=authorization_code';

			$re = http_get($url);
			
			if(!isset($re['session_key'])) {
				$this->jerror('curl error');
			}

			$sessionKey = $re['session_key'];

			$signature2 = sha1($rawData . $sessionKey);
    		if ($signature2 !== $signature) {
		        $this->jerror("sign Not Match");
		    }

			$pc = new WXBizDataCrypt($appid, $sessionKey);
		    $errCode = $pc->decryptData($encryptedData, $iv, $data);

		    if ($errCode !== 0) {
		        $this->jerror("encryptData Not Match");
		    }

		    $data = json_decode($data, true);

		    $member = $this->m_m->where( array('unionId'=>$data['unionId']) )->find();
		    if (!isset($member)) {
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
			    $user_id = $this->m_m->add($memberinfo);
		    }else{
		    	$user_id = $member['user_id'];
		    }

		    $save_data = array(
	            'logintime' => $u['logintime']+1,
	            'last_login_time' => date("Y-m-d H:i:s", time())
	        );
	        $this->m_m->where(array('user_id'=>$user_id) )->save($save_data);

		    $session3rd = md5(time());//randomFromDev(16);

		    $_SESSION [$session3rd.'login_endtime'] = time()+3600;
		    $_SESSION [$session3rd] = $user_id;

		    // $data['session3rd'] = $session3rd;
		    // cache($session3rd, $data['openId'] . $sessionKey);

		    $this->jret['flag'] = 1;
		    $this->jret['reset']['session3rd'] = $session3rd;
		    $this->ajaxReturn($this->jret);
			
		}else{
			$this->jerror('参数缺失');
		}
	}
	
	// 手机验证
	public function checkPhone(){
		// todo
	}

}