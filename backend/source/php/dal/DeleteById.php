<?php

namespace oml\php\dal;

use oml\php\core\Database;
use PDO;

trait DeleteById
{
    public function deleteById(int $id): bool
    {
        $statement = Database::$PDO->prepare(<<<SQL
        DELETE FROM {$this->table}
        WHERE `id` = :_id
        SQL);

        $statement->bindValue(":_id", $id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() === 1;
    }
}
