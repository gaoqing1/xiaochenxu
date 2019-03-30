<?php 

namespace app\lib\exception;

class WecatException extends BaseException
{
	public $code=400;
	public $error=30001;
	public $msg="微信接口调用失败";
}