<?php

namespace oml\api\sql;

class QuantityItemSql
{
    public static function insert(string $table): string
    {
        return <<<SQL
        INSERT INTO {$table}
        (
            `quantityId`,
            `value`,
            `position`
        )
        VALUES
        (
            :quantityId,
            :value,
            :position
        )
        SQL;
    }

    public static function update(string $table): string
    {
        return <<<SQL
        UPDATE {$table}
        SET
        `quantityId` = :quantityId,
        `value` = :value,
        `position` = :position
        WHERE `id` = :id
        SQL;
    }

    public static function selectAllByQuantityId(string $table): string
    {
        return "SELECT * FROM {$table} WHERE `quantityId` = :quantityId ORDER BY `position` ASC";
    }

    public static function incrementQuantityPositions(string $table): string
    {
        return <<<SQL
        UPDATE {$table}
        SET `position` = `position` + :_by
        WHERE `quantityId` = :quantityId
        SQL;
    }

    public static function deleteNotInList(string $table, int $count): string
    {
        $query = <<<SQL
        DELETE FROM {$table}
        WHERE `quantityId` = :quantityId 
        AND `id` NOT IN (
        SQL;

        for ($i = 0; $i < $count; $i++) {
            $query .= ":id{$i}";

            if ($i < $count - 1) {
                $query .= ", ";
            }
        }

        return $query . ")";
    }

    public static function randomizeValues(string $table): string
    {
        return <<<SQL
        UPDATE {$table}
        SET `value` = UUID()
        WHERE `quantityId` = :quantityId
        SQL;
    }
}
