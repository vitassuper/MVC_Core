<?php
namespace application\controllers;

use application\lib\Validator\Rules\AbstractRule;

class Test extends AbstractRule{

    public function __construct()
    {
        echo "Construct worked";
    }

    public function isValid($input = null)
    {
        
    }
}