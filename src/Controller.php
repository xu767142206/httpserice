<?php

namespace src;

class Controller{

    protected $request;

    protected $response;

	public function text($request,$response){

        $get = $request->get;

        $this->json( $get );

	}


	public function  json($data)
    {

        $this->response->header("Content-Type", "application/json; charset=utf-8");

        $this->response->end(json_encode($data));

    }


    public function  view($html)
    {

        $this->response->header("Content-Type", "text/html; charset=utf-8");

        $this->response->end($html);

    }


    public function  __construct($request,$response)
    {

        $this->request  = $request;

        $this->response = $response;

    }





}