<?php 

namespace app\lib\exception;

class UserMessage extends BaseException
{
	public $code=201;
	public $errorcode=0;
	public $msg="ok";
}