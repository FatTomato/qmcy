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

class AdminIllustrateController extends AdminbaseController {
    
    protected $ill_model;
    
    function _initialize() {
        parent::_initialize();
        $this->ill_model = M('Illustrate');
    }
    
    // 后台文章管理列表
    public function index(){
        $this->ill_list = $this->ill_model->select();
        $this->display();
    }

    // add
    public function add(){
        $this->display();
    }

    // do_add
    public function add_post(){
        if (IS_POST) {
            $article=I("post.post");
            $article['content']=htmlspecialchars_decode($article['content']);
            $result=$this->ill_model->add($article);

            if ($result) {
                $this->success("添加成功！");
            }else {
                $this->error("添加失败！");
            }
             
        }
    }
    
    // 文章编辑
    public function edit(){
        $id=  I("get.id",0,'intval');

        $post=$this->ill_model->where("id=$id")->find();
        
        $this->assign("post",$post);
        $this->display();
    }

    // do_edit
    public function edit_post(){
        if (IS_POST) {
            $article=I("post.post");
            $article['content']=htmlspecialchars_decode($article['content']);
            $result=$this->ill_model->save($article);
            if ($result!==false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
    }

    // del
    public function delete(){
        if(isset($_GET['id'])){
            $id = I("get.id",0,'intval');
            $status=$this->ill_model->where(array("id"=>$id))->delete();
            
            if ($status!==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }
    
}