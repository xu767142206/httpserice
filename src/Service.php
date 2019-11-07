<?php

namespace src;

use src\Config;
use src\route\Route;
use src\DbPool;
class Service
{

    protected  $server;

    public function start()
    {
        Config::get_instance()->load();
        $this->server = new \Swoole\Http\Server("0.0.0.0", 9501,SWOOLE_BASE);
        $this->server->set([
            // 开启静态资源请求
            'enable_static_handler' => false,
//            'document_root' =>__DIR__."/public/static",
            'worker_num' => 1,
        ]);

        //是在swoole的worker进程内触发
//        $this->server->on('start', [$this, 'start']);
        $this->server->on('workerStart', [$this, 'workerStart']);
        $this->server->on('request', [$this, 'request']);
        $this->server->start(); //启动服务器

    }

    public function WorkerStart($server, $worker_id){

        Route::getIntance()->setConfig(Config::get_instance()->get('route'));
        DbPool::getInstance(Config::get_instance()->get('mysql'));

    }


    public function request($request, $response){
        if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            return $response->end();
        }

        (new Main())->start($request, $response, trim($request->server['request_uri']));

    }

}





