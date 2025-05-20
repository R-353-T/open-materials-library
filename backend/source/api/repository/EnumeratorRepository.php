<?php

namespace oml\api\repository;

use oml\api\model\EnumeratorModel;
use oml\php\abstract\Repository;
use oml\php\dal\CountAll;
use oml\php\dal\DeleteById;
use oml\php\dal\SelectAll;
use oml\php\dal\SelectById;
use oml\php\dal\SelectByName;

class EnumeratorRepository extends Repository
{
    use SelectById;
    use SelectAll;
    use SelectByName;
    use DeleteById;
    use CountAll;

    public function __construct()
    {
        parent::__construct(___DB_ENUMERATOR___, EnumeratorModel::class);
    }
}
