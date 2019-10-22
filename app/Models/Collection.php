<?php

namespace MailerTiny\Models;

use JsonSerializable;

class Collection implements JsonSerializable
{

    /**
     * @var array
     */
    private $items;

    /**
     * Collection constructor.
     *
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $data['data'] = [];
        foreach ($this->items as $item) {
            $data['data'][] = $item->jsonSerialize()['data'];
        }

        /** @TODO make working pagination */
        $data['links'] = [];
        $data['meta'] = [
            "total" => count($this->items),
        ];

        return $data;
    }
}