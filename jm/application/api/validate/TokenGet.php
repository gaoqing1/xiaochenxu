<?php 

namespace  app\api\validate;

class TokenGet extends BaseValidate
{
	protected $rule=[
		'code'=>'require|isEmptys'
	];

	protected $message=[
		'code'=>'token不存在'
	];
}