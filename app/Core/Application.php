<?php

namespace MailerTiny\Core;

use Exception;
use MailerTiny\Http\Response\ResponseInterface;
use MailerTiny\Http\Routing\Router;

class Application
{

    /**
     * Global application container
     *
     * @var array
     */
    protected $container = [];

    /** @var Application */
    private static $instance;

    /** @var array */
    private $config;

    /** @return Application */
    public static function app()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Start this amazing app
     */
    public static function start($config)
    {
        self::app()->setConfig($config);
        self::initBaseServices();
        $response = self::handleRequest();
        self::handleResponse($response);
    }

    /**
     * Private construct for singleton design pattern
     */
    private function __construct()
    {
    }

    /**
     * @param string $serviceName
     * @param object $serviceInstance
     */
    private static function register(string $serviceName, $serviceInstance)
    {
        self::app()->container[$serviceName] = $serviceInstance;
    }

    /**
     * Register services to app container
     */
    private static function initBaseServices()
    {
        self::register(Request::class, new Request());
        self::register(Router::class, Router::instance());
    }

    /**
     * @return ResponseInterface
     */
    private static function handleRequest()
    {
        $frontController = new FrontController();
        return $frontController->handle();
    }


    /**
     * @param string $serviceName
     *
     * @return mixed
     */
    public static function getService(string $serviceName)
    {
        return self::app()->container[$serviceName];
    }

    /**
     * @param ResponseInterface $response
     */
    private static function handleResponse(ResponseInterface $response)
    {
        $response->render();
    }

    /**
     * @param $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public static function getConfig(string $key):string
    {
        if (isset(self::app()->config[$key])) {
            return self::app()->config[$key];
        }
        return '';
    }

}