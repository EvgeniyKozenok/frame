<?php

namespace John\Frame\Validator\ValidatorRules;

class RequireRule extends BaseRule
{

    /**
     * Validation of validated data
     *
     * @param $object
     * @param array $validation_data
     * @return bool
     */
    function check($object, array $validation_data): bool
    {
        $result = true;
        foreach ($validation_data as $key => $value) {
            if(trim($value == '')){
                $this->errors[$this->validation_name][$key]=$this->getError($key, $value);
                $result = false;
            }
        }
        return $result;
    }

    protected function getError($object, string $question_data): string
    {
        return "Поле '$object' обязательно к заполнению";
    }
}