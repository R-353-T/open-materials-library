<?php

namespace oml\api\repository;

use oml\api\model\DatasheetCategoryModel;
use oml\php\abstract\Repository;
use oml\php\core\Database;
use oml\php\dal\DeleteById;
use oml\php\dal\SelectAll;
use oml\php\dal\SelectById;
use oml\php\dal\SelectByName;
use oml\php\error\InternalError;
use PDO;
use Throwable;
use WP_Error;

class DatasheetCategoryRepository extends Repository
{
    use SelectById;
    use SelectAll;
    use SelectByName;
    use DeleteById;

    public function __construct()
    {
        parent::__construct(___DB_DATASHEET_CATEGORY___, DatasheetCategoryModel::class);
    }

    /**
     * @param DatasheetCategoryModel $category
     */
    public function insert(mixed $category): int|WP_Error
    {
        $statement = Database::$PDO->prepare(<<<SQL
            INSERT INTO {$this->table}
            (
                `name`,
                `description`,
                `position`,
                `parentId`
            )
            VALUES
            (
                :_name,
                :_description,
                :_position,
                :_parentId
            )
        SQL);

        $statement->bindValue(":_name", $category->name, PDO::PARAM_STR);
        $statement->bindValue(":_description", $category->description, PDO::PARAM_STR);
        $statement->bindValue(":_position", $category->position, PDO::PARAM_INT);
        $statement->bindValue(":_parentId", ...Repository::nullable($category->parentId, PDO::PARAM_INT));

        try {
            $statement->execute();
            $category->id = Database::$PDO->lastInsertId();
            return $category->id;
        } catch (Throwable $error) {
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }

    /**
     * @param DatasheetCategoryModel $category
     */
    public function update(mixed $category): int|WP_Error
    {
        $statement = Database::$PDO->prepare(<<<SQL
            UPDATE {$this->table}
            SET
                `name` = :_name,
                `description` = :_description,
                `position` = :_position,
                `parentId` = :_parentId
            WHERE `id` = :_id
        SQL);

        $statement->bindValue(":_id", $category->id, PDO::PARAM_INT);
        $statement->bindValue(":_name", $category->name, PDO::PARAM_STR);
        $statement->bindValue(":_description", $category->description, PDO::PARAM_STR);
        $statement->bindValue(":_position", $category->position, PDO::PARAM_INT);
        $statement->bindValue(":_parentId", ...Repository::nullable($category->parentId, PDO::PARAM_INT));

        try {
            $statement->execute();
            $category->id = Database::$PDO->lastInsertId();
            return $category->id;
        } catch (Throwable $error) {
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }
}
