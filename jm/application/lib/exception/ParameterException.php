<?php 

namespace app\lib\exception;

class ParameterException extends BaseException
{
	public $code=400;
	public $error=10000;
	public $msg="参数错误";
}