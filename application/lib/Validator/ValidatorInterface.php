<?php

namespace application\lib\Validator;

interface ValidatorInterface{

    public function extend(array $rules);

    public function getData();

    public function setData(array $data);

    public function getRules();

    public function setRules(array $rules);

    public function getMessages();

    public function setMessages(array $messages);

    public function validate();

    public function hasErrors();

    public function getErrors();

    public function getErrorsList();

    public function shouldStopOnFirstFailure($stop = true);
}
