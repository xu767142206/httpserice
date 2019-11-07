<?php

namespace  src;

class Model
{
    public function query($sql){

        try{
            return DbPool::getInstance()->query($sql);
        }
        catch (\Exception $e){
        	//重连机制啥的 都可以做
//            if($e->getMessage())
            return false;

        }


    }

}