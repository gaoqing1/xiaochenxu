<?php 

namespace app\lib\exception;

class ScopeException extends BaseException
{
	public $code=401;
	public $errorcode=10001;
	public $msg="权限不够";
}