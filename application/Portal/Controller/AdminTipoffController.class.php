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
            $m->where(array('id'=>$_POST['id']))->save(array('status'=>0));

            M('Member')->where(array('member_id'=>$_POST['illegal_id']))->setInc('ill_num', 1);
            $ill_num = M('Member')->where(array('member_id'=>$_POST['illegal_id']))->getField('ill_num');
            if ($ill_num >= 3) {
                M('Member')->where(array('member_id'=>$_POST['illegal_id']))->save(array('islock'=>1));
            }
            
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
    
}