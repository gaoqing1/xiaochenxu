<?php

namespace app\api\Model;
use think\Model;
class Category extends BaseModel
{
	protected $hidden=['update_time','delete_time'];
	public function getallCategroy(){
		return $this -> belongsTo('Image','topic_img_id','id');
	}
}