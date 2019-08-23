<?php

use Phalcon\Mvc\Router;

/**
 * @Desc 路由配置
 * @Author tangqin
 * @Date 2016/7/26
 * @Time 17:43
 */
$router = new Router(false);
$router->removeExtraSlashes(true);

$router->setDefaults([
    'controller' => 'Error',
    'action' => 'show404'
]);

$router->add('/error503', [
    'controller' => 'Error',
    'action' => 'show503'
]);

$router->add('/user/getInfo', [
    'controller' => 'User',
    'action'     => 'getInfo'
]);

$router->add('/admin/public/delUserCache', [
    'controller' => 'Admin\Public',
    'action'     => 'delUserCache'
]);

return $router;