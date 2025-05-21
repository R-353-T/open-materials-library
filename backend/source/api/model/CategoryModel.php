<?php

namespace oml\api\model;

class CategoryModel
{
    public ?int $id = null;
    public int $position;
    public string $name;
    public string $description;
    public ?int $parentId = null;
}
