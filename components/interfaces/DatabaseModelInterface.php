<?php
/**
 * Created by PhpStorm.
 * User: sanzhikee
 * Date: 2018-12-09
 * Time: 22:12
 */

namespace App\components\interfaces;

interface DatabaseModelInterface
{
    /**
     * @param mixed ...$condition
     * @return mixed
     */
    public function getOne(...$condition);

    /**
     * @param mixed ...$condition
     * @return mixed
     */
    public function getAll(...$condition);

    /**
     * @param mixed ...$condition
     * @return mixed
     */
    public function delete(...$condition);

    /**
     * @param $data
     * @return mixed
     */
    public function insert($data);
}