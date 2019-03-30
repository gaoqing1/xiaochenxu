<?php

namespace app\api\controller\v1;
use think\Controller;
use app\api\Validate\ProductValidate;
use app\api\model\Product as ProductModel;
use app\api\Validate\IDMustBePositivelnt;
use app\lib\exception\ProductException;
class Product extends Controller
{
	// 最近新品
	public function getProducts($count='15'){
		(new ProductValidate())->gocheck();
		
		$request=ProductModel::getCountProduct($count);
		if(!$request){
			throw new ProductException();
		}
		
		$list=$request->hidden(['summary']);
		return $list;
	}

	/*单个分类下的商品*/
	public function getProductCate($id){
		(new IDMustBePositivelnt())->gocheck();
		$data=ProductModel::getProductCates($id);
		$list=$data->hidden(['summary']);
		return $list;
	}
	// 商品详情
	public function getOne($id){
		
		(new IDMustBePositivelnt())->gocheck();
		$Product=ProductModel::getProductDetail($id);
		if(!$Product){
			throw new ProductException();
			
		}
		return $Product;
	}
}