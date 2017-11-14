<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;

class MemberController extends BaseController {

	protected $mr_m;
	protected $m_m;
	
	public function _initialize() {
		$this->mr_m = M('MembersRelationships');
		$this->m_m = M('Member');
		$this->member_id = 1;
	}

	// 我的粉丝列表
	public function getFans(){
		$fans = $this->mr_m->field('fan_id, fan_name, fan_photo')->where(array('follow_id'=>$this->member_id))->select();

		foreach ($fans as &$value) {
			$id = $this->mr_m->where(array('fan_id'=>$this->member_id, 'follow_id'=>$value['fan_id']))->getField('id');
			$value['is_follow'] = isset($id)? 1: 0;
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
		$follows = $this->mr_m->field('follow_id, follow_name, follow_photo')->where(array('fan_id'=>$this->member_id))->select();

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
			$re = $this->mr_m->where(array('fan_id'=>$this->member_id, 'follow_id'=>$member_id))->delete();
		}elseif ($action == 'true') {
			// 关注
			if (empty($name) || empty($photo)) {
				$this->jerror("参数缺失");
			}
			$data['follow_id'] = $member_id;
			$data['follow_name'] = $name;
			$data['follow_photo'] = $photo;
			$data['fan_id'] = $this->member_id;
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
			$re = M('CiclesRelationships')->where(array('member_id'=>$this->member_id, 'cg_id'=>$cg_id))->delete();
		}elseif ($status == 'true') {
			// 关注
			$cg_name = I('request.cg_name');
			$data['member_id'] = $this->member_id;
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
		$memberinfo['id'] = $this->member_id;
		$memberinfo['name'] = $this->username;
		$memberinfo['photo'] = $this->userphoto;
		$memberinfo['point'] = $this->m_m->where(array('user_id'=>$this->member_id))->getField('exp');
		$memberinfo['post_num'] = M('Infos')->where(array('post_author'=>$this->member_id))->count();
		$memberinfo['follow_num'] = $this->mr_m->where(array('fan_id'=>$this->member_id))->count();
		$memberinfo['fan_num'] = $this->mr_m->where(array('follow_id'=>$this->member_id))->count();

		$jret['flag'] = 1;
		$jret['result'] = $memberinfo;
	    $this->ajaxreturn($jret);
	}

	// 授权登陆
	public function onLogin(){

	}
	
	// 手机验证
	public function checkPhone(){
		// todo
	}

}