<?php

namespace john\frame\Validator;

use john\frame\Exceptions\Validator\ValidationClassNotFoundException;
use john\frame\Exceptions\Validator\ValidationRuleNotFoundException;
use john\frame\Exceptions\Validator\WrongValidationDataException;
use john\frame\Validator\ValidatorRules\BaseRule;

/**
 * Validate all kind of data
 *
 * Class Validator
 * @package john\frame\Validator
 */
class Validator
{

    const RULES_DIR = 'john\\frame\\Validator\\ValidatorRules\\';
    /**
     * @var object validation
     */
    private $object;
    /**
     * @var data for validation
     */
    private $validation_data;
    /**
     * @var array errors in validation
     */
    private $errors = [];

    /**
     * @var array know validation rules
     */
    private static $know_validated_rule = [
        'key_verification_rule' => self::RULES_DIR . 'ExpectedConfigKeyRule',
        'not_start_from' => self::RULES_DIR . 'NotStartFromRule'
    ];

    /**
     * Validator constructor.
     *
     * $valid = new Validator(object $o, [
     *          'validation_name1' => [validation_rule1 => [$param1, ..., $paramN]],
     *             ...
     *          'validation_nameN' => [$validation_ruleN => [$param1, ..., $paramN]],
     * ]
     * )
     * @param $object
     * @param array $validation_data
     * @throws WrongValidationDataException
     */
    public function __construct($object, array $validation_data)
    {
        $this->object = $object;
        foreach ($validation_data as $name => $rule) {
            if ($this->isValidValidationData($name)) {
                throw new WrongValidationDataException($this->isValidValidationData($name));
            }
            foreach ($rule as $rule_name => $rule_values) {
                if ($this->isValidValidationData($rule_name)) {
                    throw new WrongValidationDataException($this->isValidValidationData($rule_name));
                }
                $this->validation_data[$rule_name] = $rule_values;
                array_push($this->validation_data[$rule_name], $name);

            }
        }
    }

    /**
     * method for Validate data
     *
     * @return bool
     * @throws ValidationClassNotFoundException
     * @throws ValidationRuleNotFoundException
     */
    public function validate(): bool
    {
        $result = true;
        foreach ($this->validation_data as $rule_name => $verifiable_data) {
            if (array_key_exists($rule_name, self::$know_validated_rule)) {
                if (class_exists(self::$know_validated_rule[$rule_name])) {
                    /** @var $class BaseRule */
                    $class = new self::$know_validated_rule[$rule_name](array_pop($verifiable_data));
                    if (!$class->check($this->object, $verifiable_data)) {
                        $result = false;
                        $this->errors[$rule_name] = $class->getErrors();
                    }
                } else {
                    throw new ValidationClassNotFoundException("Class ". self::$know_validated_rule[$rule_name] . " not found!");
                }
            } else {
                throw new ValidationRuleNotFoundException("Rule '$rule_name' not found!");
            }
        }
        return $result;
    }

    /**
     * Validation of submitted data for validation
     *
     * @param string $name
     * @return string
     */
    private function isValidValidationData(string $name): string
    {
        $string = '';
        if (is_numeric($name)) {
            $function = debug_backtrace()[2]['function'];
            $class = debug_backtrace()[2]['class'];
            $string = "Array of verify data not valid in method '$function()' of class '$class'! Form of array verify data must be as: ['validation_name' => ['validation_rule' => ['param1', ..., 'paramN']]";
        }
        return $string;
    }

    /**
     * Getting array of errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


}