<?php

namespace App\Controllers;
/**
 * @Desc 错误页面
 * @Author tangqin
 * @Date 2016/7/28
 * @Time 19:00
 */

class ErrorController extends ControllerBase
{
    /**
     * 404错误
     */
    public function show404Action()
    {
        $this->setErrorMsg(CODE_PAGE_NOT_FOUND, 'Not Found');
        $this->send();
    }

    /**
     * 其他错误
     */
    public function show503Action()
    {
        $this->setErrorMsg(CODE_SERVER_ERROR, 'Server Error');
        $this->send();
    }
}