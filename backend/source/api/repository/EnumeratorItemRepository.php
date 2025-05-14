<?php

namespace oml\api\repository;

use oml\api\model\EnumeratorItemModel;
use oml\php\abstract\Repository;

class EnumeratorItemRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(OML_SQL_ENUMERATOR_ITEM_TABLENAME, EnumeratorItemModel::class);
    }
}
