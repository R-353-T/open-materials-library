<?php

namespace oml\api\model;

class DatasheetCategoryModel
{
    public ?int $id = null;
    public int $position;
    public string $name;
    public string $description;
    public ?int $parentId = null;
}
