<?php

namespace oml\api\repository;

use oml\api\model\QuantityModel;
use oml\php\abstract\Repository;
use oml\php\core\Database;
use oml\php\dal\CountAll;
use oml\php\dal\DeleteById;
use oml\php\dal\SelectAll;
use oml\php\dal\SelectById;
use oml\php\dal\SelectByName;
use oml\php\error\InternalError;
use PDO;
use Throwable;
use WP_Error;

class QuantityRepository extends Repository
{
    use SelectById;
    use SelectAll;
    use SelectByName;
    use DeleteById;
    use CountAll;

    private readonly QuantityItemRepository $itemRepository;

    public function __construct()
    {
        parent::__construct(___DB_QUANTITY___, QuantityModel::class);
        $this->itemRepository = QuantityItemRepository::inject();
    }

    /**
     * @param QuantityModel $quantity
     */
    public function insert(mixed $quantity): int|WP_Error
    {
        $statement = Database::$PDO->prepare(<<<SQL
        INSERT INTO {$this->table}
        (
            `name`,
            `description`
        )
        VALUES
        (
            :_name,
            :_description
        )
        SQL);

        $statement->bindValue(":_name", $quantity->name, PDO::PARAM_STR);
        $statement->bindValue(":_description", $quantity->description, PDO::PARAM_STR);

        try {
            Database::$PDO->beginTransaction();
            $statement->execute();
            $quantity->id = Database::$PDO->lastInsertId();

            // ? Optimizable with bulk insert               ?
            // ? ------------------------------------------ ?

            foreach ($quantity->items as $position => $item) {
                $item->quantityId = $quantity->id;
                $item->position = $position;
                $this->itemRepository->insert($item);
            }

            // ? ------------------------------------------ ?

            Database::$PDO->commit();
            return $quantity->id;
        } catch (Throwable $error) {
            Database::$PDO->rollBack();
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }

    /**
     * @param QuantityModel $quantity
     */
    public function update(mixed $quantity): int|WP_Error
    {
        $statement = Database::$PDO->prepare(<<<SQL
        UPDATE {$this->table}
        SET
            `name` = :_name,
            `description` = :_description
        WHERE `id` = :_id
        SQL);
        $statement->bindValue(":_name", $quantity->name, PDO::PARAM_STR);
        $statement->bindValue(":_description", $quantity->description, PDO::PARAM_STR);
        $statement->bindValue(":_id", $quantity->id, PDO::PARAM_INT);

        try {
            Database::$PDO->beginTransaction();
            $statement->execute();

            // * ------------------------------------------ *
            // * 1. Free positions and values of items      *
            // * ------------------------------------------ *

            $this->itemRepository->resetAllByQuantityId($quantity->id);

            // * ------------------------------------------ *
            // * 2. Update items                            *
            // * ------------------------------------------ *

            $id_list = [];

            foreach ($quantity->items as $item) {
                $item->quantityId = $quantity->id;
                $id_list[] = $item->id;

                if ($item->id !== null) {
                    $this->itemRepository->update($item);
                } else {
                    $this->itemRepository->insert($item);
                }
            }

            // * ------------------------------------------ *
            // * 3. Delete items not in $quantity->items    *
            // * ------------------------------------------ *

            $this->itemRepository->deleteNotIn($quantity->id, $id_list);

            Database::$PDO->commit();
            return $quantity->id;
        } catch (Throwable $error) {
            Database::$PDO->rollBack();
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }

    public function selectByIdWithItems(int $id): false|object
    {
        $quantity = $this->selectById($id);

        if ($quantity !== false) {
            $quantity->items = $this->itemRepository->selectAllByQuantityId($id);
        }

        return $quantity;
    }
}
