<?php


namespace MailerTiny\Http\Routing;


use MailerTiny\Core\Application;
use MailerTiny\Core\Request;
use MailerTiny\Http\Controllers\API\FieldsController;
use MailerTiny\Http\Controllers\API\SubscribersController;
use MailerTiny\Http\Controllers\HomeController;
use MailerTiny\Http\Requests\StoreField;
use MailerTiny\Http\Requests\StoreSubscriber;
use MailerTiny\Http\Requests\UpdateSubscriber;

class Router
{
    /** @var Router */
    private static $instance;

    /** @var Action[][] */
    private $routes;

    /** @var Request */
    private $request;

    /** @return Router */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Empty private construct for singleton design pattern
     */
    private function __construct()
    {
        $this->request = Application::getService(Request::class);

        $this->registerRoutes();
    }

    /**
     * @param        $routePath
     * @param Action $action
     *
     * @return $this
     */
    public function add(
        $routePath,
        Action $action
    ) {
        $this->routes[$routePath][] = $action;

        return $this;
    }

    /**
     * @return null|Action
     */
    public function findAction()
    {
        $requestRoute = explode('?', $this->request->getRequestUri())[0];
        preg_match('/\d+/', $requestRoute, $matches);
        $requestRoute = preg_replace('/\/\d+/', '/{id}', $requestRoute);

        if (
            isset($this->routes[$requestRoute])
        ) {
            foreach ($this->routes[$requestRoute] as $action) {
                if (strtolower($this->request->getHttpMethod())
                    == strtolower($action->getHttpMethod())
                ) {
                    if (isset($matches[0])) {
                        $action->setArgument($matches[0]);
                    }
                    return $action;
                }
            }
        }

        return null;
    }

    /**
     * @return void
     */
    private function registerRoutes(): void
    {
        $this->add(
            '/',
            new Action(
                'get',
                HomeController::class,
                'index'
            )
        );

        $this->add(
            '/api/subscribers',
            new Action(
                'get',
                SubscribersController::class,
                'index'
            )
        );

        $this->add(
            '/api/subscribers',
            new Action(
                'post',
                SubscribersController::class,
                'store',
                new StoreSubscriber()
            )
        );

        $this->add(
            '/api/subscribers/{id}',
            new Action(
                'get',
                SubscribersController::class,
                'show'
            )
        );

        $this->add(
            '/api/subscribers/{id}',
            new Action(
                'put',
                SubscribersController::class,
                'update',
                new UpdateSubscriber()
            )
        );

        $this->add(
            '/api/subscribers/{id}',
            new Action(
                'delete',
                SubscribersController::class,
                'destroy'
            )
        );

        $this->add(
            '/api/fields',
            new Action(
                'get',
                FieldsController::class,
                'index'
            )
        );

        $this->add(
            '/api/fields',
            new Action(
                'post',
                FieldsController::class,
                'store',
                new StoreField()
            )
        );

        $this->add(
            '/api/fields/{id}',
            new Action(
                'get',
                FieldsController::class,
                'show'
            )
        );

        $this->add(
            '/api/fields/{id}',
            new Action(
                'put',
                FieldsController::class,
                'update',
                new UpdateSubscriber()
            )
        );

        $this->add(
            '/api/fields/{id}',
            new Action(
                'delete',
                FieldsController::class,
                'destroy'
            )
        );
    }

}