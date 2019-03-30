<?php

namespace app\api\controller\v1;
use app\api\validate\IDMustBePositivelnt;
use app\api\controller\BaseController;
use app\api\service\Pay as Payservice;
use app\api\service\WxNotify as WxNotifyservice;
class Pay extends BaseController
{
	// 用户选择商品之后，向api提交所选商品的相关信息
	// api接收到信息后，需检查订单相关商品的库存量
	// 有库存 把订单数据写入数据库中 下单成功 返回客户端消息 告诉客户端可以支付了
	// 调用支付接口  进行支付
	// 还需要再次检测库存量
	// 服务器这边可以调用微信支付接口进行支付
	// 微信会返回给我们一个支付结果（异步）
	// 成功 进行库存量的检测
	// 成功 进行库存量的扣除
	
	// 前置操作
	protected $beforeActionList=[
		'checkplaceOrder'=>['only'=>'getpreorder']
	];
	public function getPreOrder($id=''){
		(new IDMustBePositivelnt())->gocheck();
		$pay=new Payservice($id);
		return $pay->pay();
	}

	// 回调地址
	public function receiveNotify()
	{
		// 1 检测库存量防止小概率的超卖
		// 2 更新这个订单的status的状态
		// 3 减库存
		// 如果成功处理 放回微信成功处理的信息  否则  放回没有成功处理
		
		$wxnotify= new WxNotifyservice();
		$wxnotify->Handle();
	}
}