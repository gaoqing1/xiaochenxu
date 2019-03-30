<?php

namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\validate\AddressValidate;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\api\model\UserAddress;
use app\lib\exception\UserException;
use app\lib\exception\UserMessage;
class Address extends BaseController
{
	// 前置操作
	protected $beforeActionList = [
		'checkPrimaryScope' => ['only'=>'createorupdateaddress']
		];
		
		// 设置或者而修改用户地址
	public function createOrUpdateAddress(){
		$addressvalidate=new AddressValidate;
		$addressvalidate->gocheck();

		// 根据token获取uid
		$uid=TokenService::getCurrentUid();
		
		// 根据uid来获取用户数据 判断用户是否存在  不存在返回异常
		$user=UserModel::get($uid);
		if(!$user){
			throw new UserException();
		}
		// 获取用户从客户端提交过来的地址信息
		$dataArray=$addressvalidate->getDataByRule(input('post.'));
		// 通过获取到的地址信息是否存在 从而判断是添加地址还是更新地址
		$userAddress=$user->address;
		if(!$userAddress){
			$user->address()->save($dataArray);
		}else{
			$user->address->save($dataArray);
		}
		return json(new UserMessage(),201);
	}

	// 获取用户地址
	public function getUserAddress(){
		$uid = TokenService::getCurrentUid();
		$userAddress = UserAddress::where('user_id','=',$uid)->find();
		
		if(!$userAddress) {
			throw new UserException([
				'msg'=>'用户地址不存在',
				'errorcode'=>60001
			]);
			
		}

		return $userAddress;
	}
}