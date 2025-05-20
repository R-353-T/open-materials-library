<?php

namespace oml\api\controller;

use oml\api\model\EnumeratorModel;
use oml\api\repository\EnumeratorItemRepository;
use oml\api\repository\EnumeratorRepository;
use oml\php\abstract\Controller;
use oml\php\abstract\Repository;
use oml\php\core\SqlSelectOptions;
use oml\php\error\NotFoundError;

class EnumeratorController extends Controller
{
    private readonly EnumeratorRepository $repository;
    private readonly EnumeratorItemRepository $itemRepository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = EnumeratorRepository::inject();
    }

    public function create(EnumeratorModel $enumerator)
    {
        $error = $this->repository->insert($enumerator);

        if (is_wp_error($error)) {
            return $error;
        }

        return $this->OK($enumerator);
    }

    public function delete(EnumeratorModel $enumerator)
    {
        $deleted = $this->repository->deleteById($enumerator->id);
        return $this->OK($deleted);
    }

    public function get(EnumeratorModel $enumerator)
    {
        $enumerator = $this->repository->selectById($enumerator->id);
        $enumerator->items = $this->itemRepository->selectAllByEnumeratorId($enumerator->id);
        return $this->OK($enumerator);
    }

    public function update(EnumeratorModel $enumerator)
    {
        $error = $this->repository->update($enumerator);

        if (is_wp_error($error)) {
            return $error;
        }

        return $this->OK($enumerator);
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
