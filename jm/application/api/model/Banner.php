<?php

namespace app\api\Model;
use think\Model;
use think\Db;
class Banner extends Model
{
	protected $hidden=['delete_time','update_time'];

	public function items(){

		return $this->hasMany('BannerItem','banner_id','id');
	}
	public static function getBanner($id){
		
		$Banner=self::with(['items','items.img'])->find($id);
		return $Banner;
	}
}