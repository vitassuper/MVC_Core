<?php

namespace application\lib\Validator;

interface FactoryInterface
{

    public function make(array $data, array $rules, array $messages = []);

    public function extend(array $rules);
    
    public function getAvailable();
}
