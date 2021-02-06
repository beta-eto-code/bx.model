<?php


namespace Bx\Model\UI;


class SingleAction extends BaseAction
{
    public function toArray(int $id): array
    {
        return [
            'text' => $this->title,
            'onclick' => $this->getJs($id),
        ];
    }

    private function getJs(int $id): string
    {
        if (!empty($this->jsString)) {
            return str_replace('#id#', $id, $this->jsString);
        }

        return $this->helper->reloadTable('POST', [
            'action' => $this->action,
            'type' => 'single',
            'id' => $id,
        ]);
    }

    public function exec(int $id)
    {
        if ($this->callback instanceof \Closure) {
            $callback = $this->callback;
            $callback($id);
        }
    }
}