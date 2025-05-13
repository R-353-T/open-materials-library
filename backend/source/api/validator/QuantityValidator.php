<?php

namespace oml\api\validator;

use oml\api\repository\QuantityRepository;
use oml\php\abstract\Validator;
use oml\php\enum\ControllerParamErrorCode as ERRC;
use oml\php\error\BadRequestError;
use WP_REST_Request;

class QuantityValidator extends Validator
{
    private readonly QuantityItemValidator $itemValidator;

    public function __construct()
    {
        $this->repository = QuantityRepository::inject();
        $this->itemValidator = QuantityItemValidator::inject();
    }

    public function validateItems(mixed $items, WP_REST_Request $request, string $name)
    {
        if (oml_validate_array($items) === false) {
            return new BadRequestError($name, ERRC::INVALID_TYPE);
        }

        foreach ($items as $index => $item) {
            if (
                isset($item["id"])
                && ($err = $this->itemValidator->validateId($item["id"], $request, ["items", $index]))
                && is_wp_error($err)
            ) {
                return $err;
            }

            if (
                ($err = $this->itemValidator->validateValue($item, $request, ["items", $index]))
                && is_wp_error($err)
            ) {
                return $err;
            }
        }

        return true;
    }
}
