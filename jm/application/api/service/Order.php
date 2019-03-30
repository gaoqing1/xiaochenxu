<?php

namespace app\api\service;
use app\api\Model\Product as ProductModel;
use app\api\model\OrderProduct;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;
use app\api\Model\UserAddress as UserAddressModel;
use app\lib\exception\UserException;
use think\Db;
use think\Log;

class Order
{
	// 订单的商品列表  客户端传递过来的products参数
	protected $oproducts;

	// 真实的商品信息
	protected $products;

	protected $uid;


	public function place($uid , $oproducts){
		// oproducts和products 对比
		// products 从数据库中查询出来
		$this->oproducts=$oproducts;
		$this->products= $this->getProductByOrder($oproducts);
		$this->uid=$uid;
		// 检测库存量
		$status=$this->getOrderStatus();
		if(!$status['pass']){
			$status['order_id']=-1;
			return $status;
		}

		// 开始创建订单
		$orderSnap=$this->snapOrder($status);

		$order=$this->createOrder($orderSnap);
		$order['pass']=true;
		return $order;
	}

	private function createOrder($snap)
	{
		Db::startTrans();
		try {
			$orderNo=$this->makeOrderNo();//订单编号
			$order=new OrderModel();
			$order->user_id=$this->uid;//用户id
			$order->order_no=$orderNo;
			$order->total_price=$snap['orderPrice'];//总价格
			$order->total_count=$snap['totalCount'];//总数量
			$order->snap_img=$snap['snapImg'];//缩略图
			$order->snap_name=$snap['snapName'];//名称
//			$order->snap_address=$snap['snapAddress'];//收货地址
//			$order->snap_items=json_encode($snap['pStatus']);
			$order->save();
			
			// order_product表新增
			$order_ID=$order->id;
			$create_time=$order->create_time;
			foreach ($this->oproducts as  &$p) {
				$p['order_id']=$order_ID;
			}
//'user:'.$order->user_id,'order:'.$order_ID,json_encode($snap['pStatus']),
//                                'order_address:'.$order_ID,$snap['snapAddress']
			$redis=Redis::GetRedis();
			$a=$redis->hMSet('user:'.$order->user_id,[
			    "order:".$order_ID=>json_encode($snap['pStatus']),
                "order_address:".$order_ID=>$snap['snapAddress']
                ]);
			if(!$a){
			    throw new OrderException([
			        'msg'=>'数据缓存失败'
                ]);
            }

			$orderproduct=new OrderProduct();
			$orderproduct->saveAll($this->oproducts);
			Db::commit();
			return [
				'order_no'=>$orderNo,
				'order_id'=>$order_ID,
				'create_time'=>$create_time
			];
			
		} catch (Exception $e) {
			Db::rollback();
			return $e;
			
		}
		
	}

	// 生成订单号
	public static function makeOrderNo()
	{
		$yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
         $yCode[intval(date('Y')) - 2019] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
	}

	// 生成订单快照
	private function snapOrder($status)
	{
		$snap=[
			"orderPrice"=>0,//订单总价格
			"totalCount"=>0,//订单商品总数量
			'pStatus'=>[],
			"snapAddress"=>null,//收获地址
			'snapName'=>'',
			'snapImg'=>''
		];

		$snap['orderPrice']=$status['orderPrice'];
		$snap['totalCount']=$status["totalCount"];
		$snap['pStatus']=$status['pStatusArray'];
		$snap['snapAddress']=json_encode($this->getUserAddress());
		$snap['snapName']=$this->products[0]['name'];
		$snap['snapImg']=$this->products[0]['main_img_url'];
		if(count($this->products)>1){
			$snap['snapName'] .='等';
		}
		return $snap;
	}

	// 查询收获地址
	private function getUserAddress()
	{
		$useraddress=UserAddressModel::where('user_id','=',$this->uid)->find();
		if(!$useraddress){
			throw new UserException([
				'msg'=>'用户收获地址不存在，下单失败',
				'errorCode'=>60001
			]);
		}

		return $useraddress->toArray();
	}

	//外铺调用查询库存量
	public function checkOrderStock($orderID)
	{
		$oproducts=OrderProduct::where('order_id','=',$orderID)->select();
		$this->oproducts=$oproducts;
		$this->products=$this->getProductByOrder($oproducts);
		$status=$this->getOrderStatus();
		return $status;
	}

	// 获取订单状态
	private function getOrderStatus()
    {
		$status=[
			'pass'=>true,
			'orderPrice'=>0,//订单总价格
			'totalCount'=>0,
			'pStatusArray'=>[]
		];
		foreach($this->oproducts as $oproduct)
		{
			$pStatus=$this->getProductStatus(
				$oproduct['product_id'],$oproduct['count'],$this->products
			);
			if(!$pStatus['haveStock']){
				$status['pass']=false;
			}
			$status['orderPrice'] += $pStatus['totalPrice'];
			$status['totalCount'] += $pStatus['count'];
			array_push($status['pStatusArray'],$pStatus);
		}
		return $status;
	}

	private function getProductStatus($oPID, $oCount , $products)
	{
		$pIndex=-1;
		$pStatus=[
			'id'=>null,//商品id
			'haveStock'=>false,//库存量状态
			'count'=>0,//商品数量
			'name'=>'',//商品名称
			'totalPrice'=>0,//商品类的总价格
		];

		  for ($i = 0; $i < count($products); $i++) {

            if ($oPID == $products[$i]['id']) {
                $pIndex = $i;
            }
        }
		if($pIndex==-1){
			// 客户端传递过来的productid有可能不存在
			throw new OrderException([
				'msg'=>"id为".$oPID.'商品不存在，创建订单失败'
			]);
		}else{

			$product=$products[$pIndex];
			$pStatus['id']=$product['id'];
			$pStatus['name']=$product['name'];
			$pStatus['count']=$oCount;
			$pStatus['totalPrice']=$product['price'] * $oCount;

			if($product['stock'] - $oCount >=0){
				$pStatus['haveStock']=true;
			}
		}

		return $pStatus;
	}

	private function getProductByOrder($oproducts){
		$abIds=[];

		foreach ($oproducts as $value) {
		
			array_push($abIds,$value['product_id']);
		}
		  $products = ProductModel::all($abIds)
           ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
             ->toArray();
		return $products;
	}
}