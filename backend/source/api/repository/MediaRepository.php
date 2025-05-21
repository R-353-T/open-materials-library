<?php

namespace oml\api\repository;

use oml\api\model\MediaModel;
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

class MediaRepository extends Repository
{
    use SelectById;
    use SelectAll;
    use SelectByName;
    use DeleteById;
    use CountAll;

    public function __construct()
    {
        parent::__construct(___DB_MEDIA___, MediaModel::class);
    }

    /**
     * @param MediaModel $media
     */
    public function insert(mixed $media): int|WP_Error
    {
        $statement = Database::$PDO->prepare(<<<SQL
            INSERT INTO {$this->table}
            (
                `name`,
                `description`,
                `path`
            )
            VALUES 
            (
                :_name,
                :_description,
                :_path
            )
        SQL);

        $statement->bindValue(":_name", $media->name, PDO::PARAM_STR);
        $statement->bindValue(":_description", $media->description, PDO::PARAM_STR);
        $statement->bindValue(":_path", $media->path, PDO::PARAM_STR);

        try {
            $statement->execute();
            $media->id = Database::$PDO->lastInsertId();
            return $media->id;
        } catch (Throwable $error) {
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }

    /**
     * @param MediaModel $media
     */
    public function update(mixed $media): int|WP_Error
    {
        $statement = Database::$PDO->prepare(<<<SQL
            UPDATE {$this->table}
            SET
                `name` = :_name,
                `description` = :_description,
                `path` = :_path
            WHERE `id` = :_id
        SQL);

        $statement->bindValue(":_id", $media->id, PDO::PARAM_INT);
        $statement->bindValue(":_name", $media->name, PDO::PARAM_STR);
        $statement->bindValue(":_description", $media->description, PDO::PARAM_STR);
        $statement->bindValue(":_path", $media->path, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $media->id;
        } catch (Throwable $error) {
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }
}
