<?php

namespace oml\api\validator;

use oml\php\enum\ControllerParamErrorCode as ERRC;
use oml\api\repository\DatasheetMediaRepository;
use oml\php\abstract\Validator;
use oml\php\error\BadRequestError;
use WP_Error;
use WP_REST_Request;

class DatasheetMediaValidator extends Validator
{
    private array $allowedMimeList = [
        "png"   => "image/png",
        "jpg"   => "image/jpeg",
        "jpeg"  => "image/jpeg",
    ];

    public function __construct()
    {
        $this->repository = DatasheetMediaRepository::inject();
    }

    /**
     * Validates if the given value is a valid file
     *
     * @param mixed $param Value to be validated
     * @param WP_REST_Request $request The current HTTP request
     * @param string $name The name of the element to be validated
     *
     * @return bool|WP_Error Returns true if it is valid, otherwise returns a WP_Error
     */
    public function validateFile(mixed $param, WP_REST_Request $request, string $name): bool|WP_Error
    {
        [
            "name" => $fileName,
            "size" => $fileSize
        ] = $param;

        [
            "ext"   => $fileExtension,
            "type"  => $fileType
        ] = wp_check_filetype($fileName);

        if (
            isset($this->allowedMimeList[$fileExtension]) === false
            || $fileType !== $this->allowedMimeList[$fileExtension]
        ) {
            return new BadRequestError($name, ERRC::MEDIA_NOT_SUPPORTED);
        }

        if ($fileSize > OML_API_MAX_FILE_SIZE) {
            return new BadRequestError($name, ERRC::MEDIA_SIZE_LIMIT_EXCEEDED);
        }

        return true;
    }
}
