<?php

namespace MailerTiny\Repositories;

use MailerTiny\Core\DB;
use MailerTiny\Models\Field;
use MailerTiny\Models\Subscriber;

/**
 * Class FieldsRepository
 *
 * @method Field getById($id)
 *
 * @package MailerTiny\Repositories
 */
class FieldsRepository extends BaseRepository
{
    /** @var string  */
    protected $table = 'fields';

    /** @var string  */
    protected $model = Field::class;

    /**
     * @param Subscriber $subscriber
     *
     * @return array
     */
    public function getBySubscriber(Subscriber $subscriber)
    {
        $rows = DB::instance()->fetchAllByConditions('subscriber_fields', [
            'subscriber_id' => $subscriber->getIdAttribute(),
        ]);
        $fields = [];
        foreach ($rows as $row) {
            $field = $this->getById($row['field_id']);
            $field->setValue($row['value']);
            $fields[] = $field;
        }

        return $fields;
    }
}