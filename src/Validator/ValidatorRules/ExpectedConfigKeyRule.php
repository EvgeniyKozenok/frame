<?php

namespace john\frame\Validator\ValidatorRules;

/**
 * Rule for validate config field
 *
 * Class ExpectedConfigKeyRule
 * @package john\frame\Validator\ValidatorRules
 */
class ExpectedConfigKeyRule extends BaseRule
{

    /**
     * @inheritdoc
     */
    function check($object, array $validation_data): bool
    {
        $result = true;
        foreach ($object as $field => $value) {
            foreach ($validation_data as $item => $data) {
                if (!array_key_exists($data, $value)) {
                    $this->errors[$this->validation_name][] = $this->getError($field, $data);
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function getError($object, string $question_data): string
    {
         return "In section '$object' of config missing or incorrect important field '$question_data'!";
    }
}