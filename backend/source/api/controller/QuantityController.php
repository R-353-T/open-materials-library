<?php

namespace oml\api\controller;

use oml\api\model\QuantityModel;
use oml\api\model\ValueModel;
use oml\api\repository\QuantityRepository;
use oml\php\abstract\Controller;
use oml\php\abstract\Repository;
use oml\php\core\SqlSelectOptions;
use oml\php\error\NotFoundError;

class QuantityController extends Controller
{
    private readonly QuantityRepository $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = QuantityRepository::inject();
    }

    public function create(QuantityModel $quantity)
    {
        return ($error = $this->repository->insert($quantity)) && is_wp_error($error)
            ? $error
            : $this->OK($this->toDTO($quantity));
    }

    public function delete(QuantityModel $quantity)
    {
        return $this->OK($this->repository->deleteById($quantity->id));
    }

    public function get(QuantityModel $quantity)
    {
        $quantity = $this->repository->selectByIdWithItems($quantity->id);
        return $this->OK($this->toDTO($quantity));
    }

    public function update(QuantityModel $quantity)
    {
        return ($error = $this->repository->update($quantity)) && is_wp_error($error)
            ? $error
            : $this->OK($this->toDTO($quantity));
    }

    public function list(SqlSelectOptions $options)
    {
        $options->orderBy("name", "ASC");
        $count = $this->repository->countAll($options);
        $final = Repository::getFinalPageCount($count, $options->pageSize);

        if ($final < $options->pageIndex) {
            return new NotFoundError();
        }

        $items = $this->repository->selectAll($options);
        $items = array_map([$this, "toDTO"], $items);
        return $this->OKPage(
            $items,
            $options->pageIndex,
            $options->pageSize,
            $final
        );
    }

    private function toDTO(QuantityModel $quantity)
    {
        if (isset($quantity->items)) {
            $quantity->items = array_map([ValueModel::class, "fromQuantityItem"], $quantity->items);
        }

        return $quantity;
    }
}
