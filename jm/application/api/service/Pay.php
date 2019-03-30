<?php 
 
 namespace app\api\service;

use app\api\service\Order as Orderservice;
use app\api\service\Token as Tokenservice;
use app\api\Model\Order as OrderModel;
use app\lib\exception\OrderException;
use think\Exception;

use app\lib\enum\OrderStatusEnum;
use think\Loader;
use think\Log;

// 引入SDK
Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');
 class Pay
 {
 	private $orderID;
 	private $orderNo;

 	function __construct($orderID)
 	{
 		if(!$orderID){
 			throw new Exception("订单号不允许为NULL");
 		}
 		$this->orderID=$orderID;
 	}

 	public function pay()
 	{
 		// 订单可能根本不存在
 		// 订单确实存在 可是和当前用户不匹配
 		// 订单有可能已经被支付过了
 		// 进行库存量检测
 		$this->checkOrderValid();
 		$orderservice=new Orderservice();
 		$status=$orderservice->checkOrderStock($this->orderID);
 		if(!$status['pass'])
 		{
 			return $status;
 		}

 		return $this->makeWxPreDrder($status['orderPrice']);
 	}

 	// 生成预订单
 	private function makeWxPreDrder($totalPrice)
 	{
 		// 获取openid
 		$openid=Tokenservice::getCurrentTokenVar('openid');
 		if(!$openid)
 		{
 			throw new TokenException();
 			
 		}

 		$WxOrderData=new \WxPayUnifiedOrder();

 		$WxOrderData->SetOut_trade_no($this->orderNo);//订单号
 		$WxOrderData->SetTrade_type('JSAPI');//交易类型
 		$WxOrderData->SetTotal_fee($totalPrice*100);//交易总金额  单位 分
 		$WxOrderData->SetBody('零食商贩');
 		$WxOrderData->SetOpenid($openid);
 		$WxOrderData->SetNotify_url(Config('Wxpay.wxpay_url'));//异步通知地址
 		return  $this->getPaySignature($WxOrderData);
 	}
 	private function getPaySignature($WxOrderData)
 	{

 		$wxOrder= \WxPayApi::unifiedOrder($WxOrderData);
 		
 		if($wxOrder['return_code'] !='SUCCESS' || $wxOrder['result_code'] != "SUUCCESS")
 		{
 			Log::record($wxOrder,'error');
 			log::record('获取预支付订单失败','error');
 		}

 		$this->recordPreOrder($wxOrder);
 		$sign=$this->sign($wxOrder);
 		return $sign;
 	}

 	// 处理微信返回的参数返回给客户端
 	private function sign($wxOrder)
 	{
 		$jsApiPayData=new \WxPayJsApiPay();
 		$jsApiPayData->SetAppid(Config('weixin.app_id'));
 		$jsApiPayData->SetTimeStamp((string)time());

 		$rand=md5(time() . mt_rand(0,10000));
 		$jsApiPayData->SetNonceStr($rand);
 		$jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);

 		$jsApiPayData->SetSignType('md5');

 		$sign=$jsApiPayData->MakeSign();
 		$rawvalues=$jsApiPayData->GetValues();
 		$rawvalues['paysign']=$sign;
 		unset($rawvalues['appId']);

 		return $rawvalues;
 	}

 	private function recordPreOrder($wxOrder)
 	{
 		OrderModel::where('id','=',$this->orderID)->update(['prepay_id'=>$wxOrder['prepay_id']]);
 	}

 	// 检测订单是否合法
 	private function checkOrderValid()
 	{
 		$order=OrderModel::where('id','=',$this->orderID)->find();

 		if(!$order){
 			throw new OrderException();
 			
 		}
 		if(!Tokenservice::isValidOperate($order->user_id))
 		{
 			throw new TokenException([
 				"msg"=>'订单与用户不匹配',
 				'errorcode'=>10003
 			]);
 			
 		}

 		if($order->status!=OrderStatusEnum::UNPAID)
 		{
 			throw new OrderException([
 				'msg'=>'订单状态异常',
 				'code'=>400,
 				'errorcode'=>80003
 			]);
 			
 		}
 		$this->orderNo=$order->order_no;
 		return true;
 	}
 }