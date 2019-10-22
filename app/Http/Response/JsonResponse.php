<?php

namespace MailerTiny\Http\Response;

use JsonSerializable;

class JsonResponse implements ResponseInterface
{
    protected $code;
    /**
     * @var JsonSerializable
     */
    private $serializable;

    /**
     * JsonResponse constructor.
     *
     * @param JsonSerializable $serializable
     * @param                  $code
     */
    public function __construct(JsonSerializable $serializable, $code = 200)
    {
        $this->serializable = $serializable;
        $this->code = $code;
    }

    /**
     *
     */
    public function render()
    {
        http_response_code($this->code);
        header('Content-Type: application/json');
        print json_encode($this->serializable->jsonSerialize());
    }
}