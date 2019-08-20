<?php

namespace application\lib\Validator;

trait FactoryAwareTrait
{
    protected $validationFactory;

    public function getValidationFactory()
    {
        return $this->validationFactory;
    }

    public function setValidationFactory(FactoryInterface $factory)
    {
        $this->validationFactory = $factory;

        return $this;
    }

    public function makeValidator(array $data, array $rules, array $messages = [])
    {
        return $this->validationFactory->make($data, $rules, $messages);
    }
}
