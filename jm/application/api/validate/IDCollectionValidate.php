<?php 

namespace  app\api\validate;

class IDCollectionValidate extends BaseValidate
{
	protected $rule=[
		'ids'=>'require|isCheckest',
	];
	protected $message=[
		'ids'=>'isd参数必须是以逗号分开的多个正整数',
	];

	protected function isCheckest($value){
		$data=explode(',', $value);
		if(empty($data) && !is_array($data)){
			return false;
		}
		foreach ($data as $key => $value) {
			if(!$this->isPositiveInteger($value)){
				return false;
			}
			return true;
		}
	}
}