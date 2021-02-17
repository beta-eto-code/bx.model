<?php


namespace Bx\Model\UI\Admin\Form;


class FormTab
{
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $code;
    /**
     * @var string
     */
    private $icon;

    public function __construct(string $code, string $title, string $icon = '')
    {
        $this->code = $code;
        $this->title = $title;
        $this->icon = $icon;
    }

    public function addField(string $code, string $title)
    {

    }

    public function toArray(): array
    {
        return [];
    }
}