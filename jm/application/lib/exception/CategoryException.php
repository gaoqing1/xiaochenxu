<?php 

namespace app\lib\exception;

class CategoryException extends BaseException
{
	public $code=404;
	public $errorcode=20001;
	public $msg="找不到分类";
}