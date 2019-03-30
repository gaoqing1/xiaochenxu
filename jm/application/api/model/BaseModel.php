<?php

namespace app\api\Model;
use think\Model;
class BaseModel extends Model
{
	// 图片url
	protected function promfxt($value,$data){
		$FromUrl=$value;
		if($data['from']==1){

		return Config('imgurl.image_url').$value;
		}
		return $FromUrl;
	}
}