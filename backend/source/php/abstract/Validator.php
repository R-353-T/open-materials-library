<?php

namespace oml\php\abstract;

abstract class Validator extends Service
{
    protected object $model;
    protected array $error_list = [];
    protected string $paramaterName = "";
    protected mixed $parameterValue = null;
    protected ?string $propertyName = null;
    protected ?int $propertyIndex = null;

    public function __construct(string $model_class)
    {
        parent::__construct();
        $this->model = new $model_class();
    }

    protected function addError(string $parameter_name, string $error_code)
    {
        $error = [
            "parameter" => $parameter_name,
            "error" => $error_code
        ];

        if ($this->propertyName !== null) {
            $error["property"] = $this->propertyName;
        }

        if ($this->propertyIndex !== null) {
            $error["index"] = $this->propertyIndex;
        }

        $this->error_list[] = $error;
    }

    protected function hasError(null|string|array $parameter_name = null): bool
    {
        if ($parameter_name === null) {
            return count($this->error_list) > 0;
        }

        foreach ($this->error_list as $error) {
            if (is_array($parameter_name) === false) {
                if ($error["parameter"] === $parameter_name) {
                    return true;
                }
            } else {
                if (in_array($error["parameter"], $parameter_name)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function initialize(
        string $parameter_name,
        mixed $parameter_value,
        ?string $property_name = null,
        ?int $property_index = null
    ): self {
        $this->paramaterName = $parameter_name;
        $this->parameterValue = $parameter_value;
        $this->propertyName = $property_name;
        $this->propertyIndex = $property_index;
        return $this;
    }

    protected function validate(string $callback, array $args = []): self
    {
        if ($this->hasError($this->paramaterName) === false) {
            $validator_result = call_user_func($callback, $this->parameterValue, ...$args);

            if ($validator_result[0] === false) {
                $this->addError($this->paramaterName, $validator_result[1]);
                return $this;
            }

            $this->parameterValue = $validator_result[1];
        }

        return $this;
    }

    public function assign(?object $to = null): void
    {
        if ($this->hasError($this->paramaterName) === false) {
            $property_name = $this->propertyName ?? $this->paramaterName;

            if ($to !== null) {
                $to->{$property_name} = $this->parameterValue;
            } else {
                $this->model->{$property_name} = $this->parameterValue;
            }
        }

        $this->paramaterName = "";
        $this->parameterValue = null;
    }

    public function validateId(
        string $parameter_name,
        mixed $parameter_value,
        object $repository
    ): void {
        $this->initialize($parameter_name, $parameter_value)
            ->validate("validator__is_required")
            ->validate("validator__database__index", [$repository])
            ->assign();
    }

    public function validateName(
        string $parameter_name,
        mixed $parameter_value,
        object $repository,
        ?int $id = null
    ): void {
        $this->initialize($parameter_name, $parameter_value)
            ->validate("validator__is_required")
            ->validate("validator__dabatase__name", [$repository, $id])
            ->assign();
    }

    public function validateDescription(
        string $parameter_name,
        mixed $parameter_value
    ): void {
        $this->initialize($parameter_name, $parameter_value)
            ->validate("validator__is_required")
            ->validate("validator__database__description")
            ->assign();
    }
}
