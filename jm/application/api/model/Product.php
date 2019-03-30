<?php

namespace app\api\Model;
use think\Model;
class Product extends BaseModel
{
	protected $hidden=['delete_time','update_time','pivot','from','create_time','category_id'];

	public function getMainImgUrlAttr($value,$data){
		return $this->promfxt($value,$data);
	}

	public static function getCountProduct($count){

		$data=self::limit($count)->order('create_time desc')->select();
		return $data;
	}

	public static function getProductCates($id){
		return self::where('category_id','=',$id)->select();
		
	}

	public function imgs(){

		return $this->hasMany('ProductImage','product_id','id');
	}

	public function properties(){
		return $this->hasMany('ProductProperty','product_id','id');
	}

	public static function getProductDetail($id){
		$product=self::with([
				'imgs'=>function($query){
					$query->with(['imgUrl'])
					->order('order','asc');
				}
				])
				->with(['properties'])
				->find($id);
		return $product;

	}

}