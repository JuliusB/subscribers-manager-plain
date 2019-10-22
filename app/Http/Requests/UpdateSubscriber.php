<?php


namespace MailerTiny\Http\Requests;


use MailerTiny\Core\Request;

class UpdateSubscriber implements RequestValidatorInterface
{
    public function rules()
    {
        return [
            'name' => [
                'alpha',
                'max:60',
            ],
            'state' => [
                'in:'
            ],
            'fields' => [
                'array'
            ],
        ];
    }
}