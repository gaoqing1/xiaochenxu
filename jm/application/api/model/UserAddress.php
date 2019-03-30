<?php

namespace app\api\Model;
use think\Model;
class UserAddress extends BaseModel
{
	protected $hidden=['id','delete_time','user_id'];

}