<?php


namespace MailerTiny\Http\Routing;


use Exception;
use MailerTiny\Core\Application;
use MailerTiny\Core\Request;
use MailerTiny\Core\Validator;
use MailerTiny\Exceptions\InvalidInputException;
use MailerTiny\Http\Requests\RequestValidatorInterface;
use MailerTiny\Http\Response\JsonResponse;
use MailerTiny\Http\Response\ResponseInterface;

class Action
{
    /**
     * @var string
     */
    private $httpMethod;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $action;

    /**
     * @var RequestValidatorInterface
     */
    private $requestValidator;

    /**
     * @var string
     */
    private $argument;

    public function __construct(
        string $httpMethod,
        string $controller,
        string $action,
        RequestValidatorInterface $requestValidator = null
    ) {
        $this->httpMethod = $httpMethod;
        $this->controller = $controller;
        $this->action = $action;
        $this->requestValidator = $requestValidator;
    }

    /**
     * @return ResponseInterface
     */
    public function execute()
    {
        try {
            $request = $this->validateRequest();
        } catch (InvalidInputException $e) {
            return new JsonResponse($e);
        }

        return $this->executeController($request);
    }

    /**
     * @return Request
     * @throws InvalidInputException
     */
    private function validateRequest()
    {
        /** @var Request $request */
        $request = Application::getService(Request::class);
        if ($this->requestValidator) {
            $rules = $this->requestValidator->rules();

            $request->filterOutInput(array_keys($rules));

            $validator = new Validator();

            $validator->validateRequiredFields($request->inputs(), $rules);

            $errorMessages = [];
            foreach ($request->inputs() as $name => $input) {
                try {
                    if (!isset($rules[$name])) {
                        throw new InvalidInputException('Input ' . $name . ' is not allowed');
                    }
                    $validator->validate($input, $rules[$name]);
                } catch (InvalidInputException $e) {
                    $errorMessages[] = $e->getMessage();
                }
            }

            if (!empty($errorMessages)) {
                $exception = new InvalidInputException();
                $exception->addErrorMessages($errorMessages);
                throw $exception;
            }
        }

        return $request;
    }

    /**
     * @param Request $request
     *
     * @return ResponseInterface
     */
    private function executeController(Request $request)
    {
        if ($this->argument) {
            return (new $this->controller())->{$this->action}($request, $this->argument);
        }

        return (new $this->controller())->{$this->action}($request);
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @param string $argument
     */
    public function setArgument(string $argument)
    {
        $this->argument = $argument;
    }
}