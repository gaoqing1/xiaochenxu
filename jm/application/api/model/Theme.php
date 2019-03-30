<?php

namespace app\api\Model;
use think\Model;
class Theme extends BaseModel
{
	protected $hidden=['update_time','delete_time','topic_img_id','head_img_id'];
	public function topicimg(){
		return $this->belongsTo('Image','topic_img_id','id');
	}
	public function headimg(){
		return $this->belongsTo('Image','head_img_id','id');
	}

	public function products(){
		return $this->belongsToMany('product','theme_product','product_id','theme_id');
	}

	public static function getThemeProducts($id){
		$theme=self::with('products,topicimg,headimg')->find($id);
		return $theme;
	}
}