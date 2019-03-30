<?php

namespace app\api\controller\v1;
use think\Controller;
use app\api\validate\TokenGet;
use app\api\service\UserToken;
use app\lib\exception\ParameterException;
use app\api\service\Token as TokenService;

class Token extends Controller
{
	public function getToken($code=''){
		(new TokenGet())->gocheck();
		
		$ut=new UserToken($code);
		$data=$ut->get();
		return [
			'token'=>$data
		];
	}


	// 验证token
	public function VerifyToken($token= '') {
		if(!$token) {
			throw new ParameterException([
				'msg'=>'Token不能为空'
			]);
		}
		$valid = TokenService::verifyToken($token);
		return [
			'isValid'=>$valid
		];
	}
	
}