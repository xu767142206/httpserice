<?php

go(function () {
    $swoole_mysql = new Swoole\Coroutine\MySQL();
    $swoole_mysql->connect([
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => '9506',
        'database' => 'erp',
    ]);
//    var_dump($swoole_mysql);
    $res = $swoole_mysql->query('select * from dict_data');
    var_dump($res);
});





