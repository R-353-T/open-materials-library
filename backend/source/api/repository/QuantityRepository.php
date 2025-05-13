<?php

namespace oml\api\repository;

use oml\api\model\QuantityModel;
use oml\api\sql\QuantitySql;
use oml\php\abstract\Repository;
use oml\php\core\Database;
use oml\php\dal\SelectByName;
use oml\php\error\InternalError;
use PDO;
use Throwable;

class QuantityRepository extends Repository
{
    use SelectByName;

    private readonly QuantityItemRepository $itemRepository;

    public function __construct()
    {
        parent::__construct(OML_SQL_QUANTITY_TABLENAME, QuantityModel::class);
        $this->itemRepository = QuantityItemRepository::inject();
    }

    /**
     * @param QuantityModel $model
     */
    public function insert(mixed $model)
    {
        try {
            Database::$PDO->beginTransaction();
            $query = QuantitySql::insert($this->table);
            $statement = Database::$PDO->prepare($query);
            $statement->bindValue(":name", $model->name, PDO::PARAM_STR);
            $statement->bindValue(":description", $model->description, PDO::PARAM_STR);
            $statement->execute();
            $model->id = Database::$PDO->lastInsertId();

            // ? ------------------------------------------ ?
            // ? Optimizable with bulk insert               ?
            // ? ------------------------------------------ ?

            foreach ($model->items as $position => $item) {
                $item->quantityId = $model->id;
                $item->position = $position;

                $this->itemRepository->save($item);
            }

            // ? ------------------------------------------ ?

            Database::$PDO->commit();
            return $model->id;
        } catch (Throwable $error) {
            Database::$PDO->rollBack();
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }

    /**
     * @param QuantityModel $model
     */
    public function update(mixed $model)
    {
        try {
            Database::$PDO->beginTransaction();
            $query = QuantitySql::update(OML_SQL_QUANTITY_TABLENAME);
            $statement = Database::$PDO->prepare($query);
            $statement->bindValue(":name", $model->name, PDO::PARAM_STR);
            $statement->bindValue(":description", $model->description, PDO::PARAM_STR);
            $statement->bindValue(":id", $model->id, PDO::PARAM_INT);
            $statement->execute();

            // * ------------------------------------------ *
            // * 1. Increment items positions               *
            // * ------------------------------------------ *

            $this->itemRepository->incrementQuantityPositions($model->id);
            $this->itemRepository->randomizeValues($model->id);

            // * ------------------------------------------ *
            // * 2. Update items                            *
            // * ------------------------------------------ *

            $updatedList = [];

            foreach ($model->items as $position => $item) {
                $item->quantityId = $model->id;
                $item->position = $position;
                $this->itemRepository->save($item);
                $updatedList[] = $item->id;
            }

            // * ------------------------------------------ *
            // * 3. Delete items not in $model->items       *
            // * ------------------------------------------ *

            $this->itemRepository->deleteNotInList($model->id, $updatedList);

            Database::$PDO->commit();
            return $model->id;
        } catch (Throwable $error) {
            Database::$PDO->rollBack();
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }

    /**
     * @param int $id
     * @param bool $cache
     *
     * @return QuantityModel|false
     */
    public function selectById(int $id, bool $cache = true)
    {
        $model = parent::selectById($id, $cache);

        if ($model !== false) {
            $model->items = $this->itemRepository->selectAllByQuantityId($id);
        }

        return $model;
    }
}
