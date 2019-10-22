<?php


namespace MailerTiny\Http\Controllers;


use MailerTiny\Core\Request;
use MailerTiny\Http\Response\HtmlResponse;

class HomeController
{
    /**
     * @param Request $request
     *
     * @return HtmlResponse
     */
    public function index(Request $request)
    {
        return new HtmlResponse('home');
    }

}