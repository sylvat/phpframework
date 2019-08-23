<?php

namespace App\Controllers;

class UserController extends ControllerBase
{
    /**
     * 获取用户信息
     */
    public function getInfoAction()
    {
        $res = $this->getPostData();
        if (empty($res['userId'])) {
            $this->setErrorMsg(CODE_PARAMS_ERROR, 'params error');
            $this->send();
        }
        $data = $this->app->service('User')->getUserById($res['userId']);
        if(!$data) {
            $this->setErrorMsg(CODE_PARAMS_ERROR, 'user is not found');
            $this->send();
        }
        $response['user'] = $data;
        $this->send($response);
    }
}