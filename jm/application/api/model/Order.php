<?php

namespace app\api\Model;


class Order extends BaseModel
{
	protected $hidden=['delete_time','update_time','user_id'];
	protected $autoWriteTimestamp=true;

	public function getSnapAddressAttr($value)
	{
		if(empty($value)){
			return null;
		}

		return json_decode($value);
	}

		public function getSnapItemsAttr($value)
	{
		if(empty($value)){
			return null;
		}

		return json_decode($value);
	}

	public static function getSummaryUser($uid,$page,$size)
	{
		
		$page=self::where('user_id','=',$uid)->order('create_time','desc')->paginate($size,true,['page'=>$page]);
		return $page;
	}

}