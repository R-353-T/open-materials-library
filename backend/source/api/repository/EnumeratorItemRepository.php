<?php

namespace oml\api\repository;

use oml\api\model\EnumeratorItemModel;
use oml\php\abstract\Repository;
use oml\php\core\Database;
use oml\php\dal\SelectById;
use PDO;
use WP_Error;

class EnumeratorItemRepository extends Repository
{
    use SelectById;

    public function __construct()
    {
        parent::__construct(___DB_ENUMERATOR_ITEM___, EnumeratorItemModel::class);
    }

    /**
     * @param EnumeratorItemModel $enumerator_item
     */
    public function insert(mixed $enumerator_item): int|WP_Error
    {
        $statement = Database::$PDO->prepare(<<<SQL
        INSERT INTO {$this->table}
        (
            `enumeratorId`,
            `text`,
            `number`,
            `position`,
            `quantityItemId`
        )
        VALUES
        (
            :_enumeratorId,
            :_text,
            :_number,
            :_position,
            :_quantityItemId
        )
        SQL);

        $statement->bindValue(":_enumeratorId", $enumerator_item->enumeratorId, PDO::PARAM_INT);

        $value_type = isset($enumerator_item->text) ? PDO::PARAM_STR : PDO::PARAM_NULL;
        $statement->bindValue(":_value", $enumerator_item->text, $value_type);

        $number_type = isset($enumerator_item->number) ? PDO::PARAM_INT : PDO::PARAM_NULL;
        $statement->bindValue(":_number", $enumerator_item->number, $number_type);

        $quantity_item_type = isset($enumerator_item->quantityItemId) ? PDO::PARAM_INT : PDO::PARAM_NULL;
        $statement->bindValue(":_quantityItemId", $enumerator_item->quantityItemId, $quantity_item_type);

        $statement->bindValue(":_position", $enumerator_item->position, PDO::PARAM_INT);
        $statement->execute();
        $enumerator_item->id = Database::$PDO->lastInsertId();
        return $enumerator_item->id;
    }

    /**
     * @param EnumeratorItemModel $enumerator_item
     */
    public function update(mixed $enumerator_item): int|WP_Error
    {
        $statement = Database::$PDO->prepare(<<<SQL
        INSERT INTO {$this->table}
        (
            `enumeratorId`,
            `text`,
            `number`,
            `position`,
            `quantityItemId`
        )
        VALUES
        (
            :_enumeratorId,
            :_text,
            :_number,
            :_position,
            :_quantityItemId
        )
        SQL);

        $statement->bindValue(":_enumeratorId", $enumerator_item->enumeratorId, PDO::PARAM_INT);

        $value_type = isset($enumerator_item->text) ? PDO::PARAM_STR : PDO::PARAM_NULL;
        $statement->bindValue(":_value", $enumerator_item->text, $value_type);

        $number_type = isset($enumerator_item->number) ? PDO::PARAM_INT : PDO::PARAM_NULL;
        $statement->bindValue(":_number", $enumerator_item->number, $number_type);

        $quantity_item_type = isset($enumerator_item->quantityItemId) ? PDO::PARAM_INT : PDO::PARAM_NULL;
        $statement->bindValue(":_quantityItemId", $enumerator_item->quantityItemId, $quantity_item_type);

        $statement->bindValue(":_position", $enumerator_item->position, PDO::PARAM_INT);
        $statement->execute();
        $enumerator_item->id = Database::$PDO->lastInsertId();
        return $enumerator_item->id;
    }

    public function selectAllByEnumeratorId(int $id): array
    {
        $statement = Database::$PDO->prepare(<<<SQL
        SELECT *
        FROM {$this->table}
        WHERE `enumeratorId` = :_enumeratorId 
        ORDER BY `position` ASC
        SQL);

        $statement->bindValue(":_enumeratorId", $id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, $this->model);
    }

    public function resetAllByEnumeratorId(int $enumerator_id): void
    {
        // * ------------------------------------------ *
        // * 1. Positions                               *
        // * ------------------------------------------ *

        $statement = Database::$PDO->prepare(<<<SQL
        UPDATE {$this->table}
        SET `position` = `position` + :_by
        WHERE `enumeratorId` = :_enumeratorId
        SQL);

        $statement->bindValue(":_enumeratorId", $enumerator_id, PDO::PARAM_INT);
        $statement->bindValue(":_by", ___MAX_ITEM_PER_RELATION___, PDO::PARAM_INT);
        $statement->execute();

        // * ------------------------------------------ *
        // * 2. Values                                  *
        // * ------------------------------------------ *

        $statement = Database::$PDO->prepare(<<<SQL
        UPDATE {$this->table}
        SET `value` = UUID()
        WHERE `enumeratorId` = :_enumeratorId
        SQL);

        $statement->bindValue(":_enumeratorId", $enumerator_id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function deleteNotInIdList(int $enumerator_id, array $id_list)
    {
        $parameter_count = count($id_list);
        $query = <<<SQL
        DELETE FROM {$this->table}
        WHERE `enumeratorId` = :_enumeratorId 
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
        $statement->bindValue(":_enumeratorId", $enumerator_id, PDO::PARAM_INT);

        foreach ($id_list as $index => $id) {
            $statement->bindValue(":_id{$index}", $id, PDO::PARAM_INT);
        }

        $statement->execute();
        return $statement->rowCount();
    }
}
