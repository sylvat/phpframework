<?php

namespace App\Cache;

use App\Models\User;

/**
 * @Desc 用户缓存
 * @Author tangqin
 * @Date 2016/9/13
 * @Time 14:33
 */
class UserCache extends CacheBase
{
    const KEY = 'str:user';

    public function getKey() {
        if (empty($this->userId)) {
            throw new \Exception('userId is null');
        }
        return $this->prefix . self::KEY . ':' . $this->userId;
    }

    /**
     * 获取缓存数据
     * @return User|bool|mixed|\Phalcon\Mvc\Model\ResultInterface
     * @throws \Exception
     */
    public function getUserData()
    {
        //从缓存中取数据
        $data = $this->redisUser->get($this->getKey());
        if ($data) {
            return json_decode($data, true);
        }
        //从数据库中取数据
        $data = User::findFirst([
            'userId = :userId:',
            'bind' => [
                'userId' => $this->userId
            ]
        ]);
        if (!$data) {
            return false;
        }
        $data = $data->toArray();

        $this->redisUser->setex($this->getKey(), $this->ttl, json_encode($data));
        return $data;
    }

    /**
     * 更新缓存数据
     * @param $newData
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function updateUserData($newData)
    {
        //从缓存中取数据
        $data = $this->redisUser->get($this->getKey());
        if (!$data) {
            return false;
        }

        $data = json_decode($data, true);

        $data = array_merge($data, $newData);

        $this->redisUser->setex($this->getKey(), $this->ttl, json_encode($data));
        return $data;
    }

    /**
     * 删除缓存
     */
    public function delCache() {
        $this->redisUser->del($this->getKey());
    }

}