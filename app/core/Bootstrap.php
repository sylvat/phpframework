<?php

namespace App\Core;

use Phalcon\Config;
use Phalcon\Loader;
use Phalcon\Security;
use RuntimeException;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\View;
use Phalcon\Breadcrumbs;
use Phalcon\DiInterface;
use Phalcon\Http\Response;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Phalcon\Mvc\Model\MetaData\Memory as MemoryMetaData;
use Phalcon\Mvc\Model\MetaData\Redis as RedisMetaData;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\View\Exception as ViewException;

/**
 * @Desc 启动程序
 * @Author tangqin
 * @Date 2016/7/26
 * @Time 18:56
 */
class Bootstrap
{
    private $application;

    private $loaders = [
        'cache',
        'url',
        'security',
        'session',
        'router',
        'database',
        'redis',
        'dispatcher',
        'view'
    ];

    /**
     * Run the Application
     *
     * @return $this|string
     */
    public function run()
    {
        $di = new FactoryDefault;

        $em = new EventsManager;
        $em->enablePriorities(true);

        $config = $this->initConfig();

        $di->setShared('config', $config);

        $this->application = new Application;

        $this->initLogger($di, $config, $em);
        $this->initLoader($di, $config, $em);

        foreach ($this->loaders as $service) {
            $serviceName = ucfirst($service);
            $this->{'init' . $serviceName}($di, $config, $em);
        }

        $di->setShared('eventsManager', $em);

        $this->application->setEventsManager($em);
        $this->application->setDI($di);

        //Update操作允许保存空字符串
        \Phalcon\Mvc\Model::setup([
            'notNullValidations' => false
        ]);

        try {

            return $this->getOutput();

        } catch (\Exception $e) {
            Logger::error($e->getMessage());
            Logger::error($e->getTraceAsString());
            if (config('application.debug')) {
                var_dump($e->getMessage(), $e->getLine(), $e->getFile(), $e->getTraceAsString());
                exit;
            } else {
                $response = new Response();
                $response->redirect('error503');
                $response->send();
            }
        }
    }

    /**
     * Get application output.
     *
     * @return string
     */
    public function getOutput()
    {
        //安全过滤
        if ($_GET) {
            $_GET = safeFilter($_GET);
        }
        if ($_POST) {
            $_POST = safeFilter($_POST);
        }
        if ($_REQUEST) {
            $_REQUEST = safeFilter($_REQUEST);
        }
        if ($_COOKIE) {
            $_COOKIE = safeFilter($_COOKIE);
        }

        if ($this->application instanceof Application) {
            return $this->application->handle()->getContent();
        }

        return $this->application->handle();
    }

    /**
     * Initialize the Logger
     *
     * @param DiInterface $di Dependency Injector
     * @param Config $config App config
     * @param EventsManager $em Events Manager
     */
    protected function initLogger(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->set('logger', function ($filename = null, $format = null) use ($config) {
            $format = $format ?: $config->get('logger')->format;
            $path = rtrim($config->get('logger')->path, '\\/') . DIRECTORY_SEPARATOR;
            if (empty($filename)) {
                $filename = date('Ymd') . '.log';
            } else {
                $path .= trim($filename, '\\/');
                $filename = basename($path) . '-' . date('Ymd') . '.log';
                $path = dirname($path) . DIRECTORY_SEPARATOR;
            }

            is_dir($path) ?: mkdir($path, 0777, true);

            $formatter = new FormatterLine($format, $config->get('logger')->date);
            $logger = new FileLogger($path . $filename, ['model' => 'a+']);

            $logger->setFormatter($formatter);
            $logger->setLogLevel($config->get('logger')->logLevel);

            return $logger;
        });
    }

    /**
     * Initialize the Loader
     *
     * @param DiInterface $di Dependency Injector
     * @param Config $config App config
     * @param EventsManager $em Events Manager
     *
     * @return Loader
     */
    protected function initLoader(DiInterface $di, Config $config, EventsManager $em)
    {
        $loader = new Loader;
        $loader->registerNamespaces([
                'App\Core' => $config->get('application')->coreDir,
                'App\Controllers' => $config->get('application')->controllersDir,
                'App\Models' => $config->get('application')->modelsDir,
                'App\Services' => $config->get('application')->servicesDir,
                'App\Cache' => $config->get('application')->cacheDir,
                'App\Libs' => $config->get('application')->libsDir
            ]
        );

        $loader->setEventsManager($em);
        $loader->register();

        $di->setShared('loader', $loader);

        return $loader;
    }

    /**
     * Initialize the Cache
     *
     * @param DiInterface $di Dependency Injector
     * @param Config $config App config
     * @param EventsManager $em Events Manager
     */
    protected function initCache(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->set('modelsMetadata', function () use ($di, $config) {
            if ($config->application->debug) {
                $metaData = new MemoryMetaData();
            } else {
                $options = [
                    'prefix' => $config->application->redisPrefix,
                    'lifetime' => 86400,
                    'host' => $config->redis->cache->host,
                    'port' => $config->redis->cache->port,
                    'persistent' => false
                ];
                if(!empty($config->redis->cache->auth)) {
                    $options['auth'] = $config->redis->cache->auth;
                }
                if (!empty($config->redis->cache->index)) {
                    $options['index'] = $config->redis->cache->index;
                }
                $metaData = new RedisMetaData($options);
            }
            return $metaData;
        });
    }

    /**
     * Initialize the Security Service.
     *
     * @param DiInterface $di Dependency Injector
     * @param Config $config App config
     * @param EventsManager $em Events Manager
     */
    protected function initSecurity(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('security', function () {
            $security = new Security;
            $security->setWorkFactor(12);

            return $security;
        });
    }

    /**
     * Initialize the Session Service
     *
     * @param DiInterface $di Dependency Injector
     * @param Config $config App config
     * @param EventsManager $em Events Manager
     */
    protected function initSession(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('session', function () use ($config) {
            $adapter = $config->get('session')->adapter;

            /** @var \Phalcon\Session\AdapterInterface $session */
            $session = new $adapter;
            $session->start();

            return $session;
        });
    }

    /**
     * Initialize the Router
     *
     * @param DiInterface $di Dependency Injector
     * @param Config $config App config
     * @param EventsManager $em Events Manager
     */
    protected function initRouter(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('router', function () use ($config, $em) {
            $router = include_once BASE_DIR . '/app/config/routes.php';
            $router->notFound(['controller' => 'error', 'action' => 'show404']);
            return $router;
        });
    }

    /**
     * Initialize the Database connection
     *
     * @param DiInterface $di Dependency Injector
     * @param Config $config App config
     * @param EventsManager $em Events Manager
     */
    protected function initDatabase(DiInterface $di, Config $config, EventsManager $em)
    {
        foreach ($config->database as $dbService => $link) {
            $di->setShared($dbService, function () use ($dbService, $link, $di, $em) {
                $link = $link->toArray();
                $connection = new MyPDO($link);
                $em->attach($dbService, function ($event, $connection) use ($dbService, $di) {
                        if ($event->getType() == 'beforeQuery') {
                            $variables = $connection->getSQLVariables();
                            $string = $connection->getSQLStatement();
                            Logger::debug($dbService . ':SQLStatement:' . $string . '--SQLVariables:' . print_r($variables, true), 'db/db');
                        }
                    }
                );
                $connection->setEventsManager($em);
                return $connection;
            });
        }
    }

    /**
     * Initialize Redis
     *
     * @param DiInterface $di
     * @param Config $config
     * @param EventsManager $em
     */
    protected function initRedis(DiInterface $di, Config $config, EventsManager $em)
    {
        foreach ($config->redis as $connection => $link) {
            $redisService = sprintf('redis%s', ucfirst($connection));
            $di->setShared(
                $redisService,
                function () use ($link, $di) {
                    $redis = new \Redis;
                    $redis->connect($link->host, $link->port, 1);
                    if (isset($link->auth)) {
                        $redis->auth($link->auth);
                    }
                    if (isset($link->index)) {
                        $redis->select($link->index);
                    }
                    return $redis;
                }
            );
        }
    }

    /**
     * Initialize the Url service
     *
     * @param DiInterface $di Dependency Injector
     * @param Config $config App config
     */
    protected function initUrl(DiInterface $di, Config $config)
    {
        $di->setShared('url', function () use ($config) {
            $url = new UrlResolver;
            return $url;
        });
    }

    /**
     * 获取配置
     *
     * @param  string $path Config path [Optional]
     * @return Config
     *
     * @throws \RuntimeException
     */
    protected function initConfig($path = null)
    {
        $path = $path ?: BASE_DIR . '/app/config/';

        //加载全局配置
        if (!is_readable($path . 'config.php')) {
            throw new RuntimeException(
                'Unable to read config from ' . $path . 'config.php'
            );
        }

        $config = include_once $path . 'config.php';

        if (is_array($config)) {
            $config = new Config($config);
        }

        //加载服务器配置
        if (is_readable($path . 'server_config.php')) {
            $override = include_once $path . 'server_config.php';

            if (is_array($override)) {
                $override = new Config($override);
            }

            if ($override instanceof Config) {
                $config->merge($override);
            }
        }

        return $config;
    }

    /**
     * Initialize the Dispatcher.
     *
     * @param DiInterface $di Dependency Injector
     * @param Config $config App config
     */
    protected function initDispatcher(DiInterface $di, Config $config)
    {
        $di->set('dispatcher', function () {
            $eventsManager = new EventsManager();
            $eventsManager->attach("dispatch", function ($event, $dispatcher, $exception) {
                if ($event->getType() == 'beforeException') {
                    switch ($exception->getCode()) {
                        case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $dispatcher->forward([
                                'controller' => 'error',
                                'action' => 'show404'
                            ]);
                            return false;
                        case Dispatcher::EXCEPTION_CYCLIC_ROUTING:
                            $dispatcher->forward([
                                'controller' => 'error',
                                'action' => 'show404'
                            ]);
                            return false;
                    }
                }
            });

            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("App\Controllers");
            $dispatcher->setEventsManager($eventsManager);
            return $dispatcher;
        });
    }

    /**
     * Initialize the View.
     *
     * @param DiInterface $di Dependency Injector
     * @param Config $config App config
     */
    protected function initView(DiInterface $di, Config $config)
    {
        $di->set('view', function () use ($di, $config) {
            $view = new View;
            $view->registerEngines([
                // Setting up Volt Engine
                '.volt' => function ($view, $di) use ($config) {
                    $volt = new VoltEngine($view, $di);
                    $voltConfig = $config->get('volt')->toArray();

                    $options = [
                        'compiledPath' => $voltConfig['cacheDir'],
                        'compiledExtension' => $voltConfig['compiledExt'],
                        'compiledSeparator' => $voltConfig['separator'],
                        'compileAlways' => $voltConfig['forceCompile'],
                    ];

                    $volt->setOptions($options);

                    $compiler = $volt->getCompiler();

                    $compiler->addFunction('truncation', function ($resolvedArgs) {
                        return 'truncation(' . $resolvedArgs . ')';
                    });

                    return $volt;
                }
            ]);

            $view->setViewsDir($config->get('application')->viewsDir);

            $view->disableLevel([View::LEVEL_MAIN_LAYOUT => true, View::LEVEL_LAYOUT => true]);

            $eventsManager = new EventsManager();

            $eventsManager->attach('view', function ($event, $view) use ($di, $config) {
                Logger::debug(sprintf('Event %s. Path: %s', $event->getType(), $view->getActiveRenderPath()), 'view/view');

                if ('notFoundView' == $event->getType()) {
                    $message = sprintf('View not found: %s', $view->getActiveRenderPath());
                    Logger::error($message);
                    throw new ViewException($message);
                }
            });

            $view->setEventsManager($eventsManager);

            return $view;
        });
    }
}
