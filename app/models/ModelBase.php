<?php

namespace App\Models;

abstract class ModelBase extends \Phalcon\Mvc\Model
{
    public static function getTableName()
    {
        $prefix = config('application.dbPrefix');
        return $prefix . static::$tableName;
    }
}