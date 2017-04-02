<?php

namespace john\frame\Validator\ValidatorRules;

/**
 * Class BaseRule
 * @package john\frame\Validator\ValidatorRules
 */
abstract class BaseRule
{

    /**
     * @var array of validated errors
     */
    protected $errors = [];
    /**
     * @var string name of validation
     */
    protected $validation_name;

    /**
     * BaseRule constructor.
     * @param string $validation_name
     */
    public function __construct(string $validation_name)
    {
        $this->validation_name = $validation_name;
    }

    /**
     * Validation of validated data
     *
     * @param $object
     * @param array $validation_data
     * @return bool
     */
    abstract function check($object, array $validation_data): bool;

    abstract protected function getError($object, string $question_data): string;

    /**
     * Get array of validated errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}