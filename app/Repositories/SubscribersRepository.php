<?php


namespace MailerTiny\Repositories;


use MailerTiny\Core\DB;
use MailerTiny\Models\Field;
use MailerTiny\Models\Subscriber;

/**
 * Class SubscribersRepository
 *
 * @method Subscriber getById($id)
 *
 * @package MailerTiny\Repositories
 */
class SubscribersRepository extends BaseRepository
{
    /** @var string  */
    protected $table = 'subscriber';

    /** @var string  */
    protected $model = Subscriber::class;

    /**
     * @param Subscriber $subscriber
     * @param Field      $field
     * @param string     $value
     */
    public function assignField(
        Subscriber $subscriber,
        Field $field,
        string $value
    ) {
        $exists = DB::instance()->exists('subscriber_fields', [
            'subscriber_id' => $subscriber->getIdAttribute(),
            'field_id' => $field->getIdAttribute(),
        ]);
        if ($exists) {
            DB::instance()
                ->updateRow('subscriber_fields', ['value' => $value], [
                    'subscriber_id' => $subscriber->getIdAttribute(),
                    'field_id' => $field->getIdAttribute(),
                ]);

            return;
        }
        DB::instance()->insertData('subscriber_fields', [
            'subscriber_id' => $subscriber->getIdAttribute(),
            'field_id' => $field->getIdAttribute(),
            'value' => $value,
        ]);
    }
}