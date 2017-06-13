<?php

namespace John\Frame\TestModels;

use John\Frame\Config\Config;
use John\Frame\Model\BaseModel;

class MainModel extends BaseModel
{
    protected $pdo;
    protected $table;
    protected $pk = 'id';

    protected $goods;
    protected $categories;

    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->getSidebar('categories', 'goods', 'category_id', 'id', '=', 'id');
    }

    public function getAll(string $tableName = '', array $fields = ['*'], $distinct = false)
    {
        $table = $this->table;
        if (!empty($tableName))
            $table = $tableName ?: $tableName;
        $selectStatement = $distinct ?
            $this->pdo->select($fields)
                ->from($table)->distinct()->orderBy($fields[0]) :
            $this->pdo->select($fields)
                ->from($table);
        $stmt = $selectStatement->execute();
        return $stmt->fetchAll();

    }

    public function getLimit($limit,
                             string $tableName = '',
                             array $fields = ['*'],
                             $distinct = false)
    {
        $table = $this->table;
        if (!empty($tableName))
            $table = $tableName ?: $tableName;
        $limit = intval(3);
        echo is_int($limit);
        var_dump($limit);
        $selectStatement = $this->pdo->select()
            ->from($table)
            ->orderBy('id')
            ->offset(2)
            ->limit($limit);
//        ;
//        $selectStatement = $selectStatement->limit(5);
          //  ->limit(5);
        $stmt = $selectStatement->execute();
        return $stmt->fetchAll();

    }

    public function findBorder(string $field, bool $param = true)
    {
        $order = $param ? 'ASC' : 'DESC';
        $selectStatement = $this->pdo->select([$field])
            ->from($this->table)
            ->orderBy($field, $order);
        $stmt = $selectStatement->execute();
        return $stmt->fetch();
    }


    public function findOne($id, $field = '')
    {
        $field = $field ?: $this->pk;
        $selectStatement = $this->pdo->select()
            ->from($this->table)
            ->where($field, '=', $id);
        $stmt = $selectStatement->execute();
        return $stmt->fetchAll();

    }



    public function findAllMinus(string $field, string $value)
    {
        $selectStatement = $this->pdo->select()
            //where пока ограничиваю
            ->from($this->table)->where('id', '<', 10)->whereNotLike($field, $value);
        $stmt = $selectStatement->execute();
        return $stmt->fetchAll();

    }

    private function leftJoinWithOrder(string $firstTable,
                                       string $secondTable,
                                       string $firstField,
                                       string $secondField,
                                       string $operator,
                                       string $orderColumn)
    {
        $selectStatement = $this->pdo->select()
            ->from($firstTable)
            ->leftJoin($secondTable, $firstField, $operator, $secondField)
            ->where($secondField, '<', 10)
            ->orderBy($orderColumn);
        $stmt = $selectStatement->execute();
        return $stmt->fetchAll();
    }

    private function getSidebar($firstTable, $secondTable, $firstField, $secondField, $operator, $orderColumn)
    {
        $result = $this->leftJoinWithOrder($firstTable, $secondTable, $firstField, $secondField, $operator, $orderColumn);
        $countGoods = $this->getAll('goods');
        $countGoods = count($countGoods[0]);
        $this->categories = [];
        $this->goods = [];
        foreach ($result as $row) {
            $key = $row['name'];
            $this->categories[$key] = [];
            $i = 0;
            foreach ($row as $k => $value) {
                if ($countGoods > $i) {
                    $this->categories[$key][$k] = $value;
                    unset($row[$k]);
                    $i++;
                }
            }
            $this->goods[] = $row;
        }
    }

    public function getGoods()
    {
        return $this->goods;
    }

    public function getCategories()
    {
        return $this->categories;
    }
}