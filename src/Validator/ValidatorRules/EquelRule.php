<?php

namespace John\Frame\Validator\ValidatorRules;


class EquelRule extends BaseRule
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
        $keys = '';
        $etalon = $validation_data;
        array_shift($etalon);
        $test = array_keys($etalon);
        $etalon = $etalon[$test[0]];
        foreach ($validation_data as $key => $value){
            $keys .= "'$key' и ";
            if(($value != $etalon) || empty($value)) {

                $this->errors[$this->validation_name] = $this->getError($keys, $value);
                $result = false;
            }
        }
        return $result;
    }

    protected function getError($object, string $question_data): string
    {
        $object = substr($object, 0, -2);
        return "Значение в полей $object не эквивалентны";
    }
}