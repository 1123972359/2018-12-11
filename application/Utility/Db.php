<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/20/020
 * Time: 23:23
 */

namespace app\Utility;


use think\db\Query;

class Db extends \think\Db
{


    public function __construct(string $tableName)
    {
        return $this->getTable($tableName);
    }

    private function getTable(string $tableName) : Query
    {
        return parent::table($tableName);
    }

    public function batchInsert(string $tableName,array $contribute,array $data) : bool
    {

    }

    public function batchUpdate(string $tableName,array $contribute,array $data) : int
    {

    }

    public function batchDelete(string $tableName) : int
    {

    }

}