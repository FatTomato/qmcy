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

class AdminFeedbackController extends AdminbaseController {
    
    protected $fb_model;
    
    function _initialize() {
        parent::_initialize();
        $this->fb_model = M('Feedback');
    }
    
    // 后台文章管理列表
    public function index(){
        $fb_list = $this->fb_model->order('addtime desc')->select();
        foreach ($fb_list as &$value) {
           $value['info'] = mb_substr($value['info'], 0, 60);
        }
        $this->fb_list = $fb_list;
        $this->display();
    }
    
    // 文章编辑
    public function edit(){
        $id=  I("get.id",0,'intval');

        $post=$this->fb_model->where("id=$id")->find();
        
        $this->assign("post",$post);
        $this->display();
    }

    // check
    public function check(){
        if(isset($_POST['ids']) && $_GET["check"]){
            $ids = I('post.ids/a');

            if ( $this->fb_model->where(array('id'=>array('in',$ids)))->save(array('status'=>1)) !== false ) {
                $this->success("审核通过成功！");
            } else {
                $this->error("审核通过失败！");
            }
        }
        if(isset($_POST['ids']) && $_GET["uncheck"]){
            $ids = I('post.ids/a');
            
            if ( $this->fb_model->where(array('id'=>array('in',$ids)))->save(array('status'=>2)) !== false) {
                $this->success("审核不通过成功！");
            } else {
                $this->error("审核不通过失败！");
            }
        }
    }
    
}