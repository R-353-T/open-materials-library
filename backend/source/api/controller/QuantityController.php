<?php

namespace oml\api\controller;

use oml\api\model\QuantityModel;
use oml\api\repository\QuantityItemRepository;
use oml\api\repository\QuantityRepository;
use oml\php\abstract\Controller;
use oml\php\abstract\Repository;
use oml\php\core\SqlSelectOptions;
use oml\php\error\NotFoundError;

class QuantityController extends Controller
{
    private readonly QuantityRepository $repository;
    private readonly QuantityItemRepository $itemRepository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = QuantityRepository::inject();
        $this->itemRepository = QuantityItemRepository::inject();
    }

    public function create(QuantityModel $quantity)
    {
        $error = $this->repository->insert($quantity);

        if (is_wp_error($error)) {
            return $error;
        }

        return $this->OK($quantity);
    }

    public function delete(QuantityModel $quantity)
    {
        $deleted = $this->repository->deleteById($quantity->id);
        return $this->OK($deleted);
    }

    public function get(QuantityModel $quantity)
    {
        $quantity = $this->repository->selectById($quantity->id);
        $quantity->items = $this->itemRepository->selectAllByQuantityId($quantity->id);
        return $this->OK($quantity);
    }

    public function update(QuantityModel $quantity)
    {
        $error = $this->repository->update($quantity);

        if (is_wp_error($error)) {
            return $error;
        }

        return $this->OK($quantity);
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
        return $this->OKPage(
            $items,
            $options->pageIndex,
            $options->pageSize,
            $final
        );
    }
}
