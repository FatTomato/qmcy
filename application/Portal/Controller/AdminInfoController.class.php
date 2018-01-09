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

class AdminInfoController extends AdminbaseController {
    
    protected $infos_model;
    protected $categorys_model;
    
    function _initialize() {
        parent::_initialize();
        $this->infos_model = D("Portal/Infos");
        $this->categorys_model = D("Portal/Categorys");
    }
    
    // 后台文章管理列表
    public function index(){
        $this->_lists();
        $this->cg_id = $this->categorys_model->where(array('type'=>array('IN',[0,1])))->order(array("listorder"=>"asc"))->getField('cg_id,name');
        $this->display();
    }
    
    // 文章编辑
    public function edit(){
        $id=  I("get.id",0,'intval');
        $this->cg_id = $this->categorys_model->where(array('type'=>array('IN',[0,1])))->order(array("listorder"=>"asc"))->getField('cg_id,name');
        $post=$this->infos_model->where("id=$id")->find();
        $comments = M('info_comments')->where(array('post_id'=>$id))->order('createtime asc')->select();
        $this->assign("post",$post);
        $this->assign("comments",$comments);
        $this->assign("smeta",json_decode($post['smeta'],true));
        $this->display();
    }
    
    // 文章排序
    public function listorders() {
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['listorder'] = $r;
            $this->infos_model->where(array('id' => $key))->save($data);
        }
        
        $this->success("排序更新成功！");
        
    }
    
    /**
     * 文章列表处理方法,根据不同条件显示不同的列表
     * @param array $where 查询条件
     */
    private function _lists($where=array()){
        $cg_id=I('request.cg_id',0,'intval');
        
        if(!empty($cg_id)){
            $where['a.cg_id']=$cg_id;
        }

        $stars=I('request.stars');
        $m_id=I('request.m_id');
        if(!empty($stars) && !empty($m_id)){
            $where['stars']=array('like',"%$m_id%");
        }

        $post=I('request.post');
        if(!empty($post) && !empty($m_id)){
            $where['a.post_author']=$m_id;
        }
        
        $start_time=I('request.start_time');
        if(!empty($start_time)){
            $where['a.post_date']=array(
                array('EGT',$start_time)
            );
        }
        
        $end_time=I('request.end_time');
        if(!empty($end_time)){
            if(empty($where['a.post_date'])){
                $where['a.post_date']=array();
            }
            array_push($where['a.post_date'], array('ELT',$end_time));
        }
        
        $keyword=I('request.keyword');
        if(!empty($keyword)){
            $where['a.post_content']=array('like',"%$keyword%");
        }
            
        $this->infos_model
        ->alias("a")
        ->where($where);
        $count=$this->infos_model->count();
        $page = $this->page($count, 20);
            
        $this->infos_model
        ->alias("a")
        ->join("__MEMBER__ c ON a.post_author = c.member_id")
        ->where($where)
        ->limit($page->firstRow , $page->listRows)
        ->order("a.post_date DESC");
        $this->infos_model->field('a.*,c.member_id,c.username');
        $posts=$this->infos_model->select();
        foreach($posts as &$v){
            $v['post_likes'] = !empty($v['post_like'])? count(explode(',', $v['post_like'])): 0;
        }
        
        $this->assign("page", $page->show('Admin'));
        $this->assign("formget",array_merge($_GET,$_POST));
        $this->assign("posts",$posts);
    }
    
    // 文章置顶
    public function top(){
        if(isset($_POST['ids']) && $_GET["top"]){
            $ids = I('post.ids/a');
            
            if ( $this->infos_model->where(array('id'=>array('in',$ids)))->save(array('istop'=>1))!==false) {
                $this->success("置顶成功！");
            } else {
                $this->error("置顶失败！");
            }
        }
        if(isset($_POST['ids']) && $_GET["untop"]){
            $ids = I('post.ids/a');
            
            if ( $this->infos_model->where(array('id'=>array('in',$ids)))->save(array('istop'=>0))!==false) {
                $this->success("取消置顶成功！");
            } else {
                $this->error("取消置顶失败！");
            }
        }
    }

    // 文章审核
    public function check(){
        if(isset($_POST['ids']) && $_GET["check"]){
            $ids = I('post.ids/a');
            // todo  point
            
            if ( $this->infos_model->where(array('id'=>array('in',$ids)))->save(array('status'=>0)) !== false ) {
                $this->success("设置违规成功！");
            } else {
                $this->error("设置违规失败！");
            }
        }
        if(isset($_POST['ids']) && $_GET["uncheck"]){
            $ids = I('post.ids/a');
            
            if ( $this->infos_model->where(array('id'=>array('in',$ids)))->save(array('status'=>1)) !== false) {
                $this->success("取消设置违规成功！");
            } else {
                $this->error("取消设置违规失败！");
            }
        }
    }

    // 评论审核
    public function check_comments(){
        if(isset($_POST['ids']) && $_GET["check"]){
            $ids = I('post.ids/a');
            
            if ( M('info_comments')->where(array('id'=>array('in',$ids)))->save(array('status'=>0)) !== false ) {
                $this->success("设置违规成功！");
                // $this->success("设置违规成功！",U("AdminInfo/edit?id=".$_POST['type'].""));
            } else {
                $this->error("设置违规失败！");
                // $this->error("设置违规失败！",U("AdminInfo/edit?id=".$_POST['type'].""));
            }
        }
        if(isset($_POST['ids']) && $_GET["uncheck"]){
            $ids = I('post.ids/a');
            
            if ( M('info_comments')->where(array('id'=>array('in',$ids)))->save(array('status'=>1)) !== false) {
                $this->success("取消设置违规成功！");
                // $this->success("取消设置违规成功！",U("AdminInfo/edit?id=".$_POST['type'].""));
            } else {
                $this->error("取消设置违规失败！");
                // $this->error("取消设置违规失败！",U("AdminInfo/edit?id=".$_POST['type'].""));
            }
        }
    }
    
    // 清除已经删除的文章
    public function clean(){
        if(isset($_POST['ids'])){
            $ids = I('post.ids/a');
            $ids = array_map('intval', $ids);
            $status=$this->infos_model->where(array("id"=>array('in',$ids)))->delete();
            
            if ($status!==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }else{
            if(isset($_GET['id'])){
                $id = I("get.id",0,'intval');
                $status=$this->infos_model->where(array("id"=>$id))->delete();
                
                if ($status!==false) {
                    $this->success("删除成功！");
                } else {
                    $this->error("删除失败！");
                }
            }
        }
    }
    
}