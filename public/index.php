<?php
/**
 * @Desc 程序入口
 * @Author tangqin
 * @Date 2016/7/26
 * @Time 18:56
 */

use App\Core\Bootstrap;

//设置时区
date_default_timezone_set('Asia/Shanghai');

//根目录
define('BASE_DIR', realpath('..'));

/**
 * 错误显示
 */
//ini_set("display_errors", "On");
//error_reporting(E_ALL | E_STRICT);

/**
 * 常量设置
 */
include BASE_DIR . "/app/config/constants.php";
/**
 * 错误码定义
 */
include BASE_DIR . "/app/config/error_config.php";

require_once BASE_DIR . '/app/libs/helper.php';
require_once BASE_DIR . '/app/core/Bootstrap.php';

/**
 * 跨域配置
 */
//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Headers: Content-Type");

$bootstrap = new Bootstrap();

echo $bootstrap->run();

