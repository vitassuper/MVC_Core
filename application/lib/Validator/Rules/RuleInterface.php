<?php

namespace application\lib\Validator\Rules;

interface RuleInterface
{
  
    public function getName();

    public function isValid($input = null);

    public function emptyValueAllowed();

    public function allowEmptyValue();
    
    public function canSkipValidation($input = null);

    public function hasViolations();

    /**
     * Returns validation rule violations.
     *
     * @return array
     */
    public function getViolations();
}
