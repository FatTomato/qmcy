<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;

class PointController extends BaseController {
	
	public function _initialize() {
		parent::_initialize();
	}

	// 更新积分
	public function setPoint($param=[]){
		$action = $param['action'];
		$point = $param['point'];
		$member_id = $param['member_id'];
		$addtime = $param['addtime'];
		$daily_date = $param['daily_date'];
		$daily_m = $param['daily_m'];
		$weekly_date = $param['weekly_date'];
		$weekly_m = $param['weekly_m'];
		
		// detail 直接插入
		$detail_re = M('detail_points')->add(['member_id'=>$member_id, 'addtime'=>$addtime, 'point'=>$point, 'action'=>$action]);

		$daily_re = self::analysis($param=['date'=>$daily_date, 'm'=>$daily_m, 'point'=>$point, 'member_id'=>$member_id]);
		$weekly_re = self::analysis($param=['date'=>$weekly_date, 'm'=>$weekly_m, 'point'=>$point, 'member_id'=>$member_id]);

		// member直接update字段exp
		$total_re = M('member')->where(array('member_id'=>$member_id))->setInc('point',$point);
		
	}

	protected function analysis($param=[]){
		$id = $param['m']->where(['member_id'=>$param['member_id'], 'addtime'=>$param['date']])->getField('id');
		if (isset($id)) {
			$re = $param['m']->where(['member_id'=>$param['member_id'], 'addtime'=>$param['date']])->setInc('point',$param['point']);
		}else{
			$data = ['member_id'=>$param['member_id'], 'addtime'=>$param['date'], 'point'=>$param['point']];
			$re = $param['m']->add($data);
		}
		return $re;
	}

	// 周榜排名
	public function getWeeklySort(){
		$join = '__MEMBER__ b ON a.member_id = b.member_id';
		$where['a.addtime'] = date('Y-m-d 00:00:00',strtotime(date("Y-m-d")." -".(date('w',strtotime(date("Y-m-d"))) ? date('w',strtotime(date("Y-m-d"))) - 1 : 6).' days'));
		$re = M('weekly_points')->alias('a')->join($join)->where($where)->field('a.member_id, a.addtime, a.point, b.userphoto, b.username')->order('a.point desc')->limit('50')->select();

		foreach ($re as &$value) {
			if (mb_strlen($value['username'] > 10)) {
				$value['username'] = mb_substr($value['username'], 0, 10).'...';
			}
		}

		if (isset($re)) {
			$this->jret['flag'] = 1;
			$this->jret['result']['list'] = $re;
			$this->jret['result']['start_time'] = $re['0']['addtime'];
			$this->jret['result']['end_time'] = date('Y-m-d 23:59:59',strtotime($re['0']['addtime']." +6 days"));
	        $this->ajaxReturn($this->jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 日积分详情
	public function getDailyDetail(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$where['member_id'] = $this->user_result['member_id'];
		$where['addtime'] = array('EGT',date('Y-m-d 00:00:00'));
		$field = 'addtime,point,action';
		$re = M('detail_points')->field($field)->where($where)->order('addtime')->select();

		foreach ($re as &$value) {
			switch ($value['action']) {
				case '0':
					$value['action'] = '发布信息';
					break;
				case '1':
					$value['action'] = '被点赞';
					break;
				case '2':
					$value['action'] = '绑定手机';
					break;
				case '3':
					$value['action'] = '上传店铺';
					break;
				case '4':
					$value['action'] = '推荐用户';
					break;
				case '5':
					$value['action'] = '信息违规';
					break;
				case '6':
					$value['action'] = '举报通过审核';
					break;
				case '7':
					$value['action'] = '反馈通过审核';
					break;
			}
		}

		if (isset($re)) {
			$this->jret['flag'] = 1;
			$this->jret['result'] = $re;
	        $this->ajaxReturn($this->jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 周积分详情
	public function getWeeklyDetail(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$where['member_id'] = $this->user_result['member_id'];
		$where['addtime'] = array('EGT',date('Y-m-d 00:00:00',strtotime(date("Y-m-d")." -".(date('w',strtotime(date("Y-m-d"))) ? date('w',strtotime(date("Y-m-d"))) - 1 : 6).' days')));
		$field = 'addtime,point';
		$re = M('daily_points')->field($field)->where($where)->order('addtime')->select();

		foreach ($re as &$value) {
			$value['addtime'] = substr($value['addtime'], 0, 10);
		}

		if (isset($re)) {
			$this->jret['flag'] = 1;
			$this->jret['result'] = $re;
	        $this->ajaxReturn($this->jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 发布便民信息判断积分是否满足
	public function checkPoint(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		
		$point = M('Member')->where(array('member_id'=>$this->user_result['member_id']))->getField('point');

		if($point !== false){
			$this->jret['flag'] = 1;
			$is_enough = $point > 100?true:false;
			$this->jret['result']['point'] = $point;
			$this->jret['result']['is_enough'] = $is_enough;
	        $this->ajaxReturn($this->jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 获取总积分、今日积分、本周积分
	public function getPointsOverview(){
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$this->jret['flag'] = 1;

		$this->jret['result']['total_point'] = M('Member')->where(array('member_id'=>$this->user_result['member_id']))->getField('point');

		$where1['member_id'] = $this->user_result['member_id'];
		$where1['addtime'] = array('EGT',date('Y-m-d 00:00:00'));
		$this->jret['result']['daily_point'] = M('detail_points')->where($where1)->sum('point');

		$where2['member_id'] = $this->user_result['member_id'];
		$where2['addtime'] = array('EGT',date('Y-m-d'));
		$this->jret['result']['weekly_point'] = M('daily_points')->where($where2)->sum('point');
		
		$this->ajaxReturn($this->jret);
	}
}