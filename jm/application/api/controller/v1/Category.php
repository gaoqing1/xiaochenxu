<?php

namespace app\api\controller\v1;
use think\Controller;
use app\api\Model\Category as CategoryModel;
use\app\lib\exception\CategoryException;
class Category extends Controller
{

	// 分类列表
	public function getCategroy(){
		$data=CategoryModel::with('getallCategroy')->select();
	
		if(!$data) {
			throw new CategoryException();
			
		}
		return $data;
	}
}