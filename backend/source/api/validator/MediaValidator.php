<?php

namespace oml\api\validator;

use oml\api\controller\MediaController;
use oml\api\model\MediaModel;
use oml\php\abstract\Validator;
use WP_REST_Request;

class MediaValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
        $this->controller = MediaController::inject();
    }

    public function create(WP_REST_Request $request)
    {
        $media = new MediaModel();
        return $this->controller->create($media);
    }

    public function get(WP_REST_Request $request)
    {
        $media = new MediaModel();
        $media->id = $request->get_param("id");
        return $this->controller->get($media);
    }

    public function delete(WP_REST_Request $request)
    {
        $media = new MediaModel();
        $media->id = $request->get_param("id");
        return $this->controller->delete($media);
    }

    public function update(WP_REST_Request $request)
    {
        $media = new MediaModel();

        return $this->controller->update($media);
    }

    public function list(WP_REST_Request $request)
    {
        return $this->controller->list();
    }


    // public function validateFile(mixed $param, WP_REST_Request $request, string $name): bool|WP_Error
    // {
    //     [
    //         "name" => $fileName,
    //         "size" => $fileSize
    //     ] = $param;

    //     [
    //         "ext"   => $fileExtension,
    //         "type"  => $fileType
    //     ] = wp_check_filetype($fileName);

    //     if (
    //         isset($this->allowedMimeList[$fileExtension]) === false
    //         || $fileType !== $this->allowedMimeList[$fileExtension]
    //     ) {
    //         return new BadRequestError($name, ERRC::MEDIA_NOT_SUPPORTED);
    //     }

    //     if ($fileSize > OML_API_MAX_FILE_SIZE) {
    //         return new BadRequestError($name, ERRC::MEDIA_SIZE_LIMIT_EXCEEDED);
    //     }

    //     return true;
    // }
}
