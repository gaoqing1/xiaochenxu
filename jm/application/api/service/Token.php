<?php

namespace app\api\service;
use think\Request;
use think\Cache;
use think\Exception;
use app\lib\exception\TokenException;
use app\lib\exception\ScopeException;
use app\lib\enum\ScopeEnum;
class Token 
{
	protected static function  generateToken(){
		// 32个字符组成一组随机字符串
		$randChars=getRandChars(32);
		// 由三组加密方式
		$time=$_SERVER['REQUEST_TIME_FLOAT'];
		// salt 盐
		$tokensalt=Config('yan.token_salt');

		return md5($randChars.$time.$tokensalt);
	}

	// 获取缓存步骤
	public static function getCurrentTokenVar($key){
		// 获取客户端传递过来的token令牌
		$token=Request::instance()->header('token');
		$vars=Cache::get($token);
		if(!$vars){
			throw new TokenException();
		}
		if(!is_array($vars)){
			$vars=json_decode($vars,true);
		}
		if(!array_key_exists($key, $vars)){
			throw new Exception('尝试获取token变量并不存在');
		}
		return $vars[$key];
	}
	// 获取uid
	public static function getCurrentUid(){
		$uid=self::getCurrentTokenVar('uid');
		return $uid;
	}

	// 用户和cms管理员都可以访问的权限
	public static function checkScope(){

		$scope=self::getCurrentTokenVar('scope');
		if($scope){
			if($scope>=ScopeEnum::User){
				return true;
			}else{
				throw new ScopeException();
				
			}
		}else{
			throw new TokenException();
		}
	}

	 //只有用户可以访问
	 public static function checkOrder(){
	 	$scope=self::getCurrentTokenVar('scope');
	 	if($scope){
	 		if($scope==ScopeEnum::User){
	 			return true;
	 		}else{
	 			throw new ScopeException();
	 		}
	 	}else{
	 		throw new TokenException();
	 		
	 	}
	 }

	 // 检测用户操作是否合法
	 public static function isValidOperate($checkUID)
	 {
	 	if(!$checkUID)
	 	{
	 		throw new Exception("检测UID时必须传入一个被检测的UID");
	 	}
	 	$currentOperateUID=self::getCurrentUid();

	 	if($currentOperateUID==$checkUID){
	 		return true;
	 	}
	 	return false;
	 }

	 // 检测客户端传过来的token
	 
	 public static function verifyToken($token)
	 {
	 	$exist = Cache::get($token);
	 	if($exist){
	 		return true;
	 	}else{
	 		return false;
	 	}
	 }
}



