<?php


namespace MailerTiny\Models;

abstract class Model implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var string
     */
    protected $idField = 'id';

    /**
     * @var array
     */
    protected $hasMany = [];

    /**
     * Model constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->assignAttributes($data);
    }

    /**
     * @param string $attribute
     * @param        $val
     */
    protected function setAttribute(string $attribute, $val)
    {
        if (in_array($attribute, array_merge($this->fillable, $this->relations)) || $attribute == $this->idField) {
            $this->attributes[$attribute] = $val;
        }
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $data
     */
    private function assignAttributes($data)
    {
        foreach ($data as $attribute => $val) {
            $this->setAttribute($attribute, $val);
        }
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function fill(array $data)
    {
        foreach ($data as $attribute => $val) {
            if (in_array($attribute, $this->fillable) || $attribute == $this->idField) {
                $this->attributes[$attribute] = $val;
            }
        }

        return $this;
    }

    /**
     * @param $id
     */
    public function setIdAttribute($id)
    {
        $this->setAttribute($this->getIdField(), $id);
    }

    /**
     * @return mixed|null
     */
    public function getIdAttribute()
    {
        return $this->getAttribute($this->getIdField());
    }

    /**
     * @param string $attr
     *
     * @return mixed|null
     */
    public function getAttribute(string $attr)
    {
        if (isset($this->getAttributes()[$attr])) {
            return $this->getAttributes()[$attr];
        }
        return null;
    }

    /**
     * @return string
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * @param string $class
     * @param string $table
     * @param string $localKey
     * @param string $foreignKey
     * @param string $pivot
     */
    protected function hasMany(
        string $class,
        string $table,
        string $localKey,
        string $foreignKey,
        string $pivot = ''
    ) {
        $this->hasMany[$class] = [
            'table' => $table,
            'local_key' => $localKey,
            'foreign_key' => $foreignKey,
            'pivot' => $pivot
        ];
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
        return ['data' => $this->attributes];
    }
}