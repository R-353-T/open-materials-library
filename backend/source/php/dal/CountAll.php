<?php

namespace oml\php\dal;

use oml\php\core\Database;
use oml\php\core\SqlSelectOptions;

trait CountAll
{
    public function countAll(SqlSelectOptions $options = new SqlSelectOptions()): int
    {
        $statement = Database::$PDO->prepare(<<<SQL
        SELECT COUNT(*)
        FROM {$this->table}
        {$options->getWhereQuery()}
        SQL);

        $options->applyWhereBinds($statement);
        $statement->execute();
        return $statement->fetchColumn();
    }
}
