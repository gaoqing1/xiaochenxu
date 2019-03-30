<?php 

namespace app\lib\exception;

class UserException extends BaseException
{
	public $code=404;
	public $error=60000;
	public $msg="用户不存在";
}