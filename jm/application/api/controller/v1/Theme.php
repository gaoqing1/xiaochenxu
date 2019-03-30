<?php

namespace app\api\controller\v1;
use think\Controller;
use app\api\validate\IDCollectionValidate;
use app\api\validate\IDMustBePositivelnt;
use app\api\model\Theme as ThemeModel;
use app\lib\exception\BaseException;
use app\lib\exception\ThemeException;
class Theme extends Controller
{

	public function getTheme($ids)
	{
		(new IDCollectionValidate())->gocheck();
		$ids=explode(',',$ids);
		$request=ThemeModel::with('topicimg,headimg')->select($ids);
		if(!$request){
			throw new ThemeException();
		}
		return $request;
	}

	// 获取主题下的商品列表
	public function  getProducts($id){
		(new IDMustBePositivelnt())->gocheck();
		$data=ThemeModel::getThemeProducts($id);
		if(!$data){
			throw new ThemeException();
			
		}
		return $data;
	}
}