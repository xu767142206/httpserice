<?php

namespace src\controller;

use src\Controller;
use src\Db;

class Text extends Controller{

    public function text($request,$response){

//        $get = $request->get;

        $result = Db::table('dict_data')->where(['id'=>$request->get['id']])->select();

//        \co::sleep(2);

        $this->json( $result );

    }


    public function index($request,$response){

//        $get = $request->get;

        $this->view('<html><h1>index</h1>this is index.html</html>');

    }


    public function aa($request,$response){

//        $get = $request->get;

        $this->view('<html><h1>m</h1>this is m.html</html>');

    }


}