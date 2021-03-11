<?php


namespace Bx\Model\UI;


class GridActions
{
    /**
     * @var string
     */
    private $code;

    public function __construct(string $code)
    {
        $this->code = $code;
        \CUtil::InitJSCore(['model.ui']);
    }

    /**
     * @param $primaryKey
     * @return string
     */
    public function deleteByPrimaryKey($primaryKey): string
    {
        return $this->reloadTable('POST', [
            'action' => 'delete',
            'id' => $primaryKey,
        ]);
    }

    /**
     * @param string $method
     * @param array $data
     * @return string
     */
    public function reloadTable(string $method, array $data): string
    {
        $data['grid_id'] = $this->code;
        return $this->getInstance().".reloadTableOnCurrentPage('{$method}', ".json_encode($data).")";
    }

    /**
     * @param string $method
     * @param string $action
     * @return string
     */
    public function groupAction(string $method, string $action): string
    {
        return $this->getInstance().".sendGroupAction('{$method}', '{$action}', '{$this->code}')";
    }

    /**
     * @return string
     */
    public function getSelectedIds(): string
    {
        return $this->getInstance().".getSelectedIds()";
    }

    /**
     * @return string
     */
    public function getInstance(): string
    {
        return "loadExtendedGrid('{$this->code}')";
    }
}