<?php

namespace src\route;

class Route{

    private static $instance;

    private static $route;

    const NAMESPACE = '\src\controller\\';

    public function check($path){

        foreach (self::$route as $k => $v){

            if( trim($path) == trim($v[0]) ){

                return [
                         self::NAMESPACE.$v[1],
                         $v[2]
                ];
            }
        }
        return FALSE;
    }

    private function  __construct()
    {

        // TODO: Implement __construct() method.

    }


    private function __clone()
    {
        // TODO: Implement __clone() method.
    }


    public static function getIntance(){

        if (self::$instance == null) {
            //如果没有,则创建当前类的实例
            self::$instance = new self();


        }

        return self::$instance;


    }

    public function setConfig($route){

        self::$route = $route;

    }

}