<?php

namespace oml\php\dal;

use oml\php\core\Database;
use PDO;

trait SelectByName
{
    public function selectByName(string $name): false | object
    {
        $statement = Database::$PDO->prepare(<<<SQL
        SELECT *
        FROM {$this->table}
        WHERE `name` = :_name
        SQL);

        $statement->bindValue(":_name", $name, PDO::PARAM_STR);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->model);
        return $statement->fetch();
    }
}
