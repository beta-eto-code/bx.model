<?php


namespace Bx\Model\UI;


use Bitrix\Main\Grid\Panel\Actions;
use Bitrix\Main\Grid\Panel\Snippet\Onchange;
use Closure;

class GroupAction extends BaseAction
{

    /**
     * @return array
     */
    public function toArray(): array
    {
        $onchange = new Onchange();
        $onchange->addAction(
            [
                'ACTION' => Actions::CALLBACK,
                'CONFIRM' => $this->useConfirm,
                'CONFIRM_APPLY_BUTTON'  => $this->confirmBtn ?? '',
                'DATA' => [
                    [
                        'JS' => !empty($this->jsString) ?
                            $this->jsString :
                            $this->helper->groupAction('POST', $this->action)
                    ]
                ]
            ]
        );
        $jsAction = $onchange->toArray();

        return [
            'ID' => $this->action,
            'TYPE' => 'BUTTON',
            'TEXT' => $this->title,
            'CLASS' => $this->class,
            'ONCHANGE' => $jsAction,
        ];
    }

    public function exec(array $ids)
    {
        if ($this->callback instanceof Closure) {
            $callback = $this->callback;
            $callback($ids);
        }
    }
}