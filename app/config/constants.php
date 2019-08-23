<?php
/**
 * @Desc 常量定义
 * @Author tangqin
 * @Date 2016/7/26
 * @Time 19:50
 */

/**
 * 运行开始时间
 */
define('APP_START_TIME', microtime(true));

/**
 * 运行开始消耗内存
 */
define('APP_START_MEMORY', memory_get_usage());

/**
 * 版本号
 */
define('APP_VERSION', '1.0.0');

/**
 * 任务状态
 */
define('TASK_STATUS_NORMAL', 0); //启用

define('TASK_STATUS_DISABLE', 1); //禁用
