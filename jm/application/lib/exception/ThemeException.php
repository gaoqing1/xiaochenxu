<?php 

namespace app\lib\exception;

class ThemeException extends BaseException
{
	public $code=404;
	public $error=30000;
	public $msg="指定主题不存在";
}