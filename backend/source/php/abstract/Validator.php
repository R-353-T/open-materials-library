<?php

namespace oml\php\abstract;

abstract class Validator extends Service
{
    protected object $model;
    protected array $error_list = [];
    protected string $paramaterName = "";
    protected mixed $parameterValue = null;

    public function __construct(string $model_class)
    {
        parent::__construct();
        $this->model = new $model_class();
    }

    protected function addError(string $parameter_name, string $error_code)
    {
        $this->error_list[] = [
            "parameter" => $parameter_name,
            "error" => $error_code
        ];
    }

    protected function hasError(?string $parameter_name = null): bool
    {
        if ($parameter_name === null) {
            return count($this->error_list) > 0;
        }

        foreach ($this->error_list as $error) {
            if ($error["parameter"] === $parameter_name) {
                return true;
            }
        }

        return false;
    }

    protected function initialize(string $parameter_name, mixed $parameter_value): self
    {
        $this->paramaterName = $parameter_name;
        $this->parameterValue = $parameter_value;
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

    public function assign(?object $to = null)
    {
        if ($this->hasError($this->paramaterName) === false) {
            if ($to !== null) {
                $to->{$this->paramaterName} = $this->parameterValue;
            } else {
                $this->model->{$this->paramaterName} = $this->parameterValue;
            }
        }

        $this->paramaterName = "";
        $this->parameterValue = null;
    }
}
