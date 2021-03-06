<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;

class FeedbackController extends BaseController {
	
	protected $ad_m;
	
	public function _initialize() {
		parent::_initialize();
		$this->fb_m = M('Feedback');
	}

	// 反馈
	public function addFeedback(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$info = I('request.info');
		if (empty($info)) {
			$this->jerror('参数缺失');
		}
		if ( mb_strlen($info)<10 ) {
			$this->jerror('最少发布10个字哦！');
		}
		if ( mb_strlen($info)>200 ) {
			$this->jerror('最多发布200个字哦！');
		}
		$fb = $this->fb_m->where(array('member_id'=>$this->user_result['member_id']))->order('addtime desc')->find();
		if ( (time()-strtotime($fb['addtime'])) < 86400 ) {
			$this->jerror('反馈过于频繁，请稍后再试！');
		}
		$fb_data['member_id'] = $this->user_result['member_id'];
		$fb_data['info'] = $info;
		$fb_data['addtime'] = date('Y-m-d H:i:s');
		$re = $this->fb_m->add($fb_data);
		if ($re) {
			$this->jret['flag'] = 1;
			$this->ajaxReturn($this->jret);
		} else {
			$this->jerror('反馈失败');
		}
	}
}