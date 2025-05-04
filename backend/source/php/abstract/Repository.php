<?php

namespace oml\php\abstract;

use oml\php\core\Database;
use oml\php\core\HashMap;
use oml\php\core\SqlSelectOptions;
use oml\php\enum\SqlQueries;
use PDO;
use WP_Error;

abstract class Repository extends Service
{
    public readonly string $table;
    public readonly string $model;
    protected readonly HashMap $cache;

    public function __construct(string $table, string $model)
    {
        $this->table = $table;
        $this->model = $model;
        $this->cache = new HashMap();
    }

    /**
     * Insert a model into the database table
     *
     * @param mixed $model The model to be inserted
     *
     * @return int|WP_Error
     */
    public function insert(mixed $model)
    {
        return null;
    }

    /**
     * Update a model in the database table
     *
     * @param mixed $model The model to be updated
     *
     * @return int|WP_Error
     */
    public function update(mixed $model)
    {
        return null;
    }

    /**
     * Select a model from the database table by its ID
     *
     * @param int $id The ID of the model to be selected
     * @param bool $cache Whether to use the cache or not
     *
     * @return false|object The selected model if successful, null otherwise
     */
    public function selectById(int $id, bool $cache = true)
    {
        $model = null;

        if ($cache && $this->cache->contains($id)) {
            $model = $this->cache->get($id);
        } else {
            $statement = Database::$PDO->prepare(SqlQueries::selectById($this->table));
            $statement->bindValue(":id", $id, PDO::PARAM_INT);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->model);
            $model = $statement->fetch();
            $this->cache->set($id, $model);
        }

        return $model;
    }

    /**
     * Delete a model from the database table by its ID
     *
     * @param int $id The ID of the model to be deleted
     *
     * @return bool Whether the deletion was successful or not
     */
    public function deleteById(int $id)
    {
        $statement = Database::$PDO->prepare(SqlQueries::deleteById($this->table));
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();
        $deleted = $statement->rowCount() === 1;

        if ($deleted && $this->cache->contains($id)) {
            $this->cache->remove($id);
        }

        return $deleted;
    }

    /**
     * Select all models from the database table using the given select options
     *
     * @param SqlSelectOptions $options The select options to use for the query
     *
     * @return array The array of selected models
     */
    public function selectAll(SqlSelectOptions $options = new SqlSelectOptions())
    {
        $statement = Database::$PDO->prepare(SqlQueries::selectAll($this->table, $options));
        $options->applyWhereBinds($statement);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, $this->model);
    }

    /**
     * Count of all models from the database table using the given select options
     *
     * @param SqlSelectOptions $options The select options to use for the query
     *
     * @return int The count of all selected models
     */
    public function countAll(SqlSelectOptions $options = new SqlSelectOptions())
    {
        $statement = Database::$PDO->prepare(SqlQueries::countAll($this->table, $options));
        $options->applyWhereBinds($statement);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Get the final page from the database table using the given select options
     *
     * @param SqlSelectOptions $options The select options to use for the query
     *
     * @return int The final page of all selected models
     */
    public function finalPage(SqlSelectOptions $options = new SqlSelectOptions())
    {
        $count = $this->countAll($options);
        if ($count === 0) {
            return 1;
        }

        return ceil($count / $options->pageSize);
    }
}
