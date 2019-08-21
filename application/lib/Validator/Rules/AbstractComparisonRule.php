<?php

namespace application\lib\Validator\Rules;

abstract class AbstractComparisonRule extends AbstractRule
{

    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}
