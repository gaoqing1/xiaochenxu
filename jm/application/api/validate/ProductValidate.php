<?php 

namespace  app\api\validate;

class PRoductValidate extends BaseValidate
{
	protected $rule=[
		'count'=>'isPositiveInteger|between:1,15',
	];

}