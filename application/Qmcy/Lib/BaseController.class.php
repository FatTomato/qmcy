<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Qmcy\Lib;
use Think\Controller;
abstract class BaseController extends Controller {

    protected $ret_type;
    protected $user_result;
    protected $jret;

    /** error related
    */
    public function __call($function,$args){
        $this->jerror("The request API doesn't exist!");
    }
    protected function jerror($msg, $st=0){
        $this->jret['flag'] = $st;
        $this->jret['msg'] = $msg;
        $this->ajaxReturn( $this->jret );
    }
    protected function ajaxReturn($data,$type='',$json_option=0) {
        parent::ajaxReturn($data,$type,$json_option);
    }
    private function initJret(){
        $this->jret = array(
            'flag'  => 0,
            'msg'   => '400 996 9968',
            'islocked' => 0,
            'result'=> array()
        );
    }
    
    protected function setRetType(){
        $this->ret_type = "json";
    }

    public function _initialize(){
        $this->setRetType();
        $this->initJret();
        $this->user_result = null;

        // login status
        $userM = M('Member');
        $user_id = $this->getSessionUserId();
        if ($user_id) {
            $this->user_result = $userM->where(array('user_id'=>$user_id))->find();
        }

        if($this->user_result){
            $this->jret['islocked'] = $this->user_result['islock'];
        }
    }

    protected function getSessionUserId(){
        $session3rd = I('request.session3rd');
        if (isset($_SESSION[$session3rd.'login_endtime']) && $_SESSION[$session3rd.'login_endtime']>time()) {
            $_SESSION[$session3rd.'login_endtime'] = time()+86400*7;
            $user_id = $_SESSION [$session3rd];
        }else {
            $this->jerror("The session3rd is expire!");
        }
       
        return $user_id;
    }

}

