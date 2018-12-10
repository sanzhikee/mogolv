<?php
/**
 * Created by PhpStorm.
 * User: sanzhikee
 * Date: 2018-12-09
 * Time: 22:08
 */

namespace App\components;

abstract class Model
{
    /**
     * @var \MysqliDb
     */
    public $db;
    /**
     * @var string
     */
    public $tableName;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $config = require(__DIR__ . '/../config/main.php');
        $this->db = new \MysqliDb($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['databaseName']);
    }

    /**
     * @param mixed ...$condition
     * @return array
     * @throws \Exception
     */
    public function getOne(...$condition)
    {
        if (empty($condition)) {
            return $this->db->ObjectBuilder()->getOne($this->tableName);
        }
        return $this->db->where(...$condition)->ObjectBuilder()->getOne($this->tableName);
    }

    /**
     * @param mixed ...$condition
     * @return array|\MysqliDb
     * @throws \Exception
     */
    public function getAll(...$condition)
    {
        if (empty($condition)) {
            return $this->db->ObjectBuilder()->get($this->tableName);
        }
        return $this->db->where(...$condition)->ObjectBuilder()->get($this->tableName);
    }

    /**
     * @param mixed ...$condition
     * @return bool
     * @throws \Exception
     */
    public function delete(...$condition)
    {
        return $this->db->where(...$condition)->delete($this->tableName);
    }

    /**
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function insert($data)
    {
        return $this->db->insert($this->tableName, $data);
    }
}