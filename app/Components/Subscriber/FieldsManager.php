<?php

namespace MailerTiny\Components\Subscriber;

use MailerTiny\Models\Collection;
use MailerTiny\Models\Field;
use MailerTiny\Repositories\FieldsRepository;

class FieldsManager
{
    const TYPE_STRING = 'string';
    const TYPE_DATE = 'date';
    const TYPE_NUMBER = 'number';
    const TYPE_BOOLEAN = 'boolean';

    const TYPES
        = [
            self::TYPE_DATE,
            self::TYPE_NUMBER,
            self::TYPE_STRING,
            self::TYPE_BOOLEAN,
        ];


    /**
     * @var FieldsRepository
     */
    private $fieldsRepo;

    /**
     * FieldsManager constructor.
     */
    public function __construct()
    {
        $this->fieldsRepo = new FieldsRepository();
    }

    /**
     * @param array $data
     *
     * @return Field
     */
    public function createField(array $data): Field
    {
        $field = new Field();

        $field->fill($data);
        $field->setTag($this->slug($field->getTitle()));

        $this->fieldsRepo->save($field);

        return $field;
    }

    /**
     * @param $id
     *
     * @return Field
     */
    public function findFieldById($id): Field
    {
        return $this->fieldsRepo->getById($id);
    }

    /**
     * @param       $id
     * @param array $data
     *
     * @return Field
     */
    public function updateField($id, array $data): Field
    {
        $field = $this->findFieldById($id);

        $field->fill($data);

        $this->fieldsRepo->save($field);

        return $field;
    }

    /**
     * @param $id
     */
    public function deleteField($id): void
    {
        $field = $this->findFieldById($id);

        $this->fieldsRepo->deleteModel($field);
    }

    /**
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->fieldsRepo->all();
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string $title
     * @param string $separator
     *
     * @return string
     */
    public function slug($title, $separator = '-'): string
    {

        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator,
            $title);

        // Replace @ with the word 'at'
        $title = str_replace('@', $separator.'at'.$separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '',
            strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator,
            $title);

        return trim($title, $separator);
    }

}