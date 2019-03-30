<?php

namespace app\api\Model;
use think\Model;
class User extends BaseModel
{
	public function address(){

		return $this->hasOne('UserAddress','user_id','id');
	}

	public function getOpen($openid){
		$user=$this->where('openid','=',$openid)->find();
		return $user;
	}
	public function addopen($id){
		$data=$this->create([
			'openid'=>$id
		]);
	return $data->id;
	}

}