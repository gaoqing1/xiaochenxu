<?php 

namespace app\lib\exception;

class TokenException extends BaseException
{
	public $code=401;
	public $errorcode=10005;
	public $msg="token缓存失败或者token不存在";
}