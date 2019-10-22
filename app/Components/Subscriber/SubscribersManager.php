<?php

namespace MailerTiny\Components\Subscriber;

use MailerTiny\Models\Collection;
use MailerTiny\Models\Subscriber;
use MailerTiny\Repositories\FieldsRepository;
use MailerTiny\Repositories\SubscribersRepository;

class SubscribersManager
{
    const STATE_ACTIVE = 'active';
    const STATE_UNSUBSCRIBE = 'unsubscribe';
    const STATE_JUNK = 'junk';
    const STATE_BOUNCED = 'bounced';
    const STATE_UNCONFIRMED = 'unconfirmed';

    const STATES
        = [
            self::STATE_ACTIVE,
            self::STATE_BOUNCED,
            self::STATE_JUNK,
            self::STATE_UNCONFIRMED,
            self::STATE_UNSUBSCRIBE,
        ];
    /**
     * @var SubscribersRepository
     */
    private $subscribersRepo;

    /**
     * @var FieldsRepository
     */
    private $fieldsRepo;

    /**
     * SubscribersManager constructor.
     */
    public function __construct()
    {
        $this->subscribersRepo = new SubscribersRepository();
        $this->fieldsRepo = new FieldsRepository();
    }

    /**
     * @param array $data
     *
     * @return Subscriber
     */
    public function createSubscriber(array $data): Subscriber
    {
        $subscriber = new Subscriber();
        // make subscriber active by default
        if (!isset($data['state'])) {
            $data['state'] = self::STATE_ACTIVE;
        }
        $fields = $data['fields'];
        unset($data['fields']);
        $subscriber->fill($data);

        $this->subscribersRepo->save($subscriber);

        if ($fields) {
            $this->attachFieldsToSubscriber($subscriber, $fields);
        }

        return $subscriber;
    }

    /**
     * @param Subscriber $subscriber
     * @param            $fields
     */
    private function attachFieldsToSubscriber(Subscriber $subscriber, $fields)
    {
        foreach ($fields as $fieldData) {
            if (isset($fieldData['value'])) {
                $field = $this->fieldsRepo->getById($fieldData['id']);
                $this->subscribersRepo->assignField($subscriber, $field,
                    $fieldData['value']);
            }
        }
    }

    /**
     * @param      $id
     * @param bool $withFields
     *
     * @return Subscriber
     */
    public function findSubscriberById($id, bool $withFields = true)
    {
        $subscriber = $this->subscribersRepo->getById($id);

        if ($withFields) {
            $subscriber->addFields($this->fieldsRepo->getBySubscriber($subscriber));
        }

        return $subscriber;
    }

    /**
     * @param       $id
     * @param array $data
     *
     * @return null
     */
    public function updateSubscriber($id, array $data)
    {
        $subscriber = $this->findSubscriberById($id, false);

        $subscriber->fill($data);

        $this->subscribersRepo->save($subscriber);

        if (isset($data['fields'])) {
            $this->attachFieldsToSubscriber($subscriber, $data['fields']);
        }

        return $subscriber;
    }

    /**
     * @param $id
     */
    public function deleteSubscriber($id)
    {
        $subscriber = $this->findSubscriberById($id);

        $this->subscribersRepo->deleteModel($subscriber);
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        $subscribersCollection = $this->subscribersRepo->all();

        /** @var Subscriber $subscriber */
        foreach ($subscribersCollection->getItems() as $subscriber) {
            $fields = $this->fieldsRepo->getBySubscriber($subscriber);
            $subscriber->addFields($fields);
        }

        return $subscribersCollection;
    }

}