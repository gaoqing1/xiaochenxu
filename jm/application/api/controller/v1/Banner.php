<?php

namespace app\api\controller\v1;
use think\Controller;
use app\api\validate\IDMustBePositivelnt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BaseException;
class Banner extends Controller
{

	public function banner($id)
	{
		
		(new IDMustBePositivelnt())->batch()->gocheck();
		$Banner=BannerModel::getBanner($id);	
		if(!$Banner){
			throw new BaseException();
		}
		return $Banner;
	}
	
}