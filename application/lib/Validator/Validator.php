<?php

namespace application\lib\Validator;

use application\lib\Validator\Rules\StopsFurtherValidationInterface;
use OutOfBoundsException;
use ReflectionClass;
use application\lib\Validator\Rules\CallableRuleWrapper;
use application\lib\Validator\Rules\RuleInterface;
use UnexpectedValueException;


class Validator implements ValidatorInterface{
    
    protected static $rulesCache = [];

    protected $available = [];
    
    protected $data = [];
    
    protected $rules = [];

    protected $formattedRules = [];

    protected $messages = [];

    protected $errors = [];

    protected $shouldStopOnFirstFailure = false;

    protected $shouldStopWithinAttribute = false;

    private $bypass = false;

    public function __construct(array $data, array $rules, array $messages = []){
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
    }

    public function extend(array $rules){
        $keys = array_map(function ($key) use ($rules) {
            if (is_int($key)) {
                return static::getRuleInstance($rules[$key])->getName();
            }

            return $key;
        }, array_keys($rules));

        $mapped = array_combine($keys, array_values($rules));

        $this->available = array_merge($this->available, $mapped);

        return $this;
    }

    private static function getRuleInstance($class)
    {
        $class = (new ReflectionClass($class));

        if (!$class->isInstantiable()) {
            throw new UnexpectedValueException('Rule class must be instantiable.');
        }

        if (!$class->implementsInterface(RuleInterface::class)) {
            throw new UnexpectedValueException(sprintf('Rule must implement %s.', RuleInterface::class));
        }

        return $class->newInstance();
    }

    public function getAvailable()
    {
        return $this->available;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function setRules(array $rules)
    {
        $this->rules = $rules;
        $this->formatRules($rules);

        return $this;
    }

    private function formatRules(array $all)
    {
        $attributes = array_keys($all);
        $rules = array_map(function ($attribute) use ($all) {
            $resolved = $this->resolveRules($all[$attribute]);

            if (!is_array($resolved)) {
                return [$resolved];
            }

            return $resolved;
        }, $attributes);

        $this->formattedRules = array_combine($attributes, $rules);
    }

    private function resolveRules($rules)
    {
        if (is_array($rules)) {
            foreach ($rules as $rule) {
                static::checkRuleType($rule);
            }

            return $rules;
        }

        if (is_string($rules)) {
            return $this->parseRulesFromString($rules);
        }

        static::checkRuleType($rules);

        return $rules;
    }

    protected function parseRulesFromString($string)
    {
        if (isset(static::$rulesCache[$string])) {
            return static::$rulesCache[$string];
        }

        $set = explode('|', $string);
        $rules = [];

        foreach ($set as $rule) {
            list($name, $arguments) = $this->getNameAndArgumentsFromString($rule);

            $rules[] = $this->buildRule($name, $arguments);
        }

        return static::$rulesCache[$string] = $rules;
    }

    /**
     * Takes rule identifier and arguments from the string.
     *
     * @param string $rule
     *
     * @return array
     */
    private function getNameAndArgumentsFromString($rule)
    {
        $delimpos = strpos($rule, ':');

        if ($delimpos) {
            $name = substr($rule, 0, $delimpos);
            $arguments = (array) explode(',', substr($rule, $delimpos+1));
        } else {
            $name = $rule;
            $arguments = [];
        }

        return [$name, $arguments];
    }

    /**
     * Builds a new rule object from the given identifier and constructor arguments.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return object
     */
    private function buildRule($name, array $arguments = [])
    {
        $class = $this->getRuleClassName($name);

        return (new ReflectionClass($class))->newInstanceArgs($arguments);
    }

    /**
     * Resolves a rule class name by the identifier.
     *
     * @param string $identifier
     *
     * @return string
     * @throws UnexpectedValueException
     */
    private function getRuleClassName($identifier)
    {
        if (array_key_exists($identifier, $this->available)) {
            return $this->available[$identifier];
        }

        throw new UnexpectedValueException(
            sprintf(
                'Rule identified by `%s` could not be loaded.',
                $identifier
            )
        );
    }

    /**
     * Check rule for the proper type.
     *
     * @param mixed $rule
     * @throws UnexpectedValueException
     */
    private static function checkRuleType($rule)
    {
        if (!$rule instanceof RuleInterface && !is_callable($rule)) {
            throw new UnexpectedValueException(
                sprintf('Rule must implement `%s` or be callable.', RuleInterface::class)
            );
        }
    }

    /**
     * Returns validation messages.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Sets validation messages.
     *
     * @param array $messages
     *
     * @return $this
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Validates given data.
     *
     * @return bool
     * @throws UnexpectedValueException
     */
    public function validate()
    {
        if (empty($this->formattedRules)) {
            $this->formatRules($this->rules);
        }

        foreach ($this->formattedRules as $attribute => $rules) {
            $this->bypass = false;
            $this->shouldStopWithinAttribute = false;

            foreach ($rules as $rule) {
                if ($rule instanceof UntilFirstFailure) {
                    $this->shouldStopWithinAttribute = true;
                }

                $value = $this->getValue($attribute);
                $rule = $this->resolveRule($rule, $value);

                $this->handle($rule, $attribute, $value);

                if ($this->shouldProceedToTheNextAttribute($attribute)) {
                    continue 2;
                }
                
                if ($this->shouldStopOnFailure($rule, $attribute)) {
                    return false;
                }
            }
        }
        
        return !count($this->errors);
    }

    /**
     * Processes a data attribute and creates new validation error message if the validation failed.
     *
     * @param mixed $rule
     * @param string $attribute
     * @param mixed $value
     * @throws UnexpectedValueException
     */
    protected function handle($rule, $attribute, $value)
    {
        if ($rule instanceof Sometimes && $this->valueIsEmpty($value)) {
            $this->bypass = true;

            return;
        }

        if ($rule->isValid($value) ||
            $rule->canSkipValidation($value) ||
            ($rule->emptyValueAllowed() && $this->valueIsEmpty($value))) {
            return;
        }

        $this->addError($attribute, $rule);
    }

    /**
     * Returns value of an attribute.
     *
     * @param string $attribute
     *
     * @return mixed
     */
    protected function getValue($attribute)
    {
        if ($attribute === null) {
            return null;
        }

        if (isset($this->data[$attribute])) {
            return $this->data[$attribute];
        }

        $segments = explode('.', $attribute);
        $data = null;

        foreach ($segments as $segment) {
            if (!array_key_exists($segment, $this->data)) {
                return null;
            }

            $data = $this->data[$segment];
        }

        return $data;
    }

    /**
     * Checks whether value is empty.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function valueIsEmpty($value)
    {
        return $value === null || $value === '';
    }

    /**
     * Resolves validation rule according to actual rule type.
     *
     * @param callable|RuleInterface $rule
     * @param mixed $value
     *
     * @return RuleInterface
     * @throws UnexpectedValueException
     */
    protected function resolveRule($rule, $value)
    {
        if ($rule instanceof RuleInterface) {
            return $rule;
        }
        
        if (is_callable($rule)) {
            return new CallableRuleWrapper($rule($value));
        }

        throw new UnexpectedValueException(sprintf('Rule must implement `%s` or be callable.', RuleInterface::class));
    }

    /**
     * Creates new validation error message.
     *
     * @param string $attribute
     * @param RuleInterface $rule
     */
    protected function addError($attribute, RuleInterface $rule)
    {
        $key = $this->prepareRuleKey($attribute, $rule);
        $errors = $this->prepareErrors($attribute, $key, $rule);

        if (isset($this->errors[$attribute])) {
            $this->errors[$attribute] = array_merge($this->errors[$attribute], $errors);
        } else {
            $this->errors[$attribute] = $errors;
        }
    }

    /**
     * Prepares rule key for the validation error messages array.
     *
     * @param string $attribute
     * @param RuleInterface $rule
     *
     * @return string
     */
    protected function prepareRuleKey($attribute, RuleInterface $rule)
    {
        foreach ($this->available as $identifier => $item) {
            if ($item === $rule) {
                return $identifier;
            }
        }

        $name = $rule->getName();

        if ($name) {
            return $name;
        }

        if (isset($this->errors[$attribute])) {
            return count($this->errors[$attribute]);
        }

        return 0;
    }

    /**
     * Prepares validation error messages to proper format.
     *
     * @param string $attribute
     * @param string $ruleName
     * @param RuleInterface $rule
     *
     * @return array
     */
    protected function prepareErrors($attribute, $ruleName, RuleInterface $rule)
    {
        $prefix = $attribute . '.' . $ruleName;
        $messages = $this->getMessagesByAttributeAndRuleName($attribute, $ruleName);
        $violations = $rule->getViolations();

        if (!count($violations)) {
            return [reset($messages)];
        }

        return $this->getMessagesForViolations($messages, $violations, $prefix);
    }

    /**
     * Filter all given messages by the attribute and the rule name.
     *
     * @param string $attribute
     * @param string $ruleName
     *
     * @return array
     */
    private function getMessagesByAttributeAndRuleName($attribute, $ruleName)
    {
        $prefix = $attribute . '.' . $ruleName;
        $messages = array_filter($this->messages, function ($key) use ($attribute, $prefix) {
            return $key === $attribute || strpos($key, $prefix) === 0;
        }, ARRAY_FILTER_USE_KEY);

        if (empty($messages)) {
            $messages = [$prefix];
        }

        return $messages;
    }

    /**
     * Returns messages for all validation rule violations.
     *
     * @param array $messages
     * @param array $violations
     * @param string $prefix
     *
     * @return array
     */
    private function getMessagesForViolations(array $messages, array $violations, $prefix)
    {
        $result = [];

        foreach ($violations as $violation) {
            $keys = [
                $prefix,
                $prefix . '.' . $violation
            ];

            foreach ($keys as $key) {
                if (array_key_exists($key, $messages)) {
                    $result[] = $messages[$key];
                }
            }
        }

        return $result;
    }

    /**
     * Determines whether validation should stop on the first failure.
     *
     * @param string $attribute
     * @param RuleInterface $rule
     *
     * @return bool
     */
    protected function shouldStopOnFailure(RuleInterface $rule, $attribute)
    {
        return ($rule instanceof StopsFurtherValidationInterface ||
                $this->shouldStopOnFirstFailure ||
                $this->shouldStopWithinAttribute)
               && array_key_exists($attribute, $this->errors);
    }

    /**
     * Determines whether validator should proceed to the next attribute
     *
     * @param string $attribute
     *
     * @return bool
     */
    protected function shouldProceedToTheNextAttribute($attribute)
    {
        return $this->bypass || ($this->shouldStopWithinAttribute && $this->attributeHasErrors($attribute));
    }

    /**
     * Checks whether an attribute already has errors
     *
     * @param string $attribute
     *
     * @return bool
     */
    protected function attributeHasErrors($attribute)
    {
        return !empty($this->errors[$attribute]);
    }

    /**
     * Checks whether there are validation errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Returns all validation errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Returns a plain validation errors array without their attribute names.
     *
     * @return array
     */
    public function getErrorsList()
    {
        $list = [];

        foreach ($this->errors as $messages) {
            foreach ($messages as $message) {
                $list[] = $message;
            }
        }

        return $list;
    }

    /**
     * Determines whether validation should stop on the first failure.
     *
     * @param bool $stop
     *
     * @return $this
     */
    public function shouldStopOnFirstFailure($stop = true)
    {
        $this->shouldStopOnFirstFailure = $stop;

        return $this;
    }
}
