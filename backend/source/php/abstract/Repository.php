<?php

namespace oml\php\abstract;

use oml\php\core\Database;
use oml\php\core\HashMap;
use oml\php\core\SqlSelectOptions;
use oml\php\enum\SqlQueries;
use PDO;

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

    public function insert(mixed $model): ?object
    {
        return null;
    }

    public function update(mixed $model): ?object
    {
        return null;
    }

    public function selectById(int $id, bool $cache = true): false|object
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

    public function deleteById(int $id): bool
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
        return $statement->fetchAll(PDO::FETCH_CLASS, $this->model);
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
