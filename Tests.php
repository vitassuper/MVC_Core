<?php

$db = include __DIR__.'/application/config/db.php';
$routes = include __DIR__.'/application/config/routes.php';
$rules = include __DIR__.'/application/config/routes.php';

class Tester{
    private $db;
    private $routes;
    private $rules;

    public function __construct($db, $routes, $rules)
    {
        $this->db=$db;
        $this->routes=$routes;
        $this->rules=$rules;

    }

    public function make(){
        $this->dbTest($this->db);
    }


    private function dbTest($db){
        extract($db);
        new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $password);
    }

    private function routesTest($routes){

    }

    private function rulesTest($rules){
        
    }
}
try{
(new Tester($db, $routes, $rules))->make();
echo "Success";
}catch(Exception $e){
    echo $e->getMessage();
}