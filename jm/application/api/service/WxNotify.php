<?php 
 
namespace app\api\service;
use app\api\Model\Order as OrderModel;
use app\api\service\Order as Orderservice;
use app\api\Model\Product as ProductModel;
use app\lib\enum\OrderStatusEnum;
use think\Loader;
use think\Log;
use think\Db;
Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

 class WxNotify extends \WxPayNotify
 {
 	public function NotifyProcess($data, &$msg)
	{
		//TODO 用户基础该类之后需要重写该方法，成功的时候返回true，失败返回false
		if($data['result_code']=="SUCCESS")
		{
			$order_no=$data['out_trade_no'];
			Db::startTrans();
			try {
				$order=OrderModel::where('order_no','=',$order_no)->lock(true)->find();
				if($order->status==1){
					$orderserviice=new Orderservice();
					$stockstatus=$orderserviice->checkOrderStock($order->id);
					if($stockstatus['pass'])
					{
						$this->UpdataOrderStatus($order->id , true);//修改订单状态
						$this->reduceStock($stockstatus);//减库存
					}else{
						$this->UpdataOrderStatus($order->id,false);
					}
				}
				Db::commit();
				return true;
			} catch (Exception $e) {
				log::error($e);
				Db::rollback();
				return false;
			}
		}else{
		    return true;
        }
	}
	// 减库存
	private function reduceStock($stockstatus)
	{
		foreach ($stockstatus['pStatusArray'] as $key => $value) {
			ProductModel::where('id','=',$value['id'])->setDec('stock',$value['count']);
		}
	}
	// 更新订单状态
	private function UpdataOrderStatus($orderID , $success)
	{
		$status=$success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
		OrderModel::where('id','=',$orderID)->update(['status'=>$status]);
	}
 }