<?php

namespace App\Core;

/**
 * @Desc MySQL PDO封装
 * @Author tangqin
 * @Date 2016/9/28
 * @Time 20:31
 * @Demo
 * insert('test',['aa','11'],['name','num'])
 *
 * update('test',['name','num'],['bb','22'],[
 *      'conditions' => 'id = ?',
 *      'bind' => [1]
 * ]);
 *
 * delete('test', 'id = ?', [1])
 *
 * incr('test',['num'],[1],[
 *      'conditions' => 'id = ?',
 *      'bind' => [1]
 * ]);
 *
 * insertBatch('test',[['name'=>'aa','num'=>'11'],['name'=>'bb','num'=>'22'])
 *
 */
class MyPDO extends \Phalcon\Db\Adapter\Pdo\Mysql
{

    protected static $trans_depth = 0;

    protected static $rollback_tag = false;

    /**
     * 增加/减少
     * @param $table
     * @param $fields
     * @param $values
     * @param null $whereCondition
     * @param null $dataTypes
     * @return mixed
     */
    public function incr($table, $fields, $values, $whereCondition = null, $dataTypes = null)
    {
        $sets = [];
        foreach ($fields as $key => $field) {
            $val = intval($values[$key]);
            if ($val >= 0) {
                $val = '+' . $val;
            }
            $sets[] = '`' . $field . '` = `' . $field . '`' . $val;
        }
        $sql = 'UPDATE `%s` SET %s WHERE %s';
        $conditions = isset($whereCondition['conditions']) ? $whereCondition['conditions'] : '';
        $bind = isset($whereCondition['bind']) ? $whereCondition['bind'] : [];
        $sql = sprintf($sql, $table, implode(', ', $sets), $conditions);
        $res = $this->execute($sql, $bind, $dataTypes);
        return $res;
    }

    /**
     * 批量插入
     * @param $table
     * @param $data
     * @return bool
     */
    public function insertBatch($table, $data)
    {
        if(count($data) == 0) {
            return true;
        }
        $fields = [];
        foreach ($data[0] as $k => $v) {
            $fields[] = $k;
        }
        $insert_data = [];
        foreach ($data as $arr) {
            foreach ($arr as &$val) {
                if ($val === null) {
                    $val = 'null';
                } else {
                    $val = $this->escapeString($val);
                }
            }
            $insert_data[] = '(' . implode(', ', array_values($arr)) . ')';
        }
        $sql = 'INSERT INTO %s (%s) VALUES %s';
        $res = $this->execute(sprintf(
            $sql,
            $table,
            '`' . implode('`, `', $fields) . '`',
            implode(', ', $insert_data)
        ));
        return $res;
    }

    /**
     * 插入操作并返回主键ID
     * @param $table
     * @param $values
     * @param null $fields
     * @param null $dataTypes
     * @return bool|int
     */
    public function insertAndGetId($table, $values, $fields = null, $dataTypes = null)
    {
        $res = $this->insert($table, $values, $fields, $dataTypes);
        if ($res) {
            return $this->lastInsertId();
        }

        return false;
    }

    /**
     * 事务开始
     * @param bool $nesting
     */
    public function begin($nesting = true)
    {
        if (self::$trans_depth) {
            self::$trans_depth += 1;
            return;
        }
        self::$trans_depth = 1;
        parent::begin($nesting);
    }

    /**
     * 事务回滚
     * @param bool $nesting
     */
    public function rollback($nesting = true)
    {
        self::$rollback_tag = true;

        if (self::$trans_depth > 1) {
            self::$trans_depth -= 1;
            return;
        }
        self::$trans_depth = 0;

        parent::rollback($nesting);
    }

    /**
     * 事务提交
     * @param bool $nesting
     */
    public function commit($nesting = true)
    {
        if (self::$trans_depth > 1) {
            self::$trans_depth -= 1;
            return;
        }
        self::$trans_depth = 0;

        self::$rollback_tag ? parent::rollback($nesting) : parent::commit($nesting);
    }
}