<?php

namespace application\core;

class ServerRequest{

    public function __construct(){
        
    }

    public function requestUri(){
        return $_SERVER['REQUEST_URI'];
    }
}