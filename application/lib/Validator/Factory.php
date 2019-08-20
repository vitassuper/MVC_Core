<?php

namespace application\lib\Validator;

class Factory implements FactoryInterface
{

    private static $instance;

    protected $available = [];

    public function __construct()
    {
        $this->extend(require __DIR__ . '../../../config/validator.php');
    }

    public static function getInstance()
    {
        if (static::$instance === null) {
            return static::$instance = new static;
        }

        return static::$instance;
    }

    public function make(array $data, array $rules, array $messages = [])
    {
        return (new Validator($data, $rules, $messages))->extend($this->available);
    }

    public function extend(array $rules)
    {
        $this->available = array_merge($this->available, $rules);

        return $this;
    }

    public function getAvailable()
    {
        return $this->available;
    }
}
