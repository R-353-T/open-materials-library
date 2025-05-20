<?php

namespace oml\api\repository;

use oml\api\model\TypeModel;
use oml\php\abstract\Repository;
use oml\php\core\Database;
use oml\php\core\SqlSelectOptions;
use PDO;

class TypeRepository extends Repository
{
    private readonly string $typeInputTable;

    public function __construct()
    {
        parent::__construct(___DB_TYPE___, TypeModel::class);
        $this->typeInputTable = ___DB_TYPE_INPUT___;
    }

    public function selectAll(SqlSelectOptions $options = new SqlSelectOptions()): array
    {
        $statement = Database::$PDO->prepare(<<<SQL
        SELECT
            ty.`id`,
            ty.`name`,
            ty.`column`,
            ti.`name` as `input`
        FROM {$this->table} ty
        JOIN {$this->typeInputTable} ti ON ti.`id` = ty.`inputId`
        ORDER BY ty.`id` ASC
        SQL);

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, $this->model);
    }
}
