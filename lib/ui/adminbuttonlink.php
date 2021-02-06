<?php


namespace Bx\Model\UI;


class AdminButtonLink extends BaseAdminButton
{
    /**
     * @var string
     */
    private $link;

    public function __construct(string $title, string $link, string $icon = '')
    {
        parent::__construct($title, $icon);
        $this->link = $link;
    }

    public function toArray(): array
    {
        return [
            'TEXT'	=> $this->title,
            'TITLE'	=> $this->title,
            'LINK'	=> $this->link,
            'ICON'	=> $this->icon,
        ];
    }
}