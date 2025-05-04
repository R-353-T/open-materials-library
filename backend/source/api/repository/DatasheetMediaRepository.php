<?php

namespace oml\api\repository;

use oml\api\model\DatasheetMediaModel;
use oml\php\abstract\Repository;
use oml\php\core\Database;
use oml\php\enum\SqlQueries;
use oml\php\error\InternalError;
use PDO;
use Throwable;

class DatasheetMediaRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(
            OML_SQL_MEDIA_TABLENAME,
            DatasheetMediaModel::class
        );
    }

    /**
     * Inserts a datasheet media into the database
     *
     * @param DatasheetMediaModel $model The datasheet media model to be inserted
     *
     * @return int|InternalError The id of the inserted datasheet media or an InternalError
     */
    public function insert(mixed $model)
    {
        try {
            Database::$PDO->beginTransaction();
            $statement = Database::$PDO->prepare(SqlQueries::insertMedia(OML_SQL_MEDIA_TABLENAME));
            $statement->bindValue(":name", $model->name, PDO::PARAM_STR);
            $statement->bindValue(":description", $model->description, PDO::PARAM_STR);
            $statement->bindValue(":path", $model->path, PDO::PARAM_STR);
            $statement->execute();
            $id = Database::$PDO->lastInsertId();
            Database::$PDO->commit();
            $model->id = $id;
            return $id;
        } catch (Throwable $error) {
            Database::$PDO->rollBack();
            return new InternalError(500, $error->getMessage(), $error->getTraceAsString());
        }
    }

    /**
     * Updates a datasheet media by id
     *
     * @param DatasheetMediaModel $model The datasheet media model to be updated
     *
     * @return int|InternalError The id of the updated datasheet media or an InternalError
     */
    public function update(mixed $model)
    {
        try {
            Database::$PDO->beginTransaction();
            $statement = Database::$PDO->prepare(SqlQueries::updateMedia(OML_SQL_MEDIA_TABLENAME));
            $statement->bindValue(":name", $model->name, PDO::PARAM_STR);
            $statement->bindValue(":description", $model->description, PDO::PARAM_STR);
            $statement->bindValue(":path", $model->path, PDO::PARAM_STR);
            $statement->bindValue(":id", $model->id, PDO::PARAM_INT);
            $statement->execute();
            Database::$PDO->commit();
            return $model->id;
        } catch (Throwable $error) {
            Database::$PDO->rollBack();
            return new InternalError(500, $error->getMessage(), $error->getTraceAsString());
        }
    }
}
