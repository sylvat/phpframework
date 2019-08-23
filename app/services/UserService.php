<?php

namespace App\Services;

/**
 * @Desc 用户Service
 * @Author tangqin
 * @Date 2016/8/30
 * @Time 19:30
 */
class UserService extends ServiceBase
{
    /**
     * 获取用户信息
     * @param int $userId
     * @return mixed
     */
    public function getUserById($userId)
    {
        $user = $this->app->cache('User', ['userId' => $userId])->getUserData();
        return $user;
    }
}