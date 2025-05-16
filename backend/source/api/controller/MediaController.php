<?php

namespace oml\api\controller;

use oml\api\model\MediaModel;
use oml\php\enum\ControllerParamErrorCode as ERRC;
use oml\api\repository\MediaRepository;
use oml\api\service\MediaService;
use oml\api\validator\MediaValidator;
use oml\php\abstract\Controller;
use oml\php\core\OkResponse;
use oml\php\core\PageResponse;
use oml\php\core\SqlSelectOptions;
use oml\php\error\BadRequestError;
use oml\php\error\NotFoundError;
use PDO;
use WP_REST_Request;

class MediaController extends Controller
{
    private readonly MediaRepository $repository;
    private readonly MediaService $service;
    private readonly MediaValidator $validator;

    public function __construct()
    {
        parent::__construct();
        $this->repository = MediaRepository::inject();
        $this->service = MediaService::inject();
        $this->validator = MediaValidator::inject();
    }

    public function create(WP_REST_Request $request)
    {
        $model = new MediaModel();
        $model->name = $request->get_param("name");
        $model->description = $request->get_param("description");

        // * ------------------------------------------ *
        // * VALIDATE FILE                              *
        // * ------------------------------------------ *

        if (isset($request->get_file_params()["file"]) === false) {
            return new BadRequestError("file", ERRC::REQUIRED);
        }

        $file = $request->get_file_params()["file"];
        $err = $this->validator->validateFile($file, $request, "file");
        if ($err !== true) {
            return $err;
        }

        // * ------------------------------------------ *

        $model->path = $this->service->upload($file);
        $error = $this->repository->insert($model);

        if (is_wp_error($error)) {
            $this->service->delete($model->path);
            return $error;
        }

        return $this->OK($model);
    }

    public function get(WP_REST_Request $request)
    {
        $id = $request->get_param("id");
        $model = $this->repository->selectById($id);
        return $this->OK($model);
    }

    public function delete(WP_REST_Request $request)
    {
        $id = $request->get_param("id");
        $deleted = $this->repository->deleteById($id);
        return $this->OK($deleted);
    }

    public function update(WP_REST_Request $request)
    {
        $id = $request->get_param("id");
        $model = $this->repository->selectById($id);
        $model->name = $request->get_param("name");
        $model->description = $request->get_param("description");
        $oldFilePath = $model->path;

        // * ------------------------------------------ *
        // * VALIDATE FILE                              *
        // * ------------------------------------------ *

        if (isset($request->get_file_params()["file"])) {
            $file = $request->get_file_params()["file"];
            $err = $this->validator->validateFile($file, $request, "file");
            if ($err !== true) {
                return $err;
            }

            $model->path = $this->service->upload($file);
        }

        // * ------------------------------------------ *

        $error = $this->repository->update($model);

        if (is_wp_error($error)) {
            $this->service->delete($model->path);
            return $error;
        }

        if ($model->path !== $oldFilePath) {
            $this->service->delete($oldFilePath);
        }

        return $this->OK($model);
    }

    public function list(WP_REST_Request $request)
    {
        $indexPage = $request->get_param("indexPage");
        $pageSize = $request->get_param("pageSize");
        $options = new SqlSelectOptions($indexPage, $pageSize);

        if ($request->get_param("search") !== null) {
            $options->where(
                [
                    "query" => 'LOWER(`name`) LIKE LOWER(CONCAT("%", :_search, "%"))',
                    "binds" => [
                        [":_search", $request->get_param("search"), PDO::PARAM_STR]
                    ],
                    "and" => true
                ]
            );
        }

        $options->orderBy("name", "ASC");
        $finalPage = $this->repository->finalPage($options);

        if ($finalPage < $indexPage) {
            return new NotFoundError();
        }

        $items = $this->repository->selectAll($options);
        return new PageResponse($items, $indexPage, $pageSize, $finalPage);
    }
}
