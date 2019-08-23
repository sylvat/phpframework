<?php

namespace App\Cache;

use App\Core\App;
use Phalcon\Di\Injectable;

/**
 * @Desc 缓存基类
 * @Author tangqin
 * @Date 2016/8/10
 * @Time 17:16
 */
abstract class CacheBase extends Injectable
{
    protected $app = null;

    protected $prefix = '';

    protected $ttl = 86400; //过期时间

    protected $userId = '';

    public function __construct($params)
    {
        $this->app = App::getInstance();

        $this->prefix = $this->config->application->redisPrefix;

        $this->userId = isset($params['userId']) ? $params['userId'] : 0;
    }
}