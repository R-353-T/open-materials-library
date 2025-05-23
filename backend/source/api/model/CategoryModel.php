<?php

namespace oml\api\model;

use oml\php\abstract\Model;

class CategoryModel extends Model
{
    public string $description;

    public string $name;

    public ?int $parentId = null;

    public int $position;
}
