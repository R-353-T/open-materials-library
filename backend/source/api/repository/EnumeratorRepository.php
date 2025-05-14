<?php

namespace oml\api\repository;

use oml\api\model\EnumeratorModel;
use oml\php\abstract\Repository;

class EnumeratorRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(OML_SQL_ENUMERATOR_TABLENAME, EnumeratorModel::class);
    }
}
