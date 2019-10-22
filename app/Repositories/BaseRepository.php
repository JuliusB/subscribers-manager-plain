<?php


namespace MailerTiny\Repositories;


use MailerTiny\Core\DB;
use MailerTiny\Models\Collection;
use MailerTiny\Models\Model;
use PDO;

abstract class BaseRepository
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var string
     */
    protected $table;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $this->pdo = DB::pdo();
    }

    /**
     * @return Collection
     */
    public function all()
    {
        $rows = DB::instance()->fetchAll($this->table);
        $collection = [];
        foreach ($rows as $row) {
            $collection[] = new $this->model($row);
        }
        $collection = new Collection($collection);

        return $collection;
    }

    /**
     * @param $id
     *
     * @return Model
     */
    public function getById($id)
    {
        /** @var Model $model */
        $model = new $this->model();

        $data = DB::instance()
            ->fetchRowByField($this->table, $model->getIdField(), $id);

        if ($data) {
            return $model->fill($data);
        }

        return null;
    }

    /**
     * @param Model $model
     *
     * @return Model
     */
    public function save(Model $model)
    {
        if ($model->getIdAttribute()) {
            return $this->update($model);
        }

        return $this->saveNew($model);
    }

    /**
     * @param Model $model
     *
     * @return Model
     */
    private function saveNew(Model $model)
    {
        $insertId = DB::instance()
            ->insertData($this->table, $model->getAttributes());
        $model->setIdAttribute($insertId);

        return $model;
    }

    /**
     * @param Model $model
     *
     * @return Model
     */
    private function update(Model $model)
    {
        DB::instance()
            ->updateRow($this->table, $model->getAttributes(),
                [$model->getIdField() => $model->getIdAttribute()]);

        return $model;
    }

    /**
     * @param Model $model
     *
     * @return bool
     */
    public function deleteModel(Model $model)
    {
        return DB::instance()
            ->deleteRow($this->table, $model->getIdField(),
                $model->getIdAttribute());
    }

}