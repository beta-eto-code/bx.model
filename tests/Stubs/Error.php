<?php

namespace Bitrix\Main;

class Error
{
    /**
     * @var string
     */
    private $message;
    private $code;
    private $customData;

    public function __construct(string $message, $code = null, $customData = null)
    {
        $this->message = $message;
        $this->code = $code;
        $this->customData = $customData;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode()
    {
        return $this->code;
    }
}