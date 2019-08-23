<?php

namespace App\Core;

use Phalcon\Di\Injectable;

/**
 * @Desc App类
 * @Author tangqin
 * @Date 2016/8/16
 * @Time 14:35
 */
class App extends Injectable
{
    private static $_pool = array();

    private static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

    /**
     * 获取Service对象
     * @param $serviceName
     * @param $params
     * @return mixed
     */
    public function service($serviceName, $params = [])
    {
        $serviceName = '\App\Services\\' . $serviceName . 'Service';

        if (empty(self::$_pool['service'])) {
            self::$_pool['service'] = [];
        }
        $key = md5($serviceName . json_encode($params));
        if (!isset(self::$_pool['service'][$key])) {
            self::$_pool['service'][$key] = new $serviceName($params);
        }

        return self::$_pool['service'][$key];
    }

    /**
     * 获取Model对象
     * @param $modelName
     * @param $params
     * @return mixed
     */
    public function model($modelName, $params = [])
    {
        $modelName = '\App\Models\\' . $modelName;

        if (empty(self::$_pool['model'])) {
            self::$_pool['model'] = array();
        }
        $key = md5($modelName . json_encode($params));
        if (!isset(self::$_pool['model'][$key])) {
            self::$_pool['model'][$key] = new $modelName($params);
        }

        return self::$_pool['model'][$key];
    }

    /**
     * 获取日志实例
     * @param string $filename 日志文件名
     * @return mixed
     */
    public function logger($filename = '')
    {
        return $this->getDI()->get('logger', [$filename]);
    }

    /**
     * 获取Cache对象
     * @param $cacheName
     * @param $params
     * @return mixed
     */
    public function cache($cacheName, $params = array())
    {
        $cacheName = '\App\Cache\\' . $cacheName . 'Cache';

        if (empty(self::$_pool['cache'])) {
            self::$_pool['cache'] = array();
        }
        $key = md5($cacheName . json_encode($cacheName));
        if (!isset(self::$_pool['cache'][$key])) {
            self::$_pool['cache'][$key] = new $cacheName($params);
        }

        return self::$_pool['cache'][$key];
    }

}