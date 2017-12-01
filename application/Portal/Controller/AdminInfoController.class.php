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
    protected $infos_relationships_model;
    protected $categorys_model;
    
    function _initialize() {
        parent::_initialize();
        $this->infos_model = D("Portal/Infos");
        $this->categorys_model = D("Portal/Categorys");
        $this->infos_relationships_model = D("Portal/InfosRelationships");
    }
    
    // 后台文章管理列表
    public function index(){
        $this->_lists();
        $this->_getTree();
        $this->display();
    }
    
    // 文章编辑
    public function edit(){
        $id=  I("get.id",0,'intval');
        
        $infos_relationships_model = M('InfosRelationships')->where(array("object_id"=>$id,"status"=>1))->getField("cg_id",true);
        $this->_getTermTree($infos_relationships_model);
        $post=$this->infos_model->where("id=$id")->find();
        $comments = M('info_comments')->where(array('post_id'=>$id))->order('createtime asc')->select();
        $this->assign("post",$post);
        $this->assign("comments",$comments);
        $this->assign("smeta",json_decode($post['smeta'],true));
        $this->display();
    }
    
    // 文章排序
    public function listorders() {
        $status = parent::_listorders($this->infos_relationships_model);
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }
    
    /**
     * 文章列表处理方法,根据不同条件显示不同的列表
     * @param array $where 查询条件
     */
    private function _lists($where=array()){
        $cg_id=I('request.cg_id',0,'intval');
        
        if(!empty($cg_id)){
            $where['b.cg_id']=$cg_id;
        }

        $stars=I('request.stars');
        $m_id=I('request.m_id');
        if(!empty($stars) && !empty($m_id)){
            $where['stars']=array('like',"%$m_id%");
        }

        $post=I('request.post');
        if(!empty($post) && !empty($m_id)){
            $where['post_author']=$m_id;
        }
        
        $start_time=I('request.start_time');
        if(!empty($start_time)){
            $where['post_date']=array(
                array('EGT',$start_time)
            );
        }
        
        $end_time=I('request.end_time');
        if(!empty($end_time)){
            if(empty($where['post_date'])){
                $where['post_date']=array();
            }
            array_push($where['post_date'], array('ELT',$end_time));
        }
        
        $keyword=I('request.keyword');
        if(!empty($keyword)){
            $where['post_content']=array('like',"%$keyword%");
        }
            
        $this->infos_model
        ->alias("a")
        ->where($where);
        
        $this->infos_model->join("__INFOS_RELATIONSHIPS__ b ON a.id = b.object_id");
        
        $count=$this->infos_model->count();
            
        $page = $this->page($count, 20);
            
        $this->infos_model
        ->alias("a")
        ->join("__MEMBER__ c ON a.post_author = c.member_id")
        ->where($where)
        ->limit($page->firstRow , $page->listRows)
        ->order("a.post_date DESC");
        $this->infos_model->field('a.*,c.member_id,c.username,b.listorder,b.cg_id,b.cg_name,b.infosid,b.status');
        $this->infos_model->join("__INFOS_RELATIONSHIPS__ b ON a.id = b.object_id");
        $posts=$this->infos_model->select();
        foreach($posts as &$v){
            $v['post_likes'] = !empty($v['post_like'])? count(explode(',', $v['post_like'])): 0;
        }
        
        $this->assign("page", $page->show('Admin'));
        $this->assign("formget",array_merge($_GET,$_POST));
        $this->assign("posts",$posts);
    }
    
    // 获取文章分类树结构 select 形式
    private function _getTree(){
        $cg_id=empty($_REQUEST['cg_id'])?0:intval($_REQUEST['cg_id']);
        $result = $this->categorys_model->order(array("listorder"=>"asc"))->select();
        
        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $r) {
            $r['str_manage'] = '<a href="' . U("AdminTerm/add", array("parent" => $r['cg_id'])) . '">添加子类</a> | <a href="' . U("AdminTerm/edit", array("id" => $r['cg_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminTerm/delete", array("id" => $r['cg_id'])) . '">删除</a> ';
            $r['visit'] = "<a href='#'>访问</a>";
            $r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
            $r['id']=$r['cg_id'];
            $r['parentid']=$r['parent'];
            $r['selected']=$cg_id==$r['cg_id']?"selected":"";
            $array[] = $r;
        }
        
        $tree->init($array);
        $str="<option value='\$id' \$selected>\$spacer\$name</option>";
        $taxonomys = $tree->get_tree(0, $str);
        $this->assign("taxonomys", $taxonomys);
    }
    
    // 获取文章分类树结构 
    private function _getTermTree($term=array()){
        $result = $this->categorys_model->order(array("listorder"=>"asc"))->select();
        
        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $r) {
            $r['str_manage'] = '<a href="' . U("AdminTerm/add", array("parent" => $r['cg_id'])) . '">添加子类</a> | <a href="' . U("AdminTerm/edit", array("id" => $r['cg_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminTerm/delete", array("id" => $r['cg_id'])) . '">删除</a> ';
            $r['visit'] = "<a href='#'>访问</a>";
            $r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
            $r['id']=$r['cg_id'];
            $r['parentid']=$r['parent'];
            $r['selected']=in_array($r['cg_id'], $term)?"selected":"";
            $r['checked'] =in_array($r['cg_id'], $term)?"checked":"";
            $array[] = $r;
        }
        
        $tree->init($array);
        $str="<option value='\$id' \$selected>\$spacer\$name</option>";
        $taxonomys = $tree->get_tree(0, $str);
        $this->assign("taxonomys", $taxonomys);
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
            
            if ( $this->infos_relationships_model->where(array('object_id'=>array('in',$ids)))->save(array('status'=>0)) !== false ) {
                $this->success("设置违规成功！");
            } else {
                $this->error("设置违规失败！");
            }
        }
        if(isset($_POST['ids']) && $_GET["uncheck"]){
            $ids = I('post.ids/a');
            
            if ( $this->infos_relationships_model->where(array('object_id'=>array('in',$ids)))->save(array('status'=>1)) !== false) {
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
            $this->infos_relationships_model->where(array('object_id'=>array('in',$ids)))->delete();
            
            if ($status!==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }else{
            if(isset($_GET['id'])){
                $id = I("get.id",0,'intval');
                $status=$this->infos_model->where(array("id"=>$id))->delete();
                $this->infos_relationships_model->where(array('object_id'=>$id))->delete();
                
                if ($status!==false) {
                    $this->success("删除成功！");
                } else {
                    $this->error("删除失败！");
                }
            }
        }
    }
    
}