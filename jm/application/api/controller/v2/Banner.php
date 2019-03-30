<?php

namespace app\api\controller\v2;
use think\Controller;
use app\api\validate\IDMustBePositivelnt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BaseException;
class Banner extends Controller
{

	public function getBanner($id)
	{
		return 111;
	}
}