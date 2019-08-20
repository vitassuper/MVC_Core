<?php

namespace application\lib\Validator;

interface ValidatorAwareInterface{
    public function setValidator(ValidatorInterface $validator);

    public function validate();
}
