<?php

namespace  src;

class DbPool
{
    private static $instance;//单例对象
    private $connection_num = 200;//连接数量
    private $connection_obj = [];
    private $avil_connection_num = 200;//可用连接



    //构造方法连接mysql，创建20mysql连接
    private function __construct($config)
    {

        for($i=0;$i<$this->connection_num;$i++){
            //常规方式新建pdo
//            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";
//            $this->connection_obj[] =  new \Pdo($dsn,$config['user'],$config['pwd']);

            //协程mysql
            $swoole_mysql = new \Swoole\Coroutine\MySQL();
            $swoole_mysql->connect([
                'host' => $config['host'],
                'port' => 3306,
                'user' => $config['user'],
                'password' => $config['pwd'],
                'database' => $config['dbname'],
            ]);
            $this->connection_obj[] =  $swoole_mysql;

        }
    }
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
    public static function getInstance($config='')
    {
        if(is_null(self::$instance)){
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    //执行sql操作
    public function query($sql)
    {
        if($this->avil_connection_num==0){
            throw new \Exception("暂时没有可用的连接诶，请稍后");
        }
        //执行sql语句
        $pdo = array_pop($this->connection_obj);
        //可用连接数减1
        $this->avil_connection_num --;
        //使用从连接池中取出的mysql连接执行查询，并且把数据取成关联数组
//        $rows = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $rows = $pdo->query($sql);
        //把mysql连接放回连接池，可用连接数+1
        array_push($this->connection_obj,$pdo);
        $this->avil_connection_num ++;
        return $rows;
    }
}


