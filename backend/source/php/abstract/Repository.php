<?php

namespace oml\php\abstract;

use oml\php\core\Database;
use oml\php\core\HashMap;
use oml\php\core\SqlSelectOptions;
use oml\php\enum\SqlQueries;
use oml\php\error\NotImplementedError;
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

    public function insert(mixed $model): int|WP_Error
    {
        return new NotImplementedError();
    }

    public function update(mixed $model): int|WP_Error
    {
        return new NotImplementedError();
    }

    public function selectById(int $id, bool $cache = true): false|object
    {
        $model = false;

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

    public function deleteById(int $id): false|int
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

    public function selectAll(SqlSelectOptions $options = new SqlSelectOptions()): array
    {
        $statement = Database::$PDO->prepare(SqlQueries::selectAll($this->table, $options));
        $options->applyWhereBinds($statement);
        $statement->execute();
        $modelList = $statement->fetchAll(PDO::FETCH_CLASS, $this->model);

        foreach ($modelList as $model) {
            $this->cache->set($model->id, $model);
        }

        return $modelList;
    }

    public function countAll(SqlSelectOptions $options = new SqlSelectOptions()): int
    {
        $statement = Database::$PDO->prepare(SqlQueries::countAll($this->table, $options));
        $options->applyWhereBinds($statement);
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function finalPage(SqlSelectOptions $options = new SqlSelectOptions()): int
    {
        $count = $this->countAll($options);
        if ($count === 0) {
            return 1;
        }

        return ceil($count / $options->pageSize);
    }
}
