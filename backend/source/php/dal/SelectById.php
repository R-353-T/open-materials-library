<?php

namespace oml\php\dal;

use oml\php\core\Database;
use PDO;

trait SelectById
{
    public function selectById(int $id): false|object
    {
        $statement = Database::$PDO->prepare(<<<SQL
        SELECT *
        FROM {$this->table}
        WHERE `id` = :_id
        SQL);

        $statement->bindValue(":_id", $id, PDO::PARAM_INT);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->model);
        return $statement->fetch();
    }
}
