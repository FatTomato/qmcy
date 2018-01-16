<?php
namespace Qmcy\Controller;

use Qmcy\Lib\BaseController;

class PaymentController extends BaseController {

	protected $appid;
	protected $mch_id;
	protected $key;
	protected $out_trade_no;
	protected $body;
	protected $total_fee;
	protected $SSLCERT_PATH = SITE_PATH.'/cert/apiclient_cert.pem';//证书路径
    protected $SSLKEY_PATH =  SITE_PATH.'/cert/apiclient_key.pem';//私钥存放路径
    // protected \$opUserId = '1234567899';//商户号

	public function _initialize() {
		parent::_initialize();
		$this->appid = C('APPID');
		$this->mch_id = C('MCH_ID');
		$this->key = C('API_KEY');
		
	}

	// 支付接口
	public function pay() {
		if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$body = I('request.body');
		$total_fee = I('request.total_fee');
		if (empty($body) || empty($total_fee)) {
			$this->jerror('参数缺失');
		}
		// 保证金控制频次
		if ($body == '息壤小镇-保证金') {
			$where['member_id'] = $this->user_result['member_id'];
			$where['body'] = '息壤小镇-保证金';
			$where['order_status'] = 'ok';
        	$addtime = M('Wxorder')->where($where)->order('addtime desc')->getField('addtime');
        	if ( (time()-strtotime($addtime))<86400 ) {
        		$this->jerror('不可频繁缴纳！');
        	}
		}

		$this->out_trade_no = date('YmdHis').$this->createNoncestr(7);//$out_trade_no;

		$this->body = I('request.body');//$body;
		$this->total_fee = I('request.total_fee');//$total_fee;

		// todo 自定义商户订单入库
		$data = [];
		$data['member_id'] = $this->user_result['member_id'];
		$data['trade_no'] = $this->out_trade_no;
		$data['total_fee'] = $this->total_fee;
		$data['body'] = $this->body;
		$data['addtime'] = date('Y-m-d H:i:s');
		M('Wxorder')->add($data);
		//统一下单接口
		$return = $this->weixinapp();
		if ($return) {
			$this->jret['flag'] = 1;
			$this->jret['result'] = $return;
			$this->ajaxReturn($this->jret);
		}
		
	}

	//统一下单接口 
	private function unifiedorder() {
		$url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
		$parameters = array(
			'appid' => $this->appid,//小程序ID
			'mch_id' => $this->mch_id,//商户号
			'nonce_str' => $this->createNoncestr(),//随机字符串
			'body' => $this->body,
			'out_trade_no'=> $this->out_trade_no,//商户订单号
			'total_fee' => $this->total_fee,
			'spbill_create_ip' => '123.206.198.92',//终端IP
			'notify_url' => 'https://www.qmjoin.com/Qmcy/Payment/notify',//通知地址 确保外网能正常访问
			'openid' => $this->user_result['openid'],//用户id
			'trade_type' => 'JSAPI'//交易类型
		);
		//统一下单签名
		$parameters['sign'] = $this->getSign($parameters);
		$xmlData = $this->arrayToXml($parameters);
		$return = $this->xmlToArray($this->postXmlCurl($xmlData, $url, 60));
		return $return;
	}

	private static function postXmlCurl($xml, $url, $second = 30){
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验 
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 40);
		set_time_limit(0);

		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if ($data) {
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			throw new WxPayException("curl出错，错误码:$error");
		}
	}

	//数组转换成xml 
	private function arrayToXml($arr) {
		$xml = "<root>";
		foreach ($arr as $key => $val) {
			if (is_array($val)) {
				$xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";
			} else {
				$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
			}
		}
		$xml .= "</root>";
		return $xml;
	}

	//xml转换成数组 
	private function xmlToArray($xml) {
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);

		$xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

		$val = json_decode(json_encode($xmlstring), true);

		return $val;
	}

	//微信小程序接口 
	private function weixinapp() {
		//统一下单接口
		$unifiedorder = $this->unifiedorder();
		if($unifiedorder['return_code'] == 'SUCCESS' && $unifiedorder['result_code'] == 'SUCCESS'){
			$parameters = array(
				'appId' => $this->appid, //小程序ID
				'timeStamp' => '' . time() . '', //时间戳
				'nonceStr' => $this->createNoncestr(), //随机串
				'package' => 'prepay_id=' . $unifiedorder['prepay_id'], //数据包
				'signType' => 'MD5'//签名方式
			);
			//签名 
			$parameters['paySign'] = $this->getSign($parameters);
			
		}else{
			$parameters['state'] = 0;
            $parameters['text'] = "错误";
            $parameters['return_code'] = $unifiedorder['return_code'];
            $parameters['return_msg'] = $unifiedorder['return_msg'];
		}
		return $parameters;
	}

	//作用：产生随机字符串，不长于32位 
	private function createNoncestr($length = 32) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str; 
	}

	//作用：生成签名 
	private function getSign($Obj) {
		foreach ($Obj as $k => $v) {
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//签名步骤二：在string后加入KEY
		$String = $String . "&key=" . $this->key;
		//签名步骤三：MD5加密
		$String = md5($String);
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		return $result_;
	}

	///作用：格式化参数，签名过程需要使用 
	private function formatBizQueryParaMap($paraMap, $urlencode) {
		$buff = '';
		ksort($paraMap);
		foreach ($paraMap as $k => $v) {
			if ($urlencode) {
				$v = urlencode($v);
			}
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar = '';
		if (strlen($buff) > 0) {
			$reqPar = substr($buff, 0, strlen($buff) - 1);
		} 
		return $reqPar;
	}

	private function post_data(){
		$receipt = $_REQUEST;
		if($receipt==null){
			$receipt = file_get_contents("php://input");
			if($receipt == null){
				$receipt = $GLOBALS['HTTP_RAW_POST_DATA'];
			}
		}
		return $receipt;
	}

	public function notify(){
		$post = $this->post_data();    //接受POST数据XML个数

        $post_data = $this->xmlToArray($post);   //微信支付成功，返回回调地址url的数据：XML转数组Array

        $postSign = $post_data['sign'];
        unset($post_data['sign']);
        
        /* 微信官方提醒：
         *  商户系统对于支付结果通知的内容一定要做【签名验证】,
         *  并校验返回的【订单金额是否与商户侧的订单金额】一致，
         *  防止数据泄漏导致出现“假通知”，造成资金损失。
         */
        ksort($post_data);// 对数据进行排序
        $user_sign = $this->getSign($post_data);//$this->ToUrlParams($post_data);//再次生成签名，与$postSign比较

        $where['trade_no'] = $post_data['out_trade_no'];
        $order_status = M('Wxorder')->where($where)->find();
        
        if( ($post_data['return_code']=='SUCCESS') && ($postSign==$user_sign) && ($post_data['result_code']=='SUCCESS') ){
            /*
            * 首先判断，订单是否已经更新为ok，因为微信会总共发送8次回调确认
            * 其次，订单已经为ok的，直接返回SUCCESS
            * 最后，订单没有为ok的，更新状态为ok，返回SUCCESS
            */
            if($order_status['order_status']=='ok'){
                $this->return_success();
            }else{
                $updata['order_status'] = 'ok';
                if(M('Wxorder')->where($where)->save($updata)){
                	// 业务逻辑
                	if ($order_status['body'] == '息壤小镇-保证金' && ($post_data['total_fee'] == '19800' || $post_data['total_fee'] == '1') ) {
                		$save_data['deposit'] = 1;
                		$save_data['deal_time'] = date('Y-m-d H:i:s');
                		M('Shop')->where(array('member_id'=>$order_status['member_id']))->save($save_data);
                	} elseif ($order_status['body'] == '息壤小镇-店铺升级') {
                		switch ($post_data['total_fee']) {
                			case '16800':
                				$vip_time = date("Y-m-d H:i:s",strtotime("+3 month"));
                				break;
                			case '25800':
                				$vip_time = date("Y-m-d H:i:s",strtotime("+6 month"));
                				break;
                			case '36500':
                				$vip_time = date("Y-m-d H:i:s",strtotime("+1 year"));
                				break;
                			case '1':
                				$vip_time = date("Y-m-d H:i:s",strtotime("+1 week"));
                				break;
                		}
                		$save_data['vip_time'] = $vip_time;
                		$save_data['deal_time'] = date('Y-m-d H:i:s');
						$save_data['level'] = 1;
						$save_data['vip_type'] = 1;
                		M('Shop')->where(array('member_id'=>$order_status['member_id']))->save($save_data);
                	}
                    $this->return_success();
                }
            }
        }else{
            echo '微信支付失败';
        }  
	}

	private function return_success(){
        $return['return_code'] = 'SUCCESS';
        $return['return_msg'] = 'OK';
        $xml_post = '<xml>
                    <return_code>'.$return['return_code'].'</return_code>
                    <return_msg>'.$return['return_msg'].'</return_msg>
                    </xml>';
        echo $xml_post;exit;
    }

	// 退款
	public function refund(){
	    //对外暴露的退款接口
	    if (empty($this->user_result['member_id'])) {
			$this->jerror('您还没有登录！');
		}
		$this->openid = $this->user_result['openid'];
		$deposit = M('Shop')->where(array('member_id'=>$this->user_result['member_id']))->getField('deposit');
		if ($deposit == 1) {
			$order_status = M('Wxorder')->where(array('member_id'=>$this->user_result['member_id'], 'body'=>'息壤小镇-保证金', 'order_status'=>'ok'))->order('addtime desc')->find();
			if ($order_status) {
				$this->outTradeNo = $order_status['trade_no'];
			    $this->totalFee = $order_status['total_fee'];
			    $this->outRefundNo = date('YmdHis').$this->createNoncestr(7);//退款单号
			    $this->refundFee = $order_status['total_fee'];
			    $data = [];
				$data['member_id'] = $this->user_result['member_id'];
				$data['trade_no'] = $this->outRefundNo;
				$data['total_fee'] = $this->order_status['total_fee'];
				$data['body'] = '息壤小镇-保证金退款';
				$data['addtime'] = date('Y-m-d H:i:s');
				M('Wxorder')->add($data);
			    $result = $this->wxrefundapi();
			    if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
			    	M('Wxorder')->where(array('trade_no'=>$result['out_refund_no']))->save(array('order_status'=>'ok'));
			    	M('Shop')->where(array('member_id'=>$this->user_result['member_id']))->save(array('deposit'=>0));
			    	$this->jret['flag'] = 1;
			    	$this->ajaxReturn($this->jret);
			    } else {
			    	$this->jerror($result['return_msg']);
			    }
			} else {
				$this->jerror('订单有误！');
			}
		} else {
			$this->jerror('还未缴纳保证金！');
		}
	}

	private function wxrefundapi(){
	    //通过微信api进行退款流程
	    $parma = array(
	        'appid'=> $this->appid,
	        'mch_id'=> $this->mch_id,
	        'nonce_str'=> $this->createNoncestr(),
	        'out_refund_no'=> $this->outRefundNo,
	        'out_trade_no'=> $this->outTradeNo,
	        'total_fee'=> $this->totalFee,
	        'refund_fee'=> $this->refundFee,
	        // 'op_user_id' => $this->opUserId,
	    );
	    $parma['sign'] = $this->getSign($parma);
	    $xmldata = $this->arrayToXml($parma);
	    $xmlresult = $this->postXmlSSLCurl($xmldata,'https://api.mch.weixin.qq.com/secapi/pay/refund');
	    $result = $this->xmlToArray($xmlresult);
	    
	    return $result;
	}

	//需要使用证书的请求
	private function postXmlSSLCurl($xml,$url,$second=30){
	    $ch = curl_init();
	    //超时时间
	    curl_setopt($ch,CURLOPT_TIMEOUT,$second);
	    //这里设置代理，如果有的话
	    //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
	    //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
	    curl_setopt($ch,CURLOPT_URL, $url);
	    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
	    //设置header
	    curl_setopt($ch,CURLOPT_HEADER,FALSE);
	    //要求结果为字符串且输出到屏幕上
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
	    //设置证书
	    //使用证书：cert 与 key 分别属于两个.pem文件
	    //默认格式为PEM，可以注释
	    curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
	    curl_setopt($ch,CURLOPT_SSLCERT, $this->SSLCERT_PATH);
	    //默认格式为PEM，可以注释
	    curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
	    curl_setopt($ch,CURLOPT_SSLKEY, $this->SSLKEY_PATH);
	    //post提交方式
	    curl_setopt($ch,CURLOPT_POST, true);
	    curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
	    $data = curl_exec($ch);
	    //返回结果
	    if($data){
	        curl_close($ch);
	        return $data;
	    }
	    else {
	        $error = curl_errno($ch);
	        echo "curl出错，错误码:$error"."<br>";
	        curl_close($ch);
	        return false;
	    }
	}

}