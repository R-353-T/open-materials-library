<?php

namespace oml\api\controller;

use oml\api\model\EnumeratorModel;
use oml\api\model\ValueModel;
use oml\api\repository\EnumeratorRepository;
use oml\php\abstract\Controller;
use oml\php\abstract\Repository;
use oml\php\core\SqlSelectOptions;
use oml\php\error\NotFoundError;

class EnumeratorController extends Controller
{
    private readonly EnumeratorRepository $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = EnumeratorRepository::inject();
    }

    public function create(EnumeratorModel $enumerator)
    {
        return ($error = $this->repository->insert($enumerator)) && is_wp_error($error)
            ? $error
            : $this->OK($this->toDTO($enumerator));
    }

    public function delete(EnumeratorModel $enumerator)
    {
        return $this->OK($this->repository->deleteById($enumerator->id));
    }

    public function get(EnumeratorModel $enumerator)
    {
        $enumerator = $this->repository->selectByIdWithItems($enumerator->id);
        return $this->OK($this->toDTO($enumerator));
    }

    public function update(EnumeratorModel $enumerator)
    {
        return ($error = $this->repository->update($enumerator)) && is_wp_error($error)
            ? $error
            : $this->OK($this->toDTO($enumerator));
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

    private function toDTO(EnumeratorModel $enumerator)
    {
        if (isset($enumerator->items)) {
            $enumerator->items = array_map([ValueModel::class, "fromEnumeratorItem"], $enumerator->items);
        }

        return $enumerator;
    }
}
