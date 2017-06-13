<?php

namespace John\Frame\TestModels;


class MobileModel extends MainModel
{

    protected $table = 'mobiles';
    public $pk;

    /**
     * name searching characteristic
     */
    const NAME = 'name';

    /**
     * field to search
     */
    const FIELD = 'field';

    /**
     * parameters of result array
     */
    const PARAMETERS = 'parameters';

    /**
     * Function get filter data for mobile
     * @return array of filters
     */
    public function getFilters(): array
    {
        $searchCharacteristics = [
            'brand' => 'производитель',
            'kernels' => 'Количество ядер',
            'ram' => 'Оперативная память',
            'type' => 'тип',
            'matrix' => 'Тип матрицы',
            'diagonal' => 'Диагональ, дюймов'
        ];
        $filters[] = $this->getSims();
        foreach ($searchCharacteristics as $key => $value) {
            $filters[] = $this->getCharacteristics($key, $value);
        }
        return $filters;
    }

    /**
     *
     * @param string $searchField
     * @param string $characteristicName
     * @return array
     */
    private function getCharacteristics(string $searchField, string $characteristicName):array {
        $knownCharacteristics = $this->getAll($this->table, [$searchField], true);
        return $this->combineCharacteristics($knownCharacteristics, $characteristicName, $searchField);

    }

    /**
     * get filter to sim
     * @return mixed
     */
    private function getSims()
    {
        $field = 'sim';
        $knownSimNumber = $this->getAll($this->table, [$field], true);
        $knownSimType =  $this->getAll($this->table, ['sims'], true);
        if (count($knownSimType) > 2) {
            for ($i = 0; $i< count($knownSimType); $i++) {
                foreach ($knownSimType[$i] as $key => $value) {
                    $temp = explode(',', $value);
                    if(count($temp) > 1) {
                        unset($knownSimType[$i]);
                    }
                }
            }
        }
        $knownSimNumber = array_merge($knownSimNumber, $knownSimType);
        return $this->combineCharacteristics($knownSimNumber, 'SIM-карта', $field);
    }

    /**
     * help function to combine result data
     * @param array $knownCharacteristics
     * @param string $characteristicName
     * @param string $field
     * @return array
     */
    public function combineCharacteristics(array $knownCharacteristics, string $characteristicName, string $field): array {
        $array[self::NAME] = $characteristicName;
        $array[self::FIELD] = $field;
        foreach ($knownCharacteristics as $row) {
            foreach ($row as $key => $value) {
                $array[self::PARAMETERS][]['parameter'] = $value;
            }
        }
        return $array;
    }

    public function boundaryPrice()
    {
        $field = 'price';
        $maxPrice = $this->findBorder($field, false);
        $minPrice = $this->findBorder($field);
        $price = [];
        $price['minPrice'] = $minPrice[$field];
        $price['maxPrice'] = $maxPrice[$field];
        return $price;
    }


}