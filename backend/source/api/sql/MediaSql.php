<?php

namespace oml\api\sql;

class MediaSql
{
    public static function insert(string $table): string
    {
        return <<<SQL
        INSERT INTO {$table}
        (
            `name`,
            `description`,
            `path`
        )
        VALUES 
        (
            :name,
            :description,
            :path
        )
        SQL;
    }

    public static function update(string $table): string
    {
        return <<<SQL
        UPDATE {$table}
        SET
        `name` = :name,
        `description` = :description,
        `path` = :path
        WHERE `id` = :id
        SQL;
    }
}
