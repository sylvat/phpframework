<?php

namespace App\Core;

use Phalcon\Di\Injectable;

/**
 * @Desc 锁，基于Redis
 * @Author tangqin
 * @Date 2016/8/19
 * @Time 18:56
 */
class Lock extends Injectable
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

    private function _isUseLock()
    {
        return true;
    }

    /**
     * 获取key
     * @param $key
     * @return string
     */
    private function _getLockKey($key)
    {
        return $this->config->application->redisPrefix . $key;
    }

    /**
     * 加锁
     * @access public
     * @param $lockKey 加锁key
     * @param int $lockTime 需要锁的最长时间默认五秒
     * @return bool
     */
    public function addLockWithAutoRelease($lockKey, $lockTime = 5)
    {
        if (!$this->_isUseLock()) {
            return true;
        }
        $lockKey = $this->_getLockKey($lockKey);
        $i = 0;

        //3次 如果还无法获取锁,返回加锁失败
        do {
            $i++;
            $lock_val = 1;
            $lock = $this->redisCache->set($lockKey, $lock_val, array('nx', 'ex' => $lockTime));
        } while ($lock != true && $i < 3);

        if ($i > 1) {
            Logger::info("set lock:$lockKey $i times.", 'lock/lock');
        }

        if ($lock == true) {
            $this->_autoRelease($lockKey);
        }

        return $lock;
    }

    /**
     * 自动释放锁
     * @param $lockKey
     */
    private function _autoRelease($lockKey)
    {
        register_shutdown_function(array(&$this, 'delLock'), $lockKey);
    }

    /*
     * 解锁
     * @access public
     */
    public function delLock($lockKey)
    {
        if (!$this->_isUseLock()) return;
        $this->redisCache->delete($lockKey);
    }
}