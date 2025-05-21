<?php

namespace oml\api\controller;

use oml\api\model\DatasheetCategoryModel;
use oml\api\repository\DatasheetCategoryRepository;
use oml\php\abstract\Controller;
use oml\php\core\SqlSelectOptions;

class DatasheetCategoryController extends Controller
{
    private readonly DatasheetCategoryRepository $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = DatasheetCategoryRepository::inject();
    }

    public function create(DatasheetCategoryModel $category)
    {
        return ($error = $this->repository->insert($category)) && is_wp_error($error)
            ? $error
            : $this->OK($category);
    }

    public function delete(DatasheetCategoryModel $category)
    {
        return $this->OK($this->repository->deleteById($category->id));
    }

    public function get(DatasheetCategoryModel $category)
    {
        return $this->OK($this->repository->selectById($category->id));
    }

    public function update(DatasheetCategoryModel $category)
    {
        return ($error = $this->repository->update($category)) && is_wp_error($error)
            ? $error
            : $this->OK($category);
    }

    public function list(SqlSelectOptions $options)
    {
        $options->orderBy("position", "ASC");
        $options->orderBy("name", "ASC");

        return $this->OK($this->repository->selectAll($options));
    }
}
