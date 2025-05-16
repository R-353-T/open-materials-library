<?php

namespace oml\php\dal;

use oml\php\core\Database;
use oml\php\enum\SqlQueries;
use PDO;

trait SelectByName
{
    public function selectByName(string $name): false|object
    {
        $statement = Database::$PDO->prepare(SqlQueries::selectByName($this->table));
        $statement->bindValue(":name", $name, PDO::PARAM_STR);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->model);
        $model = $statement->fetch();

        if ($model && $this->cache->contains($model->id) === false) {
            $this->cache->set($model->id, $model);
        }

        return $model;
    }
}
