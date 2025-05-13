<?php

namespace oml\php\enum;

use oml\php\core\SqlSelectOptions;

class SqlQueries
{
    # SELECT

    public static function selectById(string $table): string
    {
        return "SELECT * FROM {$table} WHERE `id` = :id";
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

    public static function selectByName(string $table): string
    {
        return "SELECT * FROM {$table} WHERE `name` = :name";
    }

    # COUNT

    public static function countAll(string $table, SqlSelectOptions $options): string
    {
        return "SELECT COUNT(*) FROM {$table} {$options->getWhereQuery()}";
    }

    # DELETE

    public static function deleteById(string $table): string
    {
        return "DELETE FROM {$table} WHERE `id` = :id";
    }
}
