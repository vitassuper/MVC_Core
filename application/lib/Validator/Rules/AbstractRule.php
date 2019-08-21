<?php

namespace application\lib\Validator\Rules;

abstract class AbstractRule implements RuleInterface
{

    protected $emptyAllowed = false;

    protected $violations = [];

    public static function allowingEmptyValue()
    {
        return (new static)->allowEmptyValue();
    }

    public function getName()
    {
        $class = get_class($this);
        $segments = explode('\\', $class);
        $name = end($segments);

        if (!ctype_lower($name)) {
            $name = preg_replace('/\s+/u', '', $name);
            $name = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $name), 'UTF-8');
        }

        $postfix = strrpos($name, '_rule');

        if ($postfix !== false) {
            $name = substr($name, 0, $postfix);
        }

        return $name;
    }

    public function emptyValueAllowed()
    {
        return $this->emptyAllowed;
    }

    public function allowEmptyValue()
    {
        $this->emptyAllowed = true;
        
        return $this;
    }

    public function canSkipValidation($input = null)
    {
        return false;
    }

    public function hasViolations()
    {
        return count($this->violations) > 0;
    }

    public function getViolations()
    {
        return $this->violations;
    }
}
