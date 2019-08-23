<?php

namespace App\Core;
use Phalcon\Di;

/**
 * @Desc 日志
 * @Author tangqin
 * @Date 2016/9/29
 * @Time 13:26
 */
class Logger
{
    const DEBUG = 'DEBUG';
    const INFO = 'INFO';
    const WARN = 'WARN';
    const ERROR = 'ERROR';
    const ALERT = 'ALERT';

    public static function debug($msg, $filename = 'debug/debug', $slice = true)
    {
        if(config('application.debug')) {
            return self::write(self::DEBUG, $msg, $filename, $slice);
        }
        return true;
    }

    public static function info($msg, $filename = 'info/info', $slice = true)
    {
        return self::write(self::INFO, $msg, $filename, $slice);
    }

    public static function warn($msg, $filename = 'warn/warn', $slice = true)
    {
        return self::write(self::WARN, $msg, $filename, $slice);
    }

    public static function error($msg, $filename = 'error/error', $slice = true)
    {
        return self::write(self::ERROR, $msg, $filename, $slice);
    }

    public static function alert($msg, $filename = 'alert/alert', $slice = true)
    {
        return self::write(self::ALERT, $msg, $filename, $slice);
    }

    protected static function write($level, $message, $filename = null, $slice = true)
    {
        $path = rtrim(config('logger.path'), '\\/') . DIRECTORY_SEPARATOR;
        if (empty($filename)) {
            $filename = $slice ? 'log-' . date('Ymd') . '.log' : 'log.log';
        } else {
            $path .= trim($filename, '\\/');
            $filename = $slice ? basename($path) . '-' . date('Ymd') . '.log' : basename($path) . '.log';
            $path = dirname($path) . DIRECTORY_SEPARATOR;
        }

        $filename = $path . $filename;
        //目录是否存在
        is_dir($path) ?: mkdir($path, 0777, true);

        $sub = "[%s] [%s] %s " . PHP_EOL;

        $message = sprintf($sub, date('Y-m-d H:i:s'), $level, $message);

        $fp = fopen($filename, 'a');
        if ($fp) {
            flock($fp, LOCK_EX);
            fwrite($fp, $message);
            flock($fp, LOCK_UN);
            fclose($fp);
            return true;
        }
        return false;
    }
}