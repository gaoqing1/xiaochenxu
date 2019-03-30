<?php

namespace app\api\Model;
use think\Model;
class Image extends BaseModel
{
	protected $hidden=['id','delete_time','from','update_time'];

	public function getUrlAttr($value,$data){
		return $this->promfxt($value,$data);
	}
}