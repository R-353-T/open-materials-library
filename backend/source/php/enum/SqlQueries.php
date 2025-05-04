<?php

namespace oml\php\enum;

use oml\php\core\SqlSelectOptions;

class SqlQueries
{
    public static function selectById(string $table): string
    {
        return "SELECT * FROM {$table} WHERE `id` = :id";
    }

    public static function deleteById(string $table): string
    {
        return "DELETE FROM {$table} WHERE `id` = :id";
    }

    public static function selectAll(string $table, SqlSelectOptions $options): string
    {
        return <<<SQL
        SELECT * FROM {$table}
        {$options->getWhereQuery()}
        {$options->getOrderByQuery()}
        {$options->getLimitAndOffset()}
        SQL;
    }

    public static function countAll(string $table, SqlSelectOptions $options): string
    {
        return "SELECT COUNT(*) FROM {$table} {$options->getWhereQuery()}";
    }

    # Datasheet Media

    public static function insertMedia(string $table): string
    {
        return <<<SQL
        INSERT INTO {$table} (`name`, `description`, `path`)
        VALUES (:name, :description, :path)
        SQL;
    }

    public static function updateMedia(string $table): string
    {
        return <<<SQL
        UPDATE {$table}
        SET `name` = :name, `description` = :description, `path` = :path
        WHERE `id` = :id
        SQL;
    }
}
