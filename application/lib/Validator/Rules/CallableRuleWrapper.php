<?php

namespace application\lib\Validator\Rules;

use UnexpectedValueException;

class CallableRuleWrapper extends AbstractRule
{
  
    private $name;

    private $valid = false;

    private $skip = false;

    public function __construct($result)
    {
        is_bool($result) ? $this->setDefaults($result)
                         : $this->setDefaultsFromArray($result);
    }

    private function setDefaults($result)
    {
        $this->valid = $result;
        $this->emptyAllowed = false;
        $this->skip = false;
    }
   
    private function setDefaultsFromArray(array $attributes)
    {
        if (!isset($attributes['valid'])) {
            throw new UnexpectedValueException('Validation check missing.');
        }

        if (isset($attributes['name'])) {
            $this->name = $attributes['name'];
        }

        $this->valid = (bool) $attributes['valid'];
        $this->emptyAllowed = isset($attributes['empty_allowed'])
            ? (bool) $attributes['empty_allowed']
            : false;

        $this->skip = isset($attributes['skip']) ? (bool) $attributes['skip'] : false;
        $this->violations = isset($attributes['violations']) ? $attributes['violations'] : [];
    }

    public function getName()
    {
        if (isset($this->name)) {
            return $this->name;
        }

        return uniqid(parent::getName() . '_', true);
    }

    public function isValid($input = null)
    {
        return $this->valid;
    }

    public function canSkipValidation($input = null)
    {
        return $this->skip;
    }
}
