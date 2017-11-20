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
		$total_re = M('member')->where(array('user_id'=>$member_id))->setInc('point',$point);
		
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
		$join = '__MEMBER__ b ON a.member_id = b.user_id';
		$where['a.addtime'] = date('Y-m-d 00:00:00',strtotime(date("Y-m-d")." -".(date('w',strtotime(date("Y-m-d"))) ? date('w',strtotime(date("Y-m-d"))) - 1 : 6).' days'));
		$re = M('weekly_points')->alias('a')->join($join)->where($where)->field('a.member_id, a.addtime, a.point, b.userphoto, b.username')->order('a.point desc')->limit('50')->select();

		if (isset($re)) {
			$jret['flag'] = 1;
			$jret['result']['list'] = $re;
			$jret['result']['start_time'] = $re['0']['addtime'];
			$jret['result']['end_time'] = date('Y-m-d 23:59:59',strtotime($re['0']['addtime']." +6 days"));
	        $this->ajaxreturn($jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 日积分详情
	public function getDailyDetail(){
		if (empty($this->user_result['user_id'])) {
			$this->jerror('u have to auth!');
		}
		$where['member_id'] = $this->user_result['user_id'];
		$where['addtime'] = array('EGT',date('Y-m-d 00:00:00'));
		$field = 'addtime,point,action';
		$re = M('detail_points')->field($field)->where($where)->order('addtime')->select();

		foreach ($re as &$value) {
			switch ($value['action']) {
				case '0':
					$value['action'] = '发布圈子';
					break;
				case '1':
					$value['action'] = '被点赞';
					break;
				case '2':
					$value['action'] = '发表评论';
					break;
				case '3':
					$value['action'] = '制作名片';
					break;
				case '4':
					$value['action'] = '发布便民';
					break;
				case '5':
					$value['action'] = '信息违规';
					break;
				case '6':
					$value['action'] = '最新活动';
					break;
				case '7':
					$value['action'] = '拉新';
					break;
			}
		}

		if (isset($re)) {
			$jret['flag'] = 1;
			$jret['result'] = $re;
	        $this->ajaxreturn($jret);
	    }else {
			$this->jerror("查询失败");
		}
	}

	// 周积分详情
	public function getWeeklyDetail(){
		if (empty($this->user_result['user_id'])) {
			$this->jerror('u have to auth!');
		}
		$where['member_id'] = $this->user_result['user_id'];
		$where['addtime'] = array('EGT',date('Y-m-d'));
		$field = 'addtime,point';
		$re = M('daily_points')->field($field)->where($where)->order('addtime')->select();

		if (isset($re)) {
			$jret['flag'] = 1;
			$jret['result'] = $re;
	        $this->ajaxreturn($jret);
	    }else {
			$this->jerror("查询失败");
		}
	}
}