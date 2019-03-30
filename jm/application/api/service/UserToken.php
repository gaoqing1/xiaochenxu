<?php

namespace app\api\service;
use think\Exception;
use app\lib\exception\WecatException;
use app\api\Model\User as UserModel;
use think\Cache;
use app\lib\exception\TokenException;
use app\lib\enum\ScopeEnum;
class UserToken extends Token
{
	protected $code;//客户端传过来的code
	protected $wxappid;//微信开发者id
	protected $wxappsecret;//小程序秘钥
	protected $wxloginurl;//

	 function __construct($code){
	 	$this->code=$code;
	 	$this->wxappid=Config('weixin.app_id');
	 	$this->wxappsecret=Config('weixin.app_secret');
	 	$this->wxloginurl=sprintf(Config('weixin.login_url'),$this->wxappid,$this->wxappsecret,$this->code);
	 
	}
	public function get(){

			$result=curl_get($this->wxloginurl);
			$wxresult=json_decode($result,true);
			if(empty($wxresult)){
				throw new Exception("获取session_key及openid时异常，微信内部错误");	
			}
			$LoginFial=array_key_exists('errcode',$wxresult);
			if($LoginFial){
				$this->processloginError($wxresult);

			}else{
				$token=$this->grantToken($wxresult);
				return $token;
			}

	}

	private function grantToken($wxresult){
		// 拿到openid
		// 数据库看一下openid是否存在
		// 存在 则不处理 不存在 新增一条user记录
		// 生成令牌 准备缓存数据  写入缓存
		// 把令牌返回给客户端
		$openid=$wxresult['openid'];
		$user = new UserModel();
		$list=$user->getOpen($openid);
		if($list){
			$uid=$list->id;
		}else{
			$uid=$user->addopen($openid);
		}
		$cache=$this->prochckevalue($wxresult,$uid);

		// 生成令牌  缓存
		$token=$this->saveToCache($cache);
		return $token;
	}

	private function saveToCache($cache){
		$key=self::generateToken();
		// 转换成字符串
		$value=json_encode($cache);
		// 过期时间
		$time=Config('yan.tokken_autotime');
		$data=cache($key,$value,$time);
		if(!$data){
			throw new TokenException([
				'msg'=>'服务器缓存异常',
				'errorcode'=>10005
			]);
		}
		return $key;
	}

	// 设置value
	private function prochckevalue($wxresult,$uid){
		$cacheValue=$wxresult;
		$cacheValue['uid']=$uid;
		// 代表用户的权限数值
		$cacheValue['scope']=ScopeEnum::User;
		return $cacheValue;
	}
	// 自定义异常
	private function processloginError($wxresult){
		throw new WecatException([
			'msg'=>$wxresult['errmsg'],
			'errorcode'=>$wxresult['errcode']

		]);
		
	}

}