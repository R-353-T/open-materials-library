<?php

namespace oml\php\dal;

use oml\php\core\Database;
use oml\php\core\SqlSelectOptions;
use PDO;

trait SelectAll
{
    public function selectAll(SqlSelectOptions $options = new SqlSelectOptions()): array
    {
        $statement = Database::$PDO->prepare(<<<SQL
        SELECT *
        FROM {$this->table}
        {$options->getWhereQuery()}
        {$options->getOrderByQuery()}
        {$options->getLimitAndOffset()}
        SQL);

        $options->applyWhereBinds($statement);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, $this->model);
    }
}
