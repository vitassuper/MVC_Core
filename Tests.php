<?php


$db = include __DIR__ . '/application/config/db.php';
$routes = include __DIR__ . '/application/config/routes.php';
$rules = include __DIR__ . '/application/config/validator.php';
include_once __DIR__ . '/autoload.php';

class Tester
{
    private $db;
    private $routes;
    private $rules;

    public function __construct($db, $routes, $rules)
    {
        $this->db = $db;
        $this->routes = $routes;
        $this->rules = $rules;
    }

    public function make()
    {
        $this->dbTest($this->db);
        $this->rulesTest($this->rules);
        $this->routesTest($this->routes);
    }


    private function dbTest($db)
    {
        extract($db);
        new PDO('mysql:host=' . $host . ';dbname=' . $dbname, $user, $password);
    }

    private function routesTest($routes)
    {
        foreach ($routes as $route) {
            $class = new ReflectionClass('application\controllers\\' . ucfirst($route['controller']) . 'Controller');
            if (!$class->isInstantiable()) {
                throw new Exception("Cannot instance object ". $class->name);
            }
            if (!$class->hasMethod($route['action'] . 'Action')) {
                throw new Exception("Class hasnt method " . $route['action'] . 'Action');
            }
        }
    }

    private function rulesTest($rules)
    {
        foreach ($rules as $rule) {
            if (!(new ReflectionClass($rule))->isInstantiable()) {
                throw new Exception("Cannot instance object ");
            }
        }
    }
}
try {
    (new Tester($db, $routes, $rules))->make();
    echo "Success";
} catch (Exception $e) {
    echo $e->getMessage();
}
