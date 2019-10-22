<?php


namespace MailerTiny\Core;

use Exception;
use MailerTiny\Http\Response\EmptyJsonSerializible;
use MailerTiny\Http\Response\HtmlResponse;
use MailerTiny\Http\Response\JsonResponse;
use MailerTiny\Http\Response\ResponseInterface;
use MailerTiny\Http\Routing\Router;

class FrontController
{
    /** @var Router */
    private $router;

    /** @var Request  */
    private $request;

    /**
     * FrontController constructor.
     */
    public function __construct()
    {
        $this->router = Application::getService(Router::class);
        $this->request = Application::getService(Request::class);
    }

    /**
     * @return ResponseInterface
     */
    public function handle()
    {
        $action = $this->router->findAction();

        if (!$action) {
            if ($this->request->isAjax()) {
                return new JsonResponse(new EmptyJsonSerializible([
                    'Route not found'
                ]), 404);

            } else {
                header('Location: ' . '/');
                die;
            }
        }

        try {
            $response = $action->execute();
        } catch (Exception $e) {
            if ($this->request->isAjax()) {
                return new JsonResponse(new EmptyJsonSerializible([
                    'Whoops, something wrong happened'
                ]), 500);
            }
            return new HtmlResponse('500');
        }

        return $response;
    }
}