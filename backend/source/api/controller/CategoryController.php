<?php

namespace oml\api\controller;

use oml\api\model\CategoryModel;
use oml\api\repository\CategoryRepository;
use oml\php\abstract\Controller;
use oml\php\core\SqlSelectOptions;

class CategoryController extends Controller
{
    private readonly CategoryRepository $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = CategoryRepository::inject();
    }

    public function create(CategoryModel $category)
    {
        return ($error = $this->repository->insert($category)) && is_wp_error($error)
            ? $error
            : $this->OK($category);
    }

    public function delete(CategoryModel $category)
    {
        return $this->OK($this->repository->deleteById($category->id));
    }

    public function get(CategoryModel $category)
    {
        return $this->OK($this->repository->selectById($category->id));
    }

    public function update(CategoryModel $category)
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
