<?php 

namespace app\lib\exception;
use think\exception\Handle;
use think\Exception;
use think\Request;
use think\Log;
class ExceptionHander extends Handle
{
	private $code;
	private $msg;
	private $errorcode;
	public function render(\Exception $e){
		 if ($e instanceof BaseException) {
           	// 如果是自定义异常
           	$this->code=$e->code;
           	$this->msg=$e->msg;
           	$this->errorcode=$e->errorcode;
        } else {
        	if(Config("app_debug")){
        		return parent::render($e);
        	}else{

        	$this->code=500;
            $this->msg="服务器内部错误";
            $this->errorcode=999;
            $this->recordErrorLog($e);
        	}
           
        }
        $request=Request::instance();
        $result=[
        	'msg'=>$this->msg,
        	'error_code'=>$this->errorcode,
        	"result_url"=>$request->url()
        ];
        return json($result,$this->code);
	}
	private function recordErrorLog(\Exception $e){
		//初始化
		Log::init([
		 // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => ['error'],
		]);
		Log::record($e->getMessage(),'error');
	}
}