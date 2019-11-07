<?php

namespace src;

use src\route\Route;

class Main{

	public function start($request, $response, $path = '/'){

        $a_c = (Route::getIntance())->check($path);

        $c = $a_c[0];
        $a = $a_c[1];

        $class = new \ReflectionClass( $c );
        $instance = $class->newInstance( $request, $response );
        $class->getMethod($a)->invoke( $instance,$request, $response );

	}


}