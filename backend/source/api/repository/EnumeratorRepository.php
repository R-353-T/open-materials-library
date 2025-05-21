<?php

namespace oml\api\repository;

use oml\api\model\EnumeratorModel;
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

class EnumeratorRepository extends Repository
{
    use SelectById;
    use SelectAll;
    use SelectByName;
    use DeleteById;
    use CountAll;

    private readonly EnumeratorItemRepository $itemRepository;

    public function __construct()
    {
        parent::__construct(___DB_ENUMERATOR___, EnumeratorModel::class);
        $this->itemRepository = EnumeratorItemRepository::inject();
    }

    /**
     * @param EnumeratorModel $enumerator
     * TODO - Optimizable with bulk insert
     */
    public function insert(mixed $enumerator): int|WP_Error
    {
        $statement = Database::$PDO->prepare(<<<SQL
            INSERT INTO {$this->table}
            (
                `name`,
                `description`,
                `typeId`,
                `quantityId`
            )
            VALUES
            (
                :_name,
                :_description,
                :_typeId,
                :_quantityId
            )
        SQL);

        $statement->bindValue(":_name", $enumerator->name, PDO::PARAM_STR);
        $statement->bindValue(":_description", $enumerator->description, PDO::PARAM_STR);
        $statement->bindValue(":_typeId", $enumerator->typeId, PDO::PARAM_INT);
        $statement->bindValue(":_quantityId", ...Repository::nullable($enumerator->quantityId, PDO::PARAM_INT));

        try {
            Database::$PDO->beginTransaction();
            $statement->execute();
            $enumerator->id = Database::$PDO->lastInsertId();

            foreach ($enumerator->items as $item) {
                $item->enumeratorId = $enumerator->id;
                $this->itemRepository->insert($item);
            }

            Database::$PDO->commit();
            return $enumerator->id;
        } catch (Throwable $error) {
            Database::$PDO->rollBack();
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }

    /**
     * @param EnumeratorModel $quantity
     * TODO - Optimizable with bulk insert / update
     */
    public function update(mixed $enumerator): int|WP_Error
    {
        $statement = Database::$PDO->prepare(<<<SQL
            UPDATE {$this->table}
            SET
                `name` = :_name,
                `description` = :_description,
                `typeId` = :_typeId,
                `quantityId` = :_quantityId
            WHERE id = :_id
        SQL);

        $statement->bindValue(":_name", $enumerator->name, PDO::PARAM_STR);
        $statement->bindValue(":_description", $enumerator->description, PDO::PARAM_STR);
        $statement->bindValue(":_typeId", $enumerator->typeId, PDO::PARAM_INT);
        $statement->bindValue(":_quantityId", ...Repository::nullable($enumerator->quantityId, PDO::PARAM_INT));
        $statement->bindValue(":_id", $enumerator->id, PDO::PARAM_INT);

        try {
            Database::$PDO->beginTransaction();
            $statement->execute();
            $this->itemRepository->resetAllByEnumeratorId($enumerator->id);
            $id_list = [];

            foreach ($enumerator->items as $item) {
                $item->quantityId = $enumerator->id;

                if ($item->id !== null) {
                    $this->itemRepository->update($item);
                } else {
                    $this->itemRepository->insert($item);
                }

                $id_list[] = $item->id;
            }

            $this->itemRepository->deleteNotInIdList($enumerator->id, $id_list);
            Database::$PDO->commit();
            return $enumerator->id;
        } catch (Throwable $error) {
            Database::$PDO->rollBack();
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }

    public function selectByIdWithItems(int $id): false|object
    {
        $enumerator = $this->selectById($id);

        if ($enumerator !== false) {
            $enumerator->items = $this->itemRepository->selectAllByEnumeratorId($id);
        }

        return $enumerator;
    }
}
