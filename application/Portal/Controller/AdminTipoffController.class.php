<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;

use Common\Controller\AdminbaseController;

class AdminTipoffController extends AdminbaseController {
    
    protected $to_model;
    
    function _initialize() {
        parent::_initialize();
        $this->to_model = M('Tipoff');
    }
    
    // 后台文章管理列表
    public function index(){
        $type=I('request.type',0,'intval');
        if(!empty($type)){
            $where['type']=$type==1?'info':'shop';
        }
        $check=I('request.check',0,'intval');
        if(!empty($check)){
            $where['status']=$check-1;
        }
            
        $this->to_model->where($where);
        
        $count=$this->to_model->count();
            
        $page = $this->page($count, 20);
            
        $this->to_model
        ->where($where)
        ->limit($page->firstRow , $page->listRows)
        ->order("addtime desc");
        
        $this->to_model->field('*');
        
        $to_list=$this->to_model->order("addtime desc")->select();
        foreach ($to_list as &$value) {
           $value['content'] = mb_substr($value['content'], 0, 60);
        }

        $this->assign("page", $page->show('Admin'));
        $this->assign("formget",array_merge($_GET,$_POST));
        $this->assign("to_list",$to_list);

        $this->type = [1=>'信息',2=>'店铺'];
        $this->check = [1=>'未审核',2=>'已通过',3=>'未通过'];
        $this->display();
    }
    
    // 文章编辑
    public function edit(){
        $tid=  I("get.tid",0,'intval');

        $to=$this->to_model->where("tid=$tid")->find();
        
        $this->assign("to",$to);
        $this->display();
    }

    // check
    public function check(){
        $m = $_POST['type']=='info'? M('Infos'): M('Shop');

        if ($_POST['check'] == 1) {
            // 先下架店铺/信息
            $m->where(array('id'=>$_POST['id']))->save(array('status'=>0));
            // 扣分
            $point = [];
            $point['action'] = '5';
            $point['point'] = '-1000';
            $point['member_id'] = $_POST['illegal_id'];
            $point['addtime'] = date('Y-m-d H:i:s');
            $point['daily_date'] = date('Y-m-d 00:00:00');
            $point['daily_m'] = M('daily_points');
            $point['weekly_date'] = date('Y-m-d 00:00:00',strtotime(date("Y-m-d")." -".(date('w',strtotime(date("Y-m-d"))) ? date('w',strtotime(date("Y-m-d"))) - 1 : 6).' days'));
            $point['weekly_m'] = M('weekly_points');
            $this->setPoint($point);
            // 违规次数+1
            M('Member')->where(array('member_id'=>$_POST['illegal_id']))->setInc('ill_num', 1);
            $ill_num = M('Member')->where(array('member_id'=>$_POST['illegal_id']))->getField('ill_num');
            if ($ill_num >= 3) {
                // 违规次数达3次 封号
                M('Member')->where(array('member_id'=>$_POST['illegal_id']))->save(array('islock'=>1));
            }
            // 加分
            $point = [];
            $point['action'] = '6';
            $point['point'] = '100';
            $point['member_id'] = $_POST['member_id'];
            $point['addtime'] = date('Y-m-d H:i:s');
            $point['daily_date'] = date('Y-m-d 00:00:00');
            $point['daily_m'] = M('daily_points');
            $point['weekly_date'] = date('Y-m-d 00:00:00',strtotime(date("Y-m-d")." -".(date('w',strtotime(date("Y-m-d"))) ? date('w',strtotime(date("Y-m-d"))) - 1 : 6).' days'));
            $point['weekly_m'] = M('weekly_points');
            $this->setPoint($point);
            
            $re = $this->to_model->where(array('tid'=>$_POST['tid']))->save(array('status'=>1));
        } elseif ($_POST['check'] == 2) {
            $re = $this->to_model->where(array('tid'=>$_POST['tid']))->save(array('status'=>2));
        }

        if ( $re !== false) {
            $this->success("审核成功！");
        } else {
            $this->error("审核失败！");
        }
    }

    // 更新积分
    protected function setPoint($param=[]){
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
    
}