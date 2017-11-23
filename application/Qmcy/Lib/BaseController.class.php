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
        $session3rd = I('request.session3rd');
        if (!empty($session3rd) && $session3rd !== 'null') {
            if (S($session3rd)) {
                $member = M('Member')->where(array('openId'=>S($session3rd)))->find();
                if (!empty($member)) {
                    $this->user_result = $member;
                }else{
                    $this->jret['reset']['status'] = 0;
                    $this->jret['msg'] = 'u have to reg!';
                    $this->ajaxReturn($this->jret);
                }
            }else{
                $this->jret['reset']['status'] = 1;
                $this->jret['msg'] = 'session3rd is expire!';
                $this->ajaxReturn($this->jret);
            }
        }

        if($this->user_result){
            $this->jret['islocked'] = $this->user_result['islock'];
        }
    }

}

