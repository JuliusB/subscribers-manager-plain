<?php

namespace MailerTiny\Core;

use PDO;

class DB
{
    /** @var PDO */
    private $pdo;

    /** @var DB */
    private static $instance;

    /** @return DB */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Private construct for singleton design pattern
     */
    private function __construct()
    {
        $host = Application::getConfig('host');
        $dbName = Application::getConfig('db_name');
        $username = Application::getConfig('user');
        $password = Application::getConfig('password');

        $this->pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4",
            $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @return PDO
     */
    public static function pdo()
    {
        return self::instance()->pdo;
    }

    /**
     * @param string $table
     *
     * @return array
     */
    public function fetchAll(string $table)
    {
        $statement = $this->pdo->query('select * from '.$table);

        return $statement->fetchAll();
    }

    /**
     * @param string $table
     * @param array  $conditions
     *
     * @return array
     */
    public function fetchAllByConditions(string $table, array $conditions)
    {
        list($where, $values) = $this->convertConditions($conditions);

        $query = 'SELECT *  FROM '.$table.' WHERE '.$where;
        $statement = $this->pdo->prepare($query);

        $statement->execute($values);

        return $statement->fetchAll();
    }

    /**
     * @param string $table
     * @param string $field
     * @param string $value
     *
     * @return mixed
     */
    public function fetchRowByField(string $table, string $field, string $value)
    {
        return $this->pdo->query('SELECT * FROM '.$table.' WHERE '
            .$field.' = '.$value)->fetch();
    }

    /**
     * @param string $table
     * @param array  $data
     *
     * @return string
     */
    public function insertData(string $table, array $data)
    {
        $this->pdo->beginTransaction();
        $cols = implode(', ', array_keys($data));

        $parameters = [];
        foreach ($data as $name => $val) {
            $parameters[':'.$name] = $val;
        }
        $values = implode(", ", array_keys($parameters));
        $query = 'INSERT INTO '.$table.' ('.$cols.') VALUES ('.$values
            .')';
        $preparedQuery = $this->pdo->prepare($query);
        $preparedQuery->execute($parameters);
        $insertId = $this->pdo->lastInsertId();
        $this->pdo->commit();

        return $insertId;
    }

    /**
     * @param string $table
     * @param array  $data
     * @param array  $conditions
     */
    public function updateRow(
        string $table,
        array $data,
        array $conditions
    ) {
        $parameters = $cols = $values = [];
        foreach ($data as $name => $val) {
            if (in_array($name, array_keys($conditions))) {
                continue;
            }
            $parameters[$name.'=:'.$name] = $val;
            $values[$name] = $val;
        }
        $cols = implode(', ', array_keys($parameters));
        list($where, $values) = $this->convertConditions($conditions, $values);

        $query = 'UPDATE '.$table.' SET '.$cols.' WHERE '
            .$where;

        $preparedQuery = $this->pdo->prepare($query);
        $preparedQuery->execute($values);
    }

    /**
     * @param string $table
     * @param string $idField
     * @param string $idFieldValue
     *
     * @return bool
     */
    public function deleteRow(
        string $table,
        string $idField,
        string $idFieldValue
    ) {
        $query = 'DELETE FROM '.$table.' WHERE '.$idField.'=:'
            .$idField;
        $values[$idField] = $idFieldValue;
        $statement = $this->pdo->prepare($query);

        return $statement->execute($values);
    }

    /**
     * @param string $table
     * @param array  $conditions
     *
     * @return bool
     */
    public function exists(string $table, array $conditions)
    {
        list($where, $values) = $this->convertConditions($conditions);

        $query = 'SELECT *  FROM '.$table.' WHERE '.$where;
        $statement = $this->pdo->prepare($query);

        $statement->execute($values);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return !empty($row);
    }

    /**
     * @param array $conditions
     * @param array $values
     *
     * @return array
     */
    protected function convertConditions(array $conditions, array $values = [])
    {
        $where = [];
        foreach ($conditions as $condKey => $condVal) {
            $values[$condKey] = $condVal;
            $where[] = $condKey.'=:'.$condKey;
        }
        $where = implode(' AND ', $where);

        return [
            $where,
            $values,
        ];
    }
}