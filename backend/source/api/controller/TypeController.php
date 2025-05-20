<?php

namespace oml\api\controller;

use oml\api\repository\TypeRepository;
use oml\php\abstract\Controller;

class TypeController extends Controller
{
    private readonly TypeRepository $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = TypeRepository::inject();
    }

    public function list()
    {
        $items = $this->repository->selectAll();
        return $this->OKPage($items, 1, ___MAX_PAGE_SIZE___, 1);
    }
}
