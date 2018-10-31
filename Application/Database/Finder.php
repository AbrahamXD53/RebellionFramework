<?php

namespace Application\Database;

use Application\Database\Connection;

class Finder
{

    private static $sql = '';
    private static $instance = null;
    private static $prefix = '';
    private static $where =[];
    private static $join = [];
    private static $control = ['', '', ''];

    public static function clear()
    {
        self::$sql = '';
        self::$instance = null;
        self::$prefix = '';
        self::$where = [];
        self::$join = [];
        self::$control = ['', '', ''];
    }

    //a = table name
    //cols = columns names
    public static function select($a, $cols = null): Finder
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

    public static function info($table): Finder
    {
        self::clear();
        self::$instance = new Finder();
        self::$prefix = 'SELECT `COLUMN_NAME` as keyName
        FROM `INFORMATION_SCHEMA`.`COLUMNS`
        WHERE TABLE_SCHEMA = \'' . Connection::getInstance()->activeConfig['dbname'] . '\' AND `TABLE_NAME`=' . '\'' . $table . '\'';
        return self::$instance;
    }

    public static function count($a): Finder
    {
        self::clear();
        self::$instance = new Finder();
        self::$prefix = 'SELECT COUNT(*) as count FROM ' . $a;
        return self::$instance;
    }

    public static function countLast($a) : Finder
    {
        self::$instance = new Finder();
        self::$prefix = 'SELECT COUNT(*) as count FROM ' . $a;
        self::$control = [self::$control[0], '', ''];
        return self::$instance;
    }

    public static function where($a = null) : Finder
    {
        self::$where[0] = ' WHERE ' . $a;
        return self::$instance;
    }

    public static function like($a, $b): Finder
    {
        self::$where[] = trim($a . ' LIKE ' . $b);
        return self::$instance;
    }

    function  and ($a = null): Finder{
        self::$where[] = trim('AND ' . $a);
        return self::$instance;
    }

    function  or ($a = null): Finder{
        self::$where[] = trim('OR ' . $a);
        return self::$instance;
    }

    public static function in(array $a): Finder
    {
        self::$where[] = 'IN ( ' . implode(',', $a) . ' )';
        return self::$instance;
    }

    public static function not($a = null): Finder
    {
        self::$where[] = trim('NOT ' . $a);
        return self::$instance;
    }

    public static function join($table,$condition,$joinType = 'inner'):Finder
    {
        self::$join[] = trim(strtoupper($joinType) . ' JOIN ' . $table . ' ON ' . $condition );
        return self::$instance;
    }

    public static function orderBy($order): Finder
    {
        self::$control[0] = 'ORDER BY ' . $order;
        return self::$instance;
    }

    public static function limit($limit): Finder
    {
        self::$control[1] = 'LIMIT ' . $limit;
        return self::$instance;
    }

    public static function offset($offset): Finder
    {
        self::$control[2] = 'OFFSET ' . $offset;
        return self::$instance;
    }

    public static function paginate($linesPerPage,$page=1) : Finder
    {
        self::limit($linesPerPage);
        self::offset(($page-1) * $linesPerPage);
        return self::$instance;
    }

    public static function getSql() : string
    {
        self::$sql = self::$prefix
        . ' ' . implode(' ',self::$join)
        . ' ' . implode(' ', self::$where)
        . ' ' . implode(' ', self::$control);
        preg_replace('/  /', ' ', self::$sql);
        return trim(self::$sql);
    }
}
