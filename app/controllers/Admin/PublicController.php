<?php

namespace App\Controllers\Admin;

class PublicController extends ControllerBase
{
    /**
     * 删除用户缓存
     */
    public function delUserCacheAction()
    {
        $res = $this->getPostData();

        if (empty($res['userId'])) {
            $this->setErrorMsg(CODE_PARAMS_ERROR, 'params error');
            $this->send();
        }
        $this->app->cache('User',['userId'=>$res['userId']])->delCache();
        $this->send();
    }
}