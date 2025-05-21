<?php

namespace oml\api\repository;

use oml\api\model\CategoryModel;
use oml\php\abstract\Repository;
use oml\php\core\Database;
use oml\php\dal\DeleteById;
use oml\php\dal\SelectAll;
use oml\php\dal\SelectById;
use oml\php\error\InternalError;
use PDO;
use Throwable;
use WP_Error;

class CategoryRepository extends Repository
{
    use SelectById;
    use SelectAll;
    use DeleteById;

    public function __construct()
    {
        parent::__construct(___DB_CATEGORY___, CategoryModel::class);
    }

    /**
     * @param CategoryModel $category
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
     * @param CategoryModel $category
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

    public function selectByNameAndParentId(string $name, int $parent_id): CategoryModel | false
    {
        $statement = Database::$PDO->prepare(<<<SQL
            SELECT * FROM {$this->table}
            WHERE `name` = :_name AND `parentId` = :_parentId
        SQL);

        $statement->bindValue(":_name", $name, PDO::PARAM_STR);
        $statement->bindValue(":_parentId", $parent_id, PDO::PARAM_INT);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->model);
        return $statement->fetch();
    }

    public function countByParentId(int $parent_id): int
    {
        $statement = Database::$PDO->prepare(<<<SQL
            SELECT COUNT(*) FROM {$this->table}
            WHERE `parentId` = :_parentId
        SQL);

        $statement->bindValue(":_parentId", $parent_id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function isChildOf(int $needle, int $parent_id, bool $recursive = false): bool
    {
        $statement = Database::$PDO->prepare(<<<SQL
            SELECT `id` FROM {$this->table}
            WHERE `parentId` = :_parentId
        SQL);

        $statement->bindValue(":_parentId", $parent_id, PDO::PARAM_INT);
        $statement->execute();
        $id_list = $statement->fetchAll(PDO::FETCH_COLUMN);

        foreach ($id_list as $child_id) {
            if ($child_id === $needle) {
                return true;
            }

            if ($recursive) {
                return $this->isChildOf($needle, $child_id, $recursive);
            }
        }

        return false;
    }
}
