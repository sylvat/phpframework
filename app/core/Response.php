<?php

namespace App\Core;

/**
 * @Desc Response类
 * @Author tangqin
 * @Date 2016/8/5
 * @Time 11:10
 */
class Response
{
    public static $code = 0;

    public static $msg = '';

    /**
     * 结果返回
     * @param array $data
     * @param string $type
     * @param string $handler
     */
    public static function send($data = [], $type = 'JSON', $handler = 'jsonpReturn')
    {
        $data = $data ? $data : new \stdClass();
        $ret['code'] = self::$code;
        $ret['msg'] = self::$msg;
        $ret['data'] = $data;
        switch (strtoupper($type)) {
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($ret));
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($ret));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                //$handler : jsonpReturn ,callback
                exit($handler . '(' . json_encode($ret) . ');');
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($ret);
            default     :
                exit($ret);
        }
    }
}