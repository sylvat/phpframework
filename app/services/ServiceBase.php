<?php

namespace App\Services;

use Phalcon\Di\Injectable;
use App\Core\Response;
use App\Core\App;

/**
 * @Desc Service基类
 * @Author tangqin
 * @Date 2016/8/1
 * @Time 17:32
 */
class ServiceBase extends Injectable
{
    protected $app = null;

    public function __construct()
    {
        $this->app = App::getInstance();
    }

    /**
     * 设置错误信息
     * @param int $code
     * @param string $msg
     */
    public function setErrorMsg($code = 0, $msg = '')
    {
        Response::$code = $code;
        Response::$msg = $msg;
    }

    /**
     * Get错误码
     * @return mixed
     */
    public function getErrorCode()
    {
        return Response::$code;
    }

    /**
     * Get错误信息
     * @return mixed
     */
    public function getErrorMsg()
    {
        return Response::$msg;
    }

    /**
     * @param array $data
     */
    public function send($data = [])
    {
        Response::send($data);
    }

}