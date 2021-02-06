<?php

namespace Bx\Model\UI;

abstract class BaseAdminButton
{
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $icon;

    public function __construct(string $title, string $icon)
    {
        $this->title = $title;
        $this->icon = $icon;
    }

    abstract public function toArray(): array;
}