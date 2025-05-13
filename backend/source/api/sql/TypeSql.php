<?php

namespace oml\api\sql;

class TypeSql
{
    public static function selectAll(string $table): string
    {
        return <<<SQL
        SELECT
            ty.`id`,
            ty.`name`,
            ty.`column`,
            ti.`name` as `input`
        FROM {$table} ty
        JOIN `oml_type_input` ti ON ti.`id` = ty.`inputId`
        ORDER BY ty.`id` ASC
        SQL;
    }
}
