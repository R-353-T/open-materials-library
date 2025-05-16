<?php

namespace oml\api\repository;

use oml\api\model\MediaModel;
use oml\api\sql\MediaSql;
use oml\php\abstract\Repository;
use oml\php\core\Database;
use oml\php\dal\SelectByName;
use oml\php\error\InternalError;
use PDO;
use Throwable;
use WP_Error;

class MediaRepository extends Repository
{
    use SelectByName;

    public function __construct()
    {
        parent::__construct(___DB_MEDIA___, MediaModel::class);
    }

    /**
     * @param MediaModel $model
     */
    public function insert(mixed $model): int|WP_Error
    {
        try {
            Database::$PDO->beginTransaction();
            $query = MediaSql::insert($this->table);
            $statement = Database::$PDO->prepare($query);
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
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }

    /**
     * @param MediaModel $model
     */
    public function update(mixed $model): int|WP_Error
    {
        try {
            Database::$PDO->beginTransaction();
            $query = MediaSql::update($this->table);
            $statement = Database::$PDO->prepare($query);
            $statement->bindValue(":name", $model->name, PDO::PARAM_STR);
            $statement->bindValue(":description", $model->description, PDO::PARAM_STR);
            $statement->bindValue(":path", $model->path, PDO::PARAM_STR);
            $statement->bindValue(":id", $model->id, PDO::PARAM_INT);
            $statement->execute();
            Database::$PDO->commit();
            return $model->id;
        } catch (Throwable $error) {
            Database::$PDO->rollBack();
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }
}
