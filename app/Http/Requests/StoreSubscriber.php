<?php


namespace MailerTiny\Http\Requests;


use MailerTiny\Components\Subscriber\SubscribersManager;

class StoreSubscriber implements RequestValidatorInterface
{
    public function rules()
    {
        return [
            'email' => [
                'email',
                'required',
                'max:60'
            ],
            'name' => [
                'required',
                'alpha',
                'max:60',
            ],
            'state' => [
                'in:' . implode(',', SubscribersManager::STATES)
            ],
            'fields' => [
                'array'
            ],
        ];
    }
}