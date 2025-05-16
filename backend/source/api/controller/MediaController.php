<?php

namespace oml\api\controller;

use oml\api\model\MediaModel;
use oml\api\repository\MediaRepository;
use oml\api\service\MediaService;
use oml\php\abstract\Controller;
use oml\php\core\SqlSelectOptions;
use oml\php\error\NotFoundError;

class MediaController extends Controller
{
    private readonly MediaRepository $repository;
    private readonly MediaService $service;

    public function __construct()
    {
        parent::__construct();
        $this->repository = MediaRepository::inject();
        $this->service = MediaService::inject();
    }

    public function create(MediaModel $media)
    {
        $error = $this->repository->insert($media);

        if (is_wp_error($error)) {
            $this->service->delete($media->path);
            return $error;
        }

        return $this->OK($media);
    }

    public function get(MediaModel $media)
    {
        $model = $this->repository->selectById($media->id);
        return $this->OK($model);
    }

    public function delete(MediaModel $media)
    {
        $deleted = $this->repository->deleteById($media->id);
        return $this->OK($deleted);
    }

    public function update(MediaModel $media)
    {
        $error = $this->repository->update($media);

        if (is_wp_error($error)) {
            $old = $this->repository->selectById($media->id);

            if ($old->path !== $media->path) {
                $this->service->delete($media->path);
            }

            return $error;
        }

        return $this->OK($media);
    }

    public function list(SqlSelectOptions $options)
    {
        $options->orderBy("name", "ASC");
        $finalPage = $this->repository->finalPage($options);

        if ($finalPage < $options->pageIndex) {
            return new NotFoundError();
        }

        $items = $this->repository->selectAll($options);
        return $this->OKPage(
            $items,
            $options->pageIndex,
            $options->pageSize,
            $finalPage
        );
    }
}
