<?php

namespace oml\php\abstract;

use oml\php\core\Database;
use oml\php\error\NotImplementedError;
use WP_Error;

abstract class Repository extends Service
{
    public static function getFinalPageCount(int $count, int $pageSize): int
    {
        return $count > 1 ? ceil($count / $pageSize) : 1;
    }
    public readonly string $table;
    public readonly string $model;

    public function __construct(string $table, string $model)
    {
        $this->table = $table;
        $this->model = $model;
        Database::initializeDatabase();
    }

    public function insert(mixed $model): int|WP_Error
    {
        return new NotImplementedError();
    }

    public function update(mixed $model): int|WP_Error
    {
        return new NotImplementedError();
    }
}
