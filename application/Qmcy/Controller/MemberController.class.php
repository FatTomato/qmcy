<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;
use Qmcy\Lib\WXBizDataCrypt;
use Qmcy\Lib\Sms\SmsSingleSender;

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
			$this->jerror('您还没有登录！');
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
			$this->jret['flag'] = 1;
			$this->jret['result'] = $fans;
	        $this->ajaxReturn($this->jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 我的关注列表
	public function getFollows(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$follows = $this->mr_m->field('follow_id, follow_name, follow_photo')->where(array('fan_id'=>$this->user_result['member_id']))->select();

		foreach ($follows as &$value) {
			$value['is_follow'] = 'true';
			$value['id'] = $value['follow_id'];
			$value['name'] = $value['follow_name'];
			$value['photo'] = $value['follow_photo'];
		}

		if($follows !== false){
			$this->jret['flag'] = 1;
			$this->jret['result'] = $follows;
	        $this->ajaxReturn($this->jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 关注&&取关
	public function setRelationship(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$is_follow = I('request.is_follow');
		$member_id = (int)I('request.member_id');
		$name = (string)I('request.name');
		$photo = (string)I('request.photo');

		if (empty($member_id)) {
			$this->jerror("参数缺失");
		}
		
		if ($is_follow == 'false') {
			// 取关
			$re = $this->mr_m->where(array('fan_id'=>$this->user_result['member_id'], 'follow_id'=>$member_id))->delete();
		}elseif ($is_follow == 'true') {
			// 关注
			if (empty($name) || empty($photo)) {
				$this->jerror("参数缺失");
			}
			$data['follow_id'] = $member_id;
			$data['follow_name'] = $name;
			$data['follow_photo'] = $photo;
			$data['fan_id'] = $this->user_result['member_id'];
			$data['fan_name'] = $this->user_result['username'];
			$data['fan_photo'] = $this->user_result['userphoto'];
			$data['addtime'] = date('Y-m-d H:i:s');
			$re = $this->mr_m->add($data);
		}

		if($re){
			$this->jret['flag'] = 1;
	        $this->ajaxReturn($this->jret);
	    }else {
	    	$msg = $action == 'false'? '取关失败': '关注失败';
			$this->jerror($msg);
		}
	}

	// 进圈&&退圈
	public function setCicleStatus(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
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
			$data['addtime'] = date('Y-m-d H:i:s');
			$re = M('CiclesRelationships')->add($data);
		}

		if($re){
			$this->jret['flag'] = 1;
	        $this->ajaxReturn($this->jret);
	    }else {
	    	$msg = $action == 'false'? '退出圈子失败': '加入圈子失败';
			$this->jerror($msg);
		}
	}

	// 基本信息
	public function getMemberInfo(){
		$member_id = I('request.member_id');
		$m_id = 0;

		if ($member_id == $this->user_result['member_id'] || ($this->user_result['member_id']) && empty($member_id)) {
			$memberinfo = $this->user_result;

			$join = '__CATEGORYS__ b ON a.cg_id = b.cg_id';
			$field = 'b.cg_id,b.name,b.icon';
			$memberinfo['cicles'] = M('CiclesRelationships')->alias('a')->join($join)->field($field)->where(array('a.member_id'=>$this->user_result['member_id'], 'a.status'=>1))->select();
			if (count($memberinfo['cicles']) > 0) {
				foreach ($memberinfo['cicles'] as &$value) {
					$value['icon'] = sp_get_image_preview_url($value['icon']);
				}
			}
			
			$memberinfo['message_num'] = M('Message')->alias('m')->join('__INFO_COMMENTS__ c ON c.id = m.comment_id')->where(array('m.member_id'=>$this->user_result['member_id'], 'm.status'=>0))->count();
			$m_id = $this->user_result['member_id'];
		} elseif ($member_id !== $this->user_result['member_id']) {
			$memberinfo = $this->m_m->field('member_id,username,userphoto,point')->where(array('member_id'=>$member_id))->find();

			if (!empty($this->user_result['member_id'])) {
				$re = $this->mr_m->where(array('fan_id'=>$this->user_result['member_id'],'follow_id'=>$member_id))->find();
				$memberinfo['is_follow'] = $re? true: false;
			}
			$m_id = $member_id;
		}
		if ($m_id) {
			$memberinfo['post_num'] = M('Infos')->where(array('post_author'=>$m_id))->count();
			$memberinfo['follow_num'] = $this->mr_m->where(array('fan_id'=>$m_id))->count();
			$memberinfo['fan_num'] = $this->mr_m->where(array('follow_id'=>$m_id))->count();
			$memberinfo['shop_id'] = M('Shop')->where(array('member_id'=>$m_id))->getField('id');
		}

		if ($memberinfo) {
			$this->jret['flag'] = 1;
			$this->jret['result'] = $memberinfo;
		    $this->ajaxReturn($this->jret);
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
		    $memberinfo['country'] = $data['country'];
		    // 邀请人
		    $invite_sign = I('request.token');
		    if ($invite_sign) {
		    	if (S($invite_sign)) {
	                $invite_userid = $this->m_m->where(array('openId'=>S($invite_sign)))->getField('member_id');
	                if ($invite_userid) {
	                    $memberinfo['invite_userid'] = $invite_userid;
	                    // 介绍人积分
	                    $point['action'] = '4';
						$point['point'] = '100';
						$point['member_id'] = $invite_userid;
						$point['addtime'] = date('Y-m-d H:i:s');
						$point['daily_date'] = date('Y-m-d 00:00:00');
						$point['daily_m'] = M('daily_points');
						$point['weekly_date'] = date('Y-m-d 00:00:00',strtotime(date("Y-m-d")." -".(date('w',strtotime(date("Y-m-d"))) ? date('w',strtotime(date("Y-m-d"))) - 1 : 6).' days'));
						$point['weekly_m'] = M('weekly_points');
						A('Point')->setPoint($point);
	                }
	            }
		    }
		    
		    $memberinfo['addtime'] = date('Y-m-d H:i:s');
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

	    $this->jret['flag'] = 1;
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

		$session3rd = randomFromDev(16);
		S($session3rd, $openId, 86400*7);

	    $this->jret['flag'] = 1;
	    $this->jret['reset']['session3rd'] = $session3rd;
	    $this->ajaxReturn($this->jret);
	}
	
	// 发送验证码
	public function sendSms(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$phone = I('request.phone');
		if(empty($phone)){
			$this->jerror('请输入手机号！');
		}
		// 发送频率限制
		$last_addtime = M('Validcode')->where(array('phone'=>$phone))->order('addtime desc')->getField('addtime');
		if ( time() - strtotime($last_addtime) < 600) {
			$this->jerror('不可频繁操作，请稍后再试！');
		}
		// 判断手机号正确性、是否已验证
		if(preg_match("/^1[34578]\d{9}$/", $phone)){
			$id = $this->m_m->where(array('phone'=>$phone))->getField('member_id');
			if($id){
				$this->jerror('手机号已存在，请勿重复验证！');
			}
		}else{
			$this->jerror('手机号有误！');
		}
		$appid = C('SMSAPPID');
		$appkey = C('SMSAPPKEY');
		$sms = new SmsSingleSender($appid, $appkey);
		$random = rand(100000, 999999);
		$params = [$random, 10];
		$result = $sms->sendWithParam("86", $phone, 74042, $params, "", "", "");
		$rsp = json_decode($result, true);
		if ($rsp['result'] == 0) {
			// 缓存在服务器
			S($this->user_result['member_id'].'pvalid', $random, 600);
			M('Validcode')->add(array('phone'=>$phone, 'addtime'=>date('Y-m-d H:i:s')));
			$this->jret['flag'] = 1;
			$this->ajaxReturn($this->jret);
		}else{
			$this->jerror('发送失败，请稍后再试！');
		}
	}

	// 检测验证码
	public function checkVaild(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$validcode = I('request.validcode');
		$phone = I('request.phone');
		if (empty($validcode)) {
			$this->jerror('请输入验证码！');
		}
		$re_phone = $this->m_m->where(array('member_id'=>$this->user_result['member_id']))->getField('phone');
		if (S($this->user_result['member_id'].'pvalid')) {
			if (S($this->user_result['member_id'].'pvalid') == $validcode) {
				$re = $this->m_m->where(array('member_id'=>$this->user_result['member_id']))->save(array('phone'=>$phone));
			} else {
				$this->jerror('验证码不正确！');
			}
		} else {
			$this->jerror('验证码已过期，请重新发送！');
		}

		if ($re) {
			$this->jret['flag'] = 1;
			// 首次绑定手机，积分+50
			if ($re_phone == '') {
				$point['action'] = '2';
				$point['point'] = '50';
				$point['member_id'] = $this->user_result['member_id'];
				$point['addtime'] = date('Y-m-d H:i:s');
				$point['daily_date'] = date('Y-m-d 00:00:00');
				$point['daily_m'] = M('daily_points');
				$point['weekly_date'] = date('Y-m-d 00:00:00',strtotime(date("Y-m-d")." -".(date('w',strtotime(date("Y-m-d"))) ? date('w',strtotime(date("Y-m-d"))) - 1 : 6).' days'));
				$point['weekly_m'] = M('weekly_points');
				A('Point')->setPoint($point);
			}

		// 归属地gsd_  todo
		

			$this->ajaxReturn($this->jret);
		} else {
			$this->jerror('验证失败！');
		}
	}

	// 我的消息
	public function getMessages(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}

		$messages = M('Message')->alias('m')->join('__INFO_COMMENTS__ c ON c.id = m.comment_id')->field('m.comment_id')->where(array('m.member_id'=>$this->user_result['member_id']))->select();
		if ($messages) {
			$ids = array_column($messages, 'comment_id');
		} else {
			$this->jerror('您暂时没有消息！');
		}

		$unread_ids = M('Message')->where(array('member_id'=>$this->user_result['member_id'], 'status'=>0))->getField('comment_id', true);

		$comments = M('info_comments')->where(array('id'=>array('in',$ids),'status'=>1))->order('createtime desc')->select();

		foreach ($comments as  &$value) {
			if (!($value['post_id'] == $this->user_result['member_id'] && $value['to_mid'] != $this->user_result['member_id'])) {
				unset($value['to_mid']);
				unset($value['to_name']);
				unset($value['to_userphoto']);
			}
			if ($unread_ids) {
				if (in_array($value['id'], $unread_ids)) {
					$value['is_new'] = true;
				}
			}
			
			$info = M('Infos')->where(array('id'=>$value['post_id']))->getField('post_content');
			$value['info'] = mb_substr($info, 0, 10);
		}

		M('Message')->where(array('member_id'=>$this->user_result['member_id']))->save(array('status'=>1));

		if ($comments) {
			$this->jret['flag'] = 1;
			$this->jret['result'] = $comments;
		} else {
			$this->jerror('获取消息失败！');
		}

        $this->ajaxReturn($this->jret);
	}

	// 首页获取是否有新消息
	public function isUnread(){
		if ($this->user_result['member_id']) {
			$this->jret['unread_num'] = M('Message')->where(array('member_id'=>$this->user_result['member_id'], 'status'=>0))->count();
		}
		$this->jret['flag'] = 1;
		$this->ajaxReturn($this->jret);
	}

	// 信息&&店铺举报
	public function tipOff(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$id = (int)I('request.id');
		$type = I('request.type');
		$content = I('request.content');

		if (empty($id) || empty($type) || empty($content)) {
			$this->jerror('参数缺失');
		}else{
			$last_time = M('Tipoff')->where(array('member_id'=>$this->user_result['member_id']))->getField('addtime');
			if ( time()-strtotime($last_time) < 600 ) {
				$this->jerror('您刚提交过举报，请稍后再操作！');
			} else {
				$data['member_id'] = $this->user_result['member_id'];
				$data['content'] = $content;
				$data['type'] = $type;
				$data['id'] = $id;
				$data['addtime'] = date('Y-m-d H:i:s');
				$m = $type=='info'? M('Infos'): M('Shop');
				$field = $type=='info'? 'post_author': 'member_id';
				$data['illegal_id'] = $m->where(array('id'=>$id))->getField($field);
				$re = M('Tipoff')->add($data);
			}
		}
		if ($re !== false) {
			$this->jret['flag'] = 1;
	        $this->ajaxReturn($this->jret);
		}else{
			$this->jerror('举报失败');
		}
	}

}