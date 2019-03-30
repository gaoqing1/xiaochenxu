<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/27
 * Time: 16:26
 */

namespace app\api\service;

use think\Config;

/**
 * 单例连接redis类
 * Class Redis
 * @package app\api\service
 */
class Redis
{
    // 有一个实例的静态变量
    // 狗仔方法和克隆方法设置为私有 防止外部调用
    //提供一个获取实力的静态方法
    private static $_instabce=null;//静态实例
    //私有的构造方法
    private function __construct()
    {
        self::$_instabce=new \Redis();
        self::$_instabce->connect(Config('redis.host'),Config('redis.post'));


    }

    //获取静态实例
    public static function GetRedis()
    {
        if(!self::$_instabce){
            new self;
        }
        return self::$_instabce;
    }

    //禁止克隆
    private function __clone()
    {

    }
}