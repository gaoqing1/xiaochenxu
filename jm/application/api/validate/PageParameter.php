<?php 

namespace  app\api\validate;

class PageParameter extends BaseValidate
{
	protected $rule=[
		'page'=>"isPositiveInteger",
		'size'=>'isPositiveInteger'
	];

	protected $message=[
		'page'=>'page必须为正整数',
		'size'=>'size必须为正整数'
	];
}