<?php


namespace MailerTiny\Http\Requests;


use MailerTiny\Components\Subscriber\FieldsManager;

class StoreField implements RequestValidatorInterface
{
    public function rules()
    {
        return [
            'title' => [
                'required',
                'max:60'
            ],
            'type' => [
                'required',
                'in:' . implode(',', FieldsManager::TYPES)
            ]
        ];
    }
}