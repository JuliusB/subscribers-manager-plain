<?php


namespace MailerTiny\Models;


class Subscriber extends Model
{
    protected $fillable
        = [
            'email',
            'name',
            'state',
            'created_at',
        ];

    protected $relations
        = [
            'fields',
        ];

    /**
     * @param array $fields
     */
    public function addFields(array $fields)
    {
        $this->setAttribute('fields', $fields);
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        $fields = $this->getAttribute('fields');

        if (is_array($fields)) {
            /** @var Field $field */
            foreach ($fields as $key => $field) {
                $this->attributes['fields'][$key] = $field->getAttributes();
            }
        }

        return ['data' => $this->attributes];
    }
}