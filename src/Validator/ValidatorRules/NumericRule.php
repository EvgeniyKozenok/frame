<?php

namespace John\Frame\Validator\ValidatorRules;

/**
 * @inheritdoc
 */
class NumericRule extends BaseRule
{
    /**
     * @inheritdoc
     */
    function check($object, array $validation_data): bool
    {
        $result = true;
            foreach ($validation_data as $item => $data) {
                if (!is_numeric($data)) {
                    $data = explode(',', $data);
                    $data = implode('.', $data);
                    if (!is_numeric($data)) {
                        $key = array_search($validation_data[$item], $object);
                        $this->errors[$this->validation_name][$key] = $this->getError($data, $key);
                        $result = false;
                    }
                }
            }
        return $result;
    }

    /**
     * @inheritdoc
     */
    function getError($object, string $question_data): string
    {
        return $question_data;
    }

}