<?php

namespace oml\api\validator;

use oml\api\repository\QuantityItemRepository;
use oml\api\repository\QuantityRepository;
use oml\php\abstract\Validator;
use oml\php\enum\ControllerParamErrorCode as ERRC;
use oml\php\error\BadRequestError;
use WP_Error;
use WP_REST_Request;

class QuantityItemValidator extends Validator
{
    private QuantityRepository $quantityRepository;

    public function __construct()
    {
        $this->repository = QuantityItemRepository::inject();
        $this->quantityRepository = QuantityRepository::inject();
    }

    public function validateId(mixed $value, WP_REST_Request $request, string|array $name): bool|WP_Error
    {
        if (
            ($err = parent::validateId($value, $request, $name))
            && is_wp_error($err)
        ) {
            return $err;
        }

        if (
            ($err = $this->idInQuantity($value, $request, $name))
            && is_wp_error($err)
        ) {
            return $err;
        }

        return true;
    }

    public function validateValue(mixed $item, WP_REST_Request $request, string|array $name)
    {
        $value = $item["value"];

        if (oml_sanitize_string($value) === null) {
            return new BadRequestError($name, ERRC::INVALID_TYPE);
        }

        if (strlen(oml_sanitize_string($value)) < OML_API_MIN_NAME_LENGTH) {
            return new BadRequestError($name, ERRC::TOO_SHORT);
        }

        if (strlen($value) > OML_API_MAX_LABEL_LENGTH) {
            return new BadRequestError($name, ERRC::TOO_LONG);
        }

        $items = $request->get_param("items");
        $count = array_filter($items, fn($i) => strtolower($i["value"]) === strtolower($value));
        if (count($count) > 1) {
            return new BadRequestError($name, ERRC::DOUBLE);
        }

        return true;
    }

    private function idInQuantity(mixed $value, WP_REST_Request $request, string|array $name): bool|WP_Error
    {
        if (
            ($quantityId = $request->get_param("id"))
            && $quantityId !== null
            && ($quantity = $this->quantityRepository->selectById($quantityId))
            && $quantity !== false
        ) {
            $in = array_filter(
                $quantity->items,
                function ($item) use ($value) {
                    return $item->id === $value;
                }
            );

            if (count($in) === 0) {
                return new BadRequestError($name, ERRC::INVALID_DATABASE_RELATION);
            }
        }

        return true;
    }
}
