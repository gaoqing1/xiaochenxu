<?php 

namespace  app\api\validate;

class IDMustBePositivelnt extends BaseValidate
{
	protected $rule=[
		'id'=>'require|isPositiveInteger',
	];

	protected $message=[
		'id'=>'id必须为正整数',
	];
}