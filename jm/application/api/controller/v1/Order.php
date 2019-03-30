<?php

namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\service\Redis;
use app\api\validate\OrderValidate;
use app\api\validate\IDMustBePositivelnt;
use app\api\validate\PageParameter;
use app\lib\exception\OrderException;
use  app\api\Model\Order as OrderModel;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;
class Order extends BaseController
{

	// 前置操作
	protected $beforeActionList=[
		'checkplaceOrder'=>['only'=>'placeorder'],
		'checkPrimaryScope'=>['omly'=>'getdatail,getsummarybyuser']
	];

	// 简要订单信息
	public function getSummaryByUser($page=1,$size=10)
	{
		(new PageParameter())->gocheck();
		$uid=TokenService::getCurrentUid();
//        $uid=1;
		$pageinate=OrderModel::getSummaryUser($uid,$page,$size);

		if($pageinate->isEmpty())
		{
			return [
				'data'=>[],
				'current_page'=>$pageinate->getCurrentPage()
			];
		}

		$data=$pageinate->hidden(['snap_items','snap_address','prepay_id'])->toArray();
		return [
			'data'=>$data,
			'ccurrent_page'=>$pageinate->getCurrentPage()
		];
	}

	// 订单详情
	public function getDatail($id)
	{
		(new IDMustBePositivelnt())->gocheck();
		$order=OrderModel::get($id);
		if(!$order)
		{
			throw new OrderException();
			
		}
		return $order;

	}

	public function placeOrder(){
		(new OrderValidate())->gocheck();
		$products=input('post.products/a');
		$uid=TokenService::getCurrentUid();

		$order= new OrderService();
		$status=$order->place($uid,$products);
		return $status;
	}

}