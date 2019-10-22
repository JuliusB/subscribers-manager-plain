<?php


namespace MailerTiny\Models;


class Field extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'title',
            'type',
            'tag',
            'value'
        ];

    /**
     * @param string $tag
     */
    public function setTag(string $tag)
    {
        $this->setAttribute('tag', $tag);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getAttribute('title');
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->setAttribute('value', $value);
    }
}