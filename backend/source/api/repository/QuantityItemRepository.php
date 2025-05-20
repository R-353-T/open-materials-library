<?php

namespace oml\api\repository;

use oml\api\model\QuantityItemModel;
use oml\php\abstract\Repository;
use oml\php\core\Database;
use oml\php\dal\SelectById;
use PDO;
use WP_Error;

class QuantityItemRepository extends Repository
{
    use SelectById;

    public function __construct()
    {
        parent::__construct(___DB_QUANTITY_ITEM___, QuantityItemModel::class);
    }

    /**
     * @param QuantityItemModel $quantity_item
     */
    public function insert(mixed $quantity_item): int|WP_Error
    {
        $statement = Database::$PDO->prepare(<<<SQL
        INSERT INTO {$this->table}
        (
            `quantityId`,
            `value`,
            `position`
        )
        VALUES
        (
            :_quantityId,
            :_value,
            :_position
        )
        SQL);

        $statement->bindValue(":_quantityId", $quantity_item->quantityId, PDO::PARAM_INT);
        $statement->bindValue(":_value", $quantity_item->value, PDO::PARAM_STR);
        $statement->bindValue(":_position", $quantity_item->position, PDO::PARAM_INT);
        $statement->execute();
        $quantity_item->id = Database::$PDO->lastInsertId();
        return $quantity_item->id;
    }

    /**
     * @param QuantityItemModel $quantity_item
     */
    public function update(mixed $quantity_item): int|WP_Error
    {
        $statement = Database::$PDO->prepare(<<<SQL
        UPDATE {$this->table}
        SET
            `quantityId` = :_quantityId,
            `value` = :_value,
            `position` = :_position
        WHERE `id` = :_id
        SQL);

        $statement->bindValue(":_quantityId", $quantity_item->quantityId, PDO::PARAM_INT);
        $statement->bindValue(":_value", $quantity_item->value, PDO::PARAM_STR);
        $statement->bindValue(":_position", $quantity_item->position, PDO::PARAM_INT);
        $statement->bindValue(":_id", $quantity_item->id, PDO::PARAM_INT);
        $statement->execute();
        return $quantity_item->id;
    }

    public function selectAllByQuantityId(int $id): array
    {
        $statement = Database::$PDO->prepare(<<<SQL
        SELECT *
        FROM {$this->table}
        WHERE `quantityId` = :_quantityId 
        ORDER BY `position` ASC
        SQL);

        $statement->bindValue(":_quantityId", $id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, $this->model);
    }

    public function resetAllByQuantityId(int $quantity_id): void
    {
        // * ------------------------------------------ *
        // * 1. Positions                               *
        // * ------------------------------------------ *

        $statement = Database::$PDO->prepare(<<<SQL
        UPDATE {$this->table}
        SET `position` = `position` + :_by
        WHERE `quantityId` = :_quantityId
        SQL);

        $statement->bindValue(":_quantityId", $quantity_id, PDO::PARAM_INT);
        $statement->bindValue(":_by", ___MAX_ITEM_PER_RELATION___, PDO::PARAM_INT);
        $statement->execute();

        // * ------------------------------------------ *
        // * 2. Values                                  *
        // * ------------------------------------------ *

        $statement = Database::$PDO->prepare(<<<SQL
        UPDATE {$this->table}
        SET `value` = UUID()
        WHERE `quantityId` = :_quantityId
        SQL);

        $statement->bindValue(":_quantityId", $quantity_id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function deleteNotInIdList(int $quantity_id, array $id_list)
    {
        $parameter_count = count($id_list);
        $query = <<<SQL
        DELETE FROM {$this->table}
        WHERE `quantityId` = :_quantityId 
        AND `id` NOT IN (
        SQL;

        for ($i = 0; $i < $parameter_count; $i++) {
            $query .= ":_id{$i}";

            if ($i < $parameter_count - 1) {
                $query .= ", ";
            }
        }

        $query .= ")";

        $statement = Database::$PDO->prepare($query);
        $statement->bindValue(":_quantityId", $quantity_id, PDO::PARAM_INT);

        foreach ($id_list as $index => $id) {
            $statement->bindValue(":_id{$index}", $id, PDO::PARAM_INT);
        }

        $statement->execute();
        return $statement->rowCount();
    }
}
