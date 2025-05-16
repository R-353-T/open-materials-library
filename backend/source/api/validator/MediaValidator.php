<?php

namespace oml\api\validator;

use oml\api\controller\MediaController;
use oml\api\enum\APIError;
use oml\api\model\MediaModel;
use oml\api\repository\MediaRepository;
use oml\api\service\MediaService;
use oml\php\abstract\Validator;
use oml\php\core\SqlSelectOptions;
use oml\php\error\BadRequestError;
use PDO;
use WP_REST_Request;

class MediaValidator extends Validator
{
    private readonly MediaController $controller;
    private readonly MediaService $service;
    private readonly MediaRepository $repository;

    public function __construct()
    {
        parent::__construct();
        $this->controller = MediaController::inject();
        $this->service = MediaService::inject();
    }

    public function create(WP_REST_Request $request)
    {
        $errors = [];
        $media = new MediaModel();

        # Name
        $name = $request->get_param("name");
        $name = oml_validate_name($name, $this->repository);
        self::apply($name, $errors, $media, "name");

        # Description
        $description = $request->get_param("description");
        $description = oml_validate_description($description);
        self::apply($description, $errors, $media, "description");

        # File (image)
        $files = $request->get_file_params();
        $file = oml_validate_image($files);

        if ($file[0]) {
            $media->path = $this->service->upload($file);
        } else {
            $errors["file"] = $file[1];
        }

        if (count($errors) > 0) {
            return new BadRequestError($errors);
        }

        return $this->controller->create($media);
    }

    public function get(WP_REST_Request $request)
    {
        $errors = [];
        $media = new MediaModel();

        # Id
        $id = $request->get_param("id");
        $id = oml_validate_database_index($id, $this->repository);
        self::apply($id, $errors, $media, "id");

        if (count($errors) > 0) {
            return new BadRequestError($errors);
        }

        return $this->controller->get($media);
    }

    public function delete(WP_REST_Request $request)
    {
        $errors = [];
        $media = new MediaModel();

        # Id
        $id = $request->get_param("id");
        $id = oml_validate_database_index($id, $this->repository);
        self::apply($id, $errors, $media, "id");

        if (count($errors) > 0) {
            return new BadRequestError($errors);
        }

        return $this->controller->delete($media);
    }

    public function update(WP_REST_Request $request)
    {
        $errors = [];
        $media = new MediaModel();

        # Id
        $id = $request->get_param("id");
        $id = oml_validate_database_index($id, $this->repository);
        self::apply($id, $errors, $media, "id");
        $media = $this->repository->selectById($media->id);

        # Name
        $name = $request->get_param("name");
        $name = oml_validate_name($name, $this->repository);
        self::apply($name, $errors, $media, "name");

        # Description
        $description = $request->get_param("description");
        $description = oml_validate_description($description);
        self::apply($description, $errors, $media, "description");

        # File (image)
        $files = $request->get_file_params();
        $file = oml_validate_image($files, false);

        if ($file[0] && $file[1] !== null) {
            $media->path = $this->service->upload($file);
        } else {
            $errors["file"] = $file[1];
        }

        if (count($errors) > 0) {
            return new BadRequestError($errors);
        }

        return $this->controller->update($media);
    }

    public function list(WP_REST_Request $request)
    {
        $errors = [];
        $options = new SqlSelectOptions();

        # Pagination Index
        $index = $request->get_param("pageIndex");
        $index = oml_validate_pagination_index($index);
        self::apply($index, $errors, $options, "pageIndex");

        # Pagination Size
        $size = $request->get_param("pageSize");
        $size = oml_validate_pagination_size($size);
        self::apply($index, $errors, $options, "pageSize");

        # Search
        $search = $request->get_param("search");
        if ($search !== null) {
            if (is_string($search) === false) {
                $errors["search"] = APIError::PARAMETER_INVALID;
            } else {
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
        }

        if (count($errors) > 0) {
            return new BadRequestError($errors);
        }

        return $this->controller->list($options);
    }
}
