<?php

namespace oml\api\repository;

use oml\api\model\QuantityItemModel;
use oml\api\sql\QuantityItemSql;
use oml\php\abstract\Repository;
use oml\php\core\Database;
use PDO;

class QuantityItemRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(OML_SQL_QUANTITY_ITEM_TABLENAME, QuantityItemModel::class);
    }

    public function save(mixed $model)
    {
        if ($model->id !== null) {
            $this->update($model);
        } else {
            $this->insert($model);
        }
    }

    /**
     * @param QuantityItemModel $model
     */
    public function insert(mixed $model)
    {
        $query = QuantityItemSql::insert($this->table);
        $statement = Database::$PDO->prepare($query);
        $statement->bindValue(":quantityId", $model->quantityId, PDO::PARAM_INT);
        $statement->bindValue(":value", $model->value, PDO::PARAM_STR);
        $statement->bindValue(":position", $model->position, PDO::PARAM_INT);
        $statement->execute();
        $model->id = Database::$PDO->lastInsertId();
    }

    /**
     * @param QuantityItemModel $model
     */
    public function update(mixed $model)
    {
        $query = QuantityItemSql::update($this->table);
        $statement = Database::$PDO->prepare($query);
        $statement->bindValue(":quantityId", $model->quantityId, PDO::PARAM_INT);
        $statement->bindValue(":value", $model->value, PDO::PARAM_STR);
        $statement->bindValue(":position", $model->position, PDO::PARAM_INT);
        $statement->bindValue(":id", $model->id, PDO::PARAM_INT);
        $statement->execute();
        return $model->id;
    }

    public function selectAllByQuantityId(int $id)
    {
        $query = QuantityItemSql::selectAllByQuantityId($this->table);
        $statement = Database::$PDO->prepare($query);
        $statement->bindValue(":quantityId", $id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, $this->model);
    }

    public function incrementQuantityPositions(int $quantityId, int $by = 1024)
    {
        $query = QuantityItemSql::incrementQuantityPositions($this->table);
        $statement = Database::$PDO->prepare($query);
        $statement->bindValue(":quantityId", $quantityId, PDO::PARAM_INT);
        $statement->bindValue(":_by", $by, PDO::PARAM_INT);
        $statement->execute();
        return 1;
    }

    public function randomizeValues(int $quantityId)
    {
        $query = QuantityItemSql::randomizeValues($this->table);
        $statement = Database::$PDO->prepare($query);
        $statement->bindValue(":quantityId", $quantityId, PDO::PARAM_INT);
        $statement->execute();
        return 1;
    }

    public function deleteNotInList(int $quantityId, array $ids)
    {
        $query = QuantityItemSql::deleteNotInList($this->table, count($ids));
        $statement = Database::$PDO->prepare($query);
        $statement->bindValue(":quantityId", $quantityId, PDO::PARAM_INT);

        foreach ($ids as $i => $id) {
            $statement->bindValue(":id{$i}", $id, PDO::PARAM_INT);
        }

        $statement->execute();
        return $statement->rowCount();
    }
}
