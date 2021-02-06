<?php


namespace Bx\Model\UI;


use Closure;

class AdminButtonAction extends BaseAdminButton
{
    /**
     * @var string
     */
    private $action;
    /**
     * @var Closure
     */
    private $callback;
    /**
     * @var GridActions
     */
    private $actionHelper;
    /**
     * @var string
     */
    private $jsString;

    public function __construct(GridActions $actionHelper, string $title, string $action, string $icon = '')
    {
        parent::__construct($title, $icon);
        $this->action = $action;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @param Closure $func
     * @return $this
     */
    public function setCallback(Closure $func): AdminButtonAction
    {
        $this->callback = $func;
        return $this;
    }

    /**
     * @param string $jsString
     * @return $this
     */
    public function setJs(string $jsString): AdminButtonAction
    {
        $this->jsString = $jsString;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'TEXT'	=> $this->title,
            'TITLE'	=> $this->title,
            'LINK'	=> $this->link,
            'ONCLICK' => !empty($this->jsString) ?
                $this->jsString :
                $this->actionHelper->groupAction('POST', $this->action),
        ];
    }
}