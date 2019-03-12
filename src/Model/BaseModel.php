<?php

namespace John\Frame\Model;

use John\Frame\Config\Config;
use Slim\PDO\Database;

abstract class BaseModel extends Database
{
    protected $pdo;

    public function __construct(Config $config)
    {
        if(!$config->has('db')){
            throw new \Exception('No DB connection params predefined');
        }
        $dsn = sprintf('%s:dbname=%s;host=%s;',
            $config->get('db.driver', 'mysql'),
            $config->get('db.dbname'),
            $config->get('db.host', '127.0.0.1')
        );
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ];
        try {
            $this->pdo = new Database($dsn,
                $config->get('db.user', 'root'),
                $config->get('db.password', ''),
                $options);
        } catch (\PDOException $e) {
            throw $e;
        }
    }

}