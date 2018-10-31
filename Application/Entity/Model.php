<?php

namespace Application\Entity;

use Application\Database\Connection;
use Application\Database\Finder;
use PDO;

class Model
{
    protected static $tableNames = [];
    protected static $columnNames = [];
    protected static $connection = null;

    protected static $protectedFields = [];

    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    public function __get($name)
    {
        if (isset($this->{$name})) {
            return $this->{$name};
        }
        if (\method_exists($this, $name)) {
            return $this->{$name}();
        }

        return null;
    }

    public function __toString()
    {
        $aux = [];
        $myVars = get_object_vars($this);
        foreach ($myVars as $key => $value) {

            if (!in_array($key, get_called_class()::$protectedFields)) {
                $aux[$key] = $value;
            }
        }
        return json_encode($aux);
    }

    public function __invoke()
    {
        echo get_called_class();die;
    }

    public function arrayToEntity($data)
    {
        if ($data && is_array($data)) {
            if (isset(get_called_class()::$columnNames[get_called_class()])) {
                foreach (get_called_class()::$columnNames[get_called_class()] as $propertyName) {
                    if (isset($data[$propertyName])) {
                        $this->$propertyName = $data[$propertyName];
                    }
                }
            } else {
                foreach ($data as $propertyName => $value) {
                    $this->$propertyName = $data[$propertyName];
                }
            }
            return true;
        }
        return false;
    }
    protected function hasMany($class, $foreign = null, $altname = null, $sort = null, $limit = null, $page = 0)
    {
        $varName = $altname ?? get_called_class()::$tableNames[$class];
        if (property_exists($this, $varName) && $page < 1) {
            return $this->{$varName};
        }
        if (!$foreign) {
            $foreign = get_called_class()::$tableNames[get_called_class()] . '_id';
        }
        $sqlObj = $class::select()->where($foreign . ' = :id ');
        if ($sort) {
            $sqlObj->orderBy($sort);
        }
        if ($limit) {
            if ($page > 0) {
                $sqlObj->paginate($limit, $page);
            } else {
                $sqlObj->limit($limit);
            }
        }
        $sql = $sqlObj->getSql();
        $result = $class::runQuery($sql, ['id' => $this->id], false, true);
        $this->{$varName} = $result !== false && !empty($result) ? $result : null;
        if ($this->{$varName} && $page > 0) {
            $sqlObj->countLast(get_called_class()::$tableNames[$class]);
            $sql = $sqlObj->getSql();
            $result = $class::runQuery($sql, ['id' => $this->id], false, false);
            $this->{$varName . 'Count'} = $result[0]['count'];
        }
        return $this->{$varName};
    }
    protected function hasOne($class, $foreign = null, $altname = null)
    {
        $varName = $altname ?? get_called_class()::$tableNames[$class];
        if (property_exists($this, $varName)) {
            return $this->{$varName};
        }
        if (!$foreign) {
            $foreign = get_called_class()::$tableNames[get_called_class()] . '_id';
        }
        $sql = $class::select()->where($foreign . ' = :id ')::getSql();
        $result = $class::runQuery($sql, ['id' => $this->id], false, true);
        $this->{$varName} = $result !== false && !empty($result) ? $result[0] : null;
        return $this->{$varName};
    }
    protected function belongsTo($class, $foreign = null, $altname = null)
    {
        $varName = $altname ?? get_called_class()::$tableNames[$class];
        if (property_exists($this, $varName)) {
            //return $this->{$varName};
        }
        if (!$foreign) {
            $foreign = get_called_class()::$tableNames[$class] . '_id';
        }

        $sql = $class::select()->where('id = :id ')::getSql();
        $result = $class::runQuery($sql, ['id' => $this->{$foreign}], false, true);
        $this->{$varName} = $result !== false && !empty($result) ? $result[0] : null;
        return $this->{$varName};
    }

    public function delete()
    {
        if ($this->id) {
            $sql = 'DELETE FROM ' . get_called_class()::$tableNames[get_called_class()] . ' WHERE id = :id';
            return get_called_class()::runQuery($sql, ['id' => $this->id], true);
        }
    }

    public function save()
    {
        if (!isset(get_called_class()::$columnNames[get_called_class()])) {
            get_called_class()::$columnNames[get_called_class()] = [];
            $sql = Finder::info(get_called_class()::$tableNames[get_called_class()])::getSql();
            $stmt = get_called_class()::$connection->getPdo()->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                get_called_class()::$columnNames[get_called_class()][] = $row['keyName'];
            }
        }
        if ($this->id) {
            $sql = 'UPDATE ' . get_called_class()::$tableNames[get_called_class()] . ' SET ';
            $params = [];
            foreach (get_called_class()::$columnNames[get_called_class()] as $key => $value) {
                $sql .= $value . '= :' . $value . ', ';
                $params[$value] = $this->{$value};
            }
            $sql = substr($sql, 0, -2) . ' WHERE id = :id';
            return get_called_class()::runQuery($sql, $params, true);
        } else {
            $sql = 'INSERT INTO ' . get_called_class()::$tableNames[get_called_class()] . ' (' . implode(', ', get_called_class()::$columnNames[get_called_class()]) . ') VALUES (';
            $params = [];
            foreach (get_called_class()::$columnNames[get_called_class()] as $key => $value) {
                $sql .= ':' . $value . ', ';
                $params[$value] = $this->{$value};
            }
            $sql = substr($sql, 0, -2) . ')';
            return get_called_class()::runQuery($sql, $params, true);
        }
    }

    public static function fieldsNames($fields,$useAlias=false): string
    {
        if(!is_array($fields) && $fields == '*')
            if (!isset(get_called_class()::$columnNames[get_called_class()])) {
                get_called_class()::$columnNames[get_called_class()] = [];
                $sql = Finder::info(get_called_class()::$tableNames[get_called_class()])::getSql();
                $stmt = get_called_class()::$connection->getPdo()->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    get_called_class()::$columnNames[get_called_class()][] = $row['keyName'];
                }
            }

        $result = '';
        $prefix = get_called_class()::getName();
        if (is_array($fields)) {
            foreach ($fields as $value) 
            {
                if($value=='*' && $useAlias)
                    throw \Exception('Your really messed this ',324);

                $result .= $prefix . '.' .$value . ($useAlias?' as '.$prefix . '.' .$value:'') . ',';
            }
        } else 
        {
            if($fields == '*')
            {
                foreach (get_called_class()::$columnNames[get_called_class()] as $value) {
                    $result.= $prefix . '.' .$value . ($useAlias?' as \''.$prefix . '.' .$value . '\'':'') . ',';
                }
            }
            else{
                foreach (explode(',',$fields) as $value) {
                    $result.= $prefix . '.' .$value . ($useAlias?' as \''.$prefix . '.' .$value . '\'':'') . ',';
                }
            }
        }
        return trim(substr($result,0,-1));
    }

    public static function init($tableName = null)
    {
        get_called_class()::$connection = Connection::getInstance();
        if ($tableName) {
            get_called_class()::$tableNames[get_called_class()] = $tableName;
        } else {
            $name = substr(strrchr(get_called_class(), '\\'), 1);
            get_called_class()::$tableNames[get_called_class()] = strtolower($name);
        }
    }

    public static function find($id)
    {
        $sql = get_called_class()::select()->where('id = :id')::getSql();
        $stmt = get_called_class()::$connection->getPdo()->prepare($sql);
        $stmt->execute(['id' => (int) $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        return $stmt->fetchObject(get_called_class());
    }
    public static function all()
    {
        $sql = get_called_class()::select()->where('id > :id')::getSql();
        $stmt = get_called_class()::$connection->getPdo()->prepare($sql);

        $stmt->execute(['id' => (int) 0]);

        $result = [];

        while ($row = $stmt->fetchObject(get_called_class())) {
            $result[] = $row;
        }

        return $result;
    }

    public static function select($cols = null): Finder
    {
        if (!isset(get_called_class()::$columnNames[get_called_class()])) {
            get_called_class()::$columnNames[get_called_class()] = [];
            $sql = Finder::info(get_called_class()::$tableNames[get_called_class()])::getSql();
            $stmt = get_called_class()::$connection->getPdo()->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                get_called_class()::$columnNames[get_called_class()][] = $row['keyName'];
            }
        }
        if (!$cols) {
            $cols = [];
            foreach (get_called_class()::$columnNames[get_called_class()] as $key => $value) {
                if (!in_array($value, get_called_class()::$protectedFields)) {
                    $cols[] = $value;
                }
            }
            $cols = implode(', ', $cols);
        }

        return Finder::select(get_called_class()::$tableNames[get_called_class()], $cols);
    }
    public static function count(): Finder
    {
        return Finder::count(get_called_class()::$tableNames[get_called_class()]);
    }

    public static function getName(): string
    {
        return get_called_class()::$tableNames[get_called_class()];
    }

    public static function runQuery($finder, $params = null, $isOperation = false, $forceCast = false)
    {
        $query = '';
        $result = null;
        if ($finder instanceof Finder) {
            $query = $finder::getSql();
        } elseif (is_string($finder)) {
            $query = $finder;
        }
        try
        {
            if ($isOperation) {
                get_called_class()::$connection->getPdo()->beginTransaction();
            }
            $stmt = get_called_class()::$connection->getPdo()->prepare($query);
            if ($params) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            if ($isOperation) {
                if ($params) {

                    if (isset($params['id'])) {
                        $result = (int) $params['id'];
                    } else {
                        $result = (int) get_called_class()::$connection->getPdo()->lastInsertId();
                    }
                }

                get_called_class()::$connection->getPdo()->commit();
            }
        } catch (PDOException $e) {
            error_log(__METHOD__ . ':' . __LINE__ . ':' . $e->getMessage());
            if ($isOperation) {
                get_called_class()::$connection->getPdo()->rollBack();
            }
            return null;
        } catch (\Exception $e) {
            error_log(__METHOD__ . ':' . __LINE__ . ':' . $e->getMessage());
            return null;
        }

        if ($isOperation) {
            return $result;
        }

        $result = [];
        if ($forceCast) {
            while ($row = $stmt->fetchObject(get_called_class())) {
                $result[] = $row;
            }
        } else {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row;
            }
        }

        return $result;
    }

}
