<?php 

namespace app\lib\exception;

class ProductException extends BaseException
{
	public $code=404;
	public $error=10001;
	public $msg="指定的最新新品不存在";
}