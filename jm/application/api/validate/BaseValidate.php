<?php 

namespace  app\api\validate;
use think\Validate;
use think\Request;
use think\Exception;
use app\lib\exception\ParameterException;
class BaseValidate extends Validate
{
	public function gocheck(){
		$request=Request::instance()->param();
		$result=$this->batch()->check($request);

		if(!$result){
			$e=new ParameterException([
				'msg'=>$this->error
			]);
			
			throw $e;

		}else{
			return true;
		}
	}

		protected function isPositiveInteger($value, $rule='', $data='', $field='')
	{

		if(is_numeric($value) && is_int($value + 0) && ($value+0) > 0){

			return true;
		}
		return false;
	}

	protected function isEmptys($value,$rule='', $data='', $field='')
	{
		if(empty($value))
		{

			return false;
		}

		return true;
	}

	//手机验证
	protected function isMobile($value)
	{
		 // $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
   //      $result = preg_match($rule, $value);
   //      if ($result) {
   //          return true;
   //      } else {
   //          return false;
   //      }
   	return true;
	}

	    protected function isNotEmpty($value, $rule='', $data='', $field='')
    {
        if (empty($value)) {
            return $field . '不允许为空';
        } else {
            return true;
        }
    }

	// 过滤客户端传递过来的字段
	public function getDataByRule($arrays)
	{
		// 不允许包含user_id 和uid 防止恶意入侵
		if(array_key_exists('user_id',$arrays) || array_key_exists('uid',$arrays))
		{
			throw new ParameterException([
				'msg'=>'参数中包含有非法的user_id或者uid'
			]);
		}
		$dataarray=[];
		foreach ($this->rule as $key => $value) {
			$dataarray[$key]=$arrays[$key];
		}

		return $dataarray;
	}	
}