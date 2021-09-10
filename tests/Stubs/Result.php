<?php

namespace Bitrix\Main;

class Result
{
    private $errors = [];
    private $data;

    public function isSuccess(): bool
    {
        return !empty($this->errors);
    }

    public function addError(Error $error): Result
    {
        $this->errors[] = $error;
        return $this;
    }

    public function addErrors(array $errors): Result
    {
        foreach ($errors as $error) {
            $this->errors[] = $error;
        }

        return $this;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorMessages(): array
    {
        return array_map(function (Error $error) {
            return $error->getMessage();
        }, $this->errors);
    }
}