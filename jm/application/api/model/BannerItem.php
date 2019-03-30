<?php

namespace app\api\Model;
use think\Model;
class BannerItem extends Model
{
	protected $hidden=['delete_time','img_id','id','banner_id'];

	public function img(){
		
		return $this->beLongsto('Image','img_id','id');
	}
}