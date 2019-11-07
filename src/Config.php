<?php

namespace src;

class Config
{
    private static $configMap=[];
    private static $instance;
    private function __construct()
    {
    }
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance=new self();
        }
        return self::$instance;
    }
    public  function load(){
        $files=glob('./src/config'."/*.php");
        if(!empty($files)){
            foreach ($files as $dir=>$fileName){
                self::$configMap+=include $fileName;
            }
        }
    }
    public  function get($key){
        if(isset(self::$configMap[$key])){
            return self::$configMap[$key];
        }
        return false;
    }
}