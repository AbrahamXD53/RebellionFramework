<?php

namespace Application\Database;

class Finder
{

    public static $sql = '';
    public static $instance = null;
    public static $prefix = '';
    public static $where = array();
    public static $control = ['', '',''];

    public static function clear(){
        self::$sql = '';
        self::$instance = null;
        self::$prefix = '';
        self::$where = array();
        self::$control = ['', '',''];
    }

    //a = table name
    //cols = columns names
    public static function select($a, $cols = null)
    {
        self::clear();
        self::$instance = new Finder();
        if ($cols) {
            self::$prefix = 'SELECT ' . $cols . ' FROM ' . $a;
        } else {
            self::$prefix = 'SELECT * FROM ' . $a;
        }
        return self::$instance;
    }

    public static function info($table){
        self::clear();
        self::$instance = new Finder();
        self::$prefix='SELECT `COLUMN_NAME` as keyName 
        FROM `INFORMATION_SCHEMA`.`COLUMNS`
        WHERE `TABLE_NAME`=' . '\'' . $table . '\'';
        return self::$instance;
    }

    public static function count($a)
    {
        self::clear();
        self::$instance = new Finder();
        self::$prefix = 'SELECT COUNT(*) as count FROM ' . $a;
        return self::$instance;
    }

    public static function where($a = null)
    {
        self::$where[0] = ' WHERE ' . $a;
        return self::$instance;
    }

    public static function like($a, $b)
    {
        self::$where[] = trim($a . ' LIKE ' . $b);
        return self::$instance;
    }

    function  and ($a = null) {
        self::$where[] = trim('AND ' . $a);
        return self::$instance;
    }

    function  or ($a = null) {
        self::$where[] = trim('OR ' . $a);
        return self::$instance;
    }

    public static function in(array $a)
    {
        self::$where[] = 'IN ( ' . implode(',', $a) . ' )';
        return self::$instance;
    }

    public static function not($a = null)
    {
        self::$where[] = trim('NOT ' . $a);
        return self::$instance;
    }

    public static function orderBy($order){
        self::$control[0] = 'ORDER BY ' . $order;
        return self::$instance;
    }

    public static function limit($limit)
    {
        self::$control[1] = 'LIMIT ' . $limit;
        return self::$instance;
    }

    public static function offset($offset)
    {
        self::$control[2] = 'OFFSET ' . $offset;
        return self::$instance;
    }

    public static function getSql():string{
        self::$sql = self::$prefix
            . implode(' ',self::$where)
            . ' ' . implode(' ',self::$control);
        preg_replace('/  /',' ',self::$sql);
        return trim(self::$sql);
    }
}
