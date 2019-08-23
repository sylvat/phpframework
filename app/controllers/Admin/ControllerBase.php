<?php

namespace App\Controllers\Admin;

use Phalcon\Mvc\Controller;
use App\Core\App;
use App\Core\Response;

/**
 * @Desc 控制器基类
 * @Author tangqin
 * @Date 2016/7/26
 * @Time 19:32
 */
class ControllerBase extends Controller
{

    protected $app = null;

    /**
     * 基类初始化
     */
    public function initialize()
    {
        $this->app = App::getInstance();
    }

    public function getPostData()
    {
        $res = file_get_contents('php://input', 'r');
        $res = json_decode(rawurldecode($res), true);
        return $res;
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
     * 接口返回调用
     * @param array $data
     */
    public function send($data = [])
    {
        Response::send($data);
    }

}
