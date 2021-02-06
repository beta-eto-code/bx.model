<?php


namespace Bx\Model\UI;


use Bitrix\Main\Grid\Panel\Actions;
use Bitrix\Main\Grid\Panel\Snippet\Onchange;
use Closure;

abstract class BaseAction
{
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $action;
    /**
     * @var string
     */
    protected $class;
    /**
     * @var Closure
     */
    protected $callback;
    /**
     * @var bool
     */
    protected $useConfirm;
    /**
     * @var string
     */
    protected $confirmBtn;
    /**
     * @var GridActions
     */
    protected $helper;
    /**
     * @var string
     */
    protected $jsString;

    public function __construct(GridActions $helper, string $title, string $action, string $cssClass = '')
    {
        $this->helper = $helper;
        $this->title = $title;
        $this->action = $action;
        $this->class = $cssClass;
    }

    /**
     * @param string $confirmBtn
     * @return $this
     */
    public function useConfirm(string $confirmBtn): BaseAction
    {
        $this->useConfirm = true;
        $this->confirmBtn = $confirmBtn;
        return $this;
    }

    public function setJs(string $jsString): BaseAction
    {
        $this->jsString = $jsString;
        return $this;
    }

    //abstract public function toArray(): array;

    /**
     * @param Closure $func
     * @return $this
     */
    public function setCallback(Closure $func): BaseAction
    {
        $this->callback = $func;
        return $this;
    }
}