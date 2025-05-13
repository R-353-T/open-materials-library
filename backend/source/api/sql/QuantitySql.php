<?php

namespace oml\api\sql;

class QuantitySql
{
    public static function insert(string $table): string
    {
        return <<<SQL
        INSERT INTO {$table}
        (
            `name`,
            `description`
        )
        VALUES
        (
            :name,
            :description
        )
        SQL;
    }

    public static function update(string $table): string
    {
        return <<<SQL
        UPDATE {$table}
        SET
        `name` = :name,
        `description` = :description
        WHERE `id` = :id
        SQL;
    }
}
