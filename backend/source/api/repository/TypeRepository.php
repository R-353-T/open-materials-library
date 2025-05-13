<?php

namespace oml\api\repository;

use oml\api\model\TypeModel;
use oml\api\sql\TypeSql;
use oml\php\abstract\Repository;
use oml\php\core\Database;
use oml\php\core\SqlSelectOptions;
use PDO;

class TypeRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(OML_SQL_TYPE_TABLENAME, TypeModel::class);
    }

    public function selectAll(SqlSelectOptions $options = new SqlSelectOptions())
    {
        $statement = Database::$PDO->prepare(TypeSql::selectAll($this->table));
        $statement->execute();
        $modelList = $statement->fetchAll(PDO::FETCH_CLASS, $this->model);

        foreach ($modelList as $model) {
            $this->cache->set($model->id, $model);
        }

        return $modelList;
    }
}
