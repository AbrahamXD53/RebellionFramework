<?php

namespace Application\Autoload;

class Loader
{
    const UNABLE_TO_LOAD = 'Unable to load class';    

    protected static $dirs = [];
    protected static $registered = 0;

    public function __construct(array $dirs = [])
    {
        self::init($dirs);
    }

    public static function init(array $dirs = []){
        if($dirs){
            self::addDirs($dirs);
        }
        if(self::$registered==0){
            spl_autoload_register(__CLASS__ . '::autoload');
            self::$registered++;
        }
    }

    protected static function loadFile($file)
    {
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        return false;
    }
    public static function autoLoad($class)
    {
        $success = false;
        $fn = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        foreach (self::$dirs as $start) {
            $file = $start . DIRECTORY_SEPARATOR . $fn;
            if (self::loadFile($file)) {
                $success = true;
                break;
            }
        }
        if (!$success) {
            if (!self::loadFile(__DIR__ . DIRECTORY_SEPARATOR . $fn)) {
                throw new \Exception(self::UNABLE_TO_LOAD . ' ' . $class);
            }
        }
        return $success;
    }
    public static function addDirs($dirs){
        if(is_array($dirs)){
            self::$dirs= array_merge(self::$dirs,$dirs);
        }else{
            self::$dirs[]=$dirs;
        }
    }
}
