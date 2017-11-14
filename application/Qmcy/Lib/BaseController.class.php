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
class BaseController extends Controller {

    protected $jret;
    protected $base_params;
    protected $ret_type;
    protected $user_result;

    public function __call($function,$args){
        $this->jerror("The request API doesn't exist!");
    }
    
    protected function jerror($msg, $st=0){
        $this->jret['flag'] = $st;
        // $this->jret['loginstatus'] = empty($this->user_result)?-4:0;
        $this->jret['msg'] = $msg;
        // if(C('LOG_LEVEL')>=4){
        //     logging( CONTROLLER_NAME."/".ACTION_NAME.json_encode($this->jret), 'PATH_LOG_ERROR');
        // }
        $this->ajaxReturn( $this->jret );
    }

    protected function ajaxReturn($data,$type='',$json_option=0) {
        // if($this->user_result){
        //     $data['loginstatus'] = 1;
        // }
        parent::ajaxReturn($data,$type,$json_option);
    }

    private function initJret(){
        $this->jret = array(
            'flag'  => 0,
            'msg'   => '400 996 9968',
            'islocked' => 0,
            'loginstatus' => 0,
            'total' => 0,
            'result'=> array(),
            'results'=> array()
        );
    }
    
    protected function setRetType(){
        $this->ret_type = "json";
    }

    /** login related
    */
    // web&wap vs. app(client)
    public function _initialize(){
        $this->setRetType();
        $this->initJret();
        $this->user_result = null;

        // device
        $vars = array('apptoken', 'devid', 'devicetype', 'sysversion', 'systype','appversion','devicetoken');
        $this->base_params = array('devicetype'=>0);
        foreach($vars as $k ){
            if( isset( $_REQUEST[$k] ) ){
                $this->base_params[$k] = I($k);
            }
        }
        if( $this->base_params['devicetype'] ){
            $this->base_params['devicetype'] = intval( $this->base_params['devicetype'] );
        }

        $this->jret['result']['prepage'] = "";
        if(isset($_SESSION['prepage']) && $_SESSION['prepage']!="" && $_SESSION['prepage']!="/null" ){
            $this->jret['result']['prepage'] = $_SESSION['prepage'];
        }

        // login status
        $userM = D('Member');
        $user_id = $this->getSessionUserId();
        if( $this->base_params['devicetype'] == 0 ){
            $this->loginByWeb($user_id);
            //是否手机端打开网页
            if(!isMobile()){
               //C('DEFAULT_THEME','web');
            }
        }elseif( $this->base_params['devicetype'] == 1||$this->base_params['devicetype'] == 2){
            $this->loginByApp();
        }else{
            $this->jerror("device not support");
        }
        if($this->user_result) $this->assign('loginstatus', true);

        $domain = parse_domain("http://".$_SERVER['HTTP_HOST']);
        //根据SESSION重新设置COOKIE
        if($this->user_result){
            $returnUrl = I('ReturnUrl');
            $retHost = parse_host($returnUrl);
            $retDomain = parse_domain($returnUrl);
            if($retDomain == $retHost){
                $retHost = "www.".$retHost;
            }
            if(CONTROLLER_NAME == 'Index' && (ACTION_NAME == 'login' || ACTION_NAME == 'reg')){
                $ctime = 0;
                $uname = $this->user_result['phone'];
                $mSite = M('site');
                $site = $mSite->where(array('host'=>$retHost))->find();
                setcookie ( $retHost."u", $uname, $ctime ,"/", ".$domain");
                $currtime = time();
                setcookie ( $retHost."t", $currtime, $ctime ,"/", ".$domain"); 
                // ticket=MD5(sitename+uname+timestamp+key)
                $src = strtoupper(urlencode($site['name']) . $uname.$currtime.$site['key']);
                $ticket = strtoupper(md5($src));
                setcookie ( $retHost."s", $ticket, $ctime ,"/", ".$domain");
                //跳转
                echo "<script>document.location.href='http://".$retHost."'</script>";
                exit;
            }
        }
        $this->checkPrivilege();

        // TODO: ip,acccess control

        if($this->user_result){
            $this->jret['islocked'] = $this->user_result['islock'];
        }
        setcookie ( "PHPSESSID", session_id(), time()+86400 ,"/", ".$domain");
    }

}

