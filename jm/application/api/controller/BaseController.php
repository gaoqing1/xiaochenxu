<?php

namespace app\api\controller;
use app\api\service\Token as TokenService;
use think\Controller;
class BaseController extends Controller
{
	//权限控制 用户管理员都可以访问
	protected function checkPrimaryScope(){
		TokenService::checkScope();
	}
	// 用户可访问
	protected function checkplaceOrder(){
		TokenService::checkOrder();
	}
} 