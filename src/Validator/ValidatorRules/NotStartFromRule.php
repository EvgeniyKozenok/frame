<?php

namespace John\Frame\Validator\ValidatorRules;

/**
 * Rule fo not start from some parameter
 *
 * Class NotStartFromRule
 * @package John\Frame\Validator\ValidatorRules
 */
class NotStartFromRule extends BaseRule
{

    /**
     * @inheritdoc
     */
    function check($object, array $validation_data): bool
    {
        $result = true;
        foreach ($object as $field => $value) {
            foreach ($validation_data as $item => $data) {
                $ch = mb_substr($field, 0, 1);
                if ($ch === $data) {
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
    function getError($object, string $question_data): string
    {
        return "Field '$object' in config don't must started from '$question_data'";
    }
}