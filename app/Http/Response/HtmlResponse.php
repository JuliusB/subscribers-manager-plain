<?php


namespace MailerTiny\Http\Response;


class HtmlResponse implements ResponseInterface
{

    /**
     * @var string
     */
    private $viewFile;

    public function __construct(string $viewFile)
    {
        $this->viewFile = $viewFile;
    }

    /**
     * @void
     */
    public function render()
    {
        require $_SERVER['DOCUMENT_ROOT'] . '/../resources/views/' . $this->viewFile . '.html';
    }
}