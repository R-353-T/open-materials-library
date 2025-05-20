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

    public function deleteInIdList(array $id_list)
    {
        $parameter_count = count($id_list);
        $query = <<<SQL
        DELETE FROM {$this->table}
        WHERE `id` IN (
        SQL;

        for ($i = 0; $i < $parameter_count; $i++) {
            $query .= ":id{$i}";

            if ($i < $parameter_count - 1) {
                $query .= ", ";
            }
        }

        $query .= ")";
        $statement = Database::$PDO->prepare($query);

        foreach ($id_list as $index => $id) {
            $statement->bindValue(":id{$index}", $id, PDO::PARAM_INT);
        }

        $statement->execute();
        return $statement->rowCount() === count($id_list);
    }
}
