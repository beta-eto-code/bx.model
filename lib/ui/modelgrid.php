<?php

namespace Bx\Model\UI;

use Bitrix\Main\Application;
use Bitrix\Main\Grid\Options;
use Bitrix\Main\UI\PageNavigation;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\ModelQueryInterface;
use Bx\Model\Interfaces\ModelServiceInterface;
use Bx\Model\ModelCollection;
use Bx\Model\Services\ProxyService;
use Bitrix\Main\Request;
use Bitrix\Main\HttpRequest;
use CAjax;
use Closure;


class ModelGrid
{
    /**
     * @var ProxyService|ModelServiceInterface
     */
    private $modelService;
    /**
     * @var BaseGridColumn[]
     */
    private $columnList;
    /**
     * @var int[]
     */
    private $pageSizes;
    /**
     * @var array
     */
    private $sortList;
    /**
     * @var string
     */
    private $code;
    /**
     * @var HttpRequest|Request
     */
    private $request;
    /**
     * @var Options
     */
    private $gridOptions;
    /**
     * @var string
     */
    private $primaryKey;
    /**
     * @var GridActions
     */
    private $gridActions;
    /**
     * @var GroupAction[]
     */
    private $groupActions;
    /**
     * @var SingleAction[]
     */
    private $singleActions;
    /**
     * @var ModelFilter
     */
    private $filter;

    public function __construct(ModelServiceInterface $modelService, string $code, string $primaryKey = 'ID')
    {
        $this->request = Application::getInstance()->getContext()->getRequest();
        $this->code = $code;
        $this->modelService = $modelService instanceof ProxyService ? $modelService : new ProxyService($modelService);
        $this->gridOptions = new Options($code);
        $this->primaryKey = $primaryKey;
        $this->gridActions = new GridActions($code);
        $this->pageSizes = [
            5,
            10,
            20,
            50,
            100,
        ];

        $this->setSingleAction('Удалить', 'delete', 'icon remove')
            ->useConfirm('Удалить')
            ->setCallback(function (int $id) {
                $this->modelService->delete($id);
            });

        $this->setGroupAction(
            'Удалить',
            'delete',
            'icon remove')
            ->useConfirm('Удалить')
            ->setCallback(function (array $ids) {
                foreach ($ids as $id) {
                    $this->modelService->delete((int)$id);
                }
            });
    }

    public function createFilter(): ModelFilter
    {
        return $this->filter = new ModelFilter($this->modelService, $this->code);
    }

    /**
     * @param string $id
     * @param string $title
     * @param null $sort
     * @param bool $isDefault
     */
    public function addColumn(string $id, string $title, $sort = null, bool $isDefault = true)
    {
        if (!empty($sort)) {
            $this->sortList[] = $sort;
            if (is_array($sort)) {
                $sort = key($sort);
            }
        } else {
            $sort = $id;
            $this->sortList[] = $sort;
        }

        $this->columnList[$id] = new GridColumn($id, $title, $sort, $isDefault);
    }

    /**
     * @param string $id
     * @param Closure $func
     * @param string $title
     * @param null $sort
     * @param bool $isDefault
     */
    public function addCalculateColumn(string $id, Closure $func, string $title, $sort = null, bool $isDefault = true)
    {
        if (!empty($sort)) {
            if (is_array($sort)) {
                $this->sortList = array_merge($this->sortList ?? [], $sort);
                $sort = key($sort);
            } else {
                $this->sortList[] = $sort;
            }
        } else {
            $sort = $id;
            $this->sortList[] = $sort;
        }

        $this->columnList[$id] = new GridCalculateColumn($id, $func, $title, $sort, $isDefault);
    }

    /**
     * @param int ...$pageSizes
     */
    public function setPageSizes(int ...$pageSizes)
    {
        $this->pageSizes = array_unique($pageSizes);
    }

    /**
     * @param string $title
     * @param string $action
     * @param string $cssClass
     * @return GroupAction
     */
    public function setGroupAction(string $title, string $action, string $cssClass = ''): GroupAction
    {
        return $this->groupActions[$action] = new GroupAction($this->gridActions, $title, $action, $cssClass);
    }

    /**
     * @param string $title
     * @param string $action
     * @param string $cssClass
     * @return SingleAction
     */
    public function setSingleAction(string $title, string $action, string $cssClass = ''): SingleAction
    {
        return $this->singleActions[$action] = new SingleAction($this->gridActions, $title, $action, $cssClass);
    }

    /**
     * @param string $title
     * @param string $action
     * @param string $cssClass
     * @return ConditionalSingleAction
     */
    public function setConditionalSingleAction(string $title, string $action, string $cssClass = ''): ConditionalSingleAction
    {
        return $this->singleActions[$action] = new ConditionalSingleAction($this->gridActions, $title, $cssClass);
    }

    public function show()
    {
        $this->processActions();

        if ($this->filter instanceof ModelFilter) {
            $this->filter->show();
        }

        $nav = $this->getNav();
        $query = $this->getQuery($nav->getLimit(), $nav->getCurrentPage());
        $nav->setRecordCount($query->getTotalCount());

        $collection = $query->getList();
        $data = $this->getFormattedData($collection);

        global $APPLICATION;
        $APPLICATION->IncludeComponent(
            'bitrix:main.ui.grid',
            '',
            [
                'GRID_ID' => $this->code,
                'COLUMNS' => $this->getColumns(),
                'ROWS' => $data,
                'SHOW_ROW_CHECKBOXES' => true,
                'NAV_OBJECT' => $nav,
                'AJAX_MODE' => 'Y',
                'AJAX_ID' => CAjax::getComponentID(
                    'bitrix:main.ui.grid',
                    '.default',
                    ''
                ),
                'PAGE_SIZES' => $this->getPageSizes(),
                'AJAX_OPTION_JUMP'          => 'N',
                'SHOW_CHECK_ALL_CHECKBOXES' => true,
                'SHOW_ROW_ACTIONS_MENU'     => true,
                'SHOW_GRID_SETTINGS_MENU'   => true,
                'SHOW_NAVIGATION_PANEL'     => true,
                'SHOW_PAGINATION'           => true,
                'SHOW_SELECTED_COUNTER'     => true,
                'SHOW_TOTAL_COUNTER'        => true,
                'SHOW_PAGESIZE'             => true,
                'SHOW_ACTION_PANEL'         => true,
                'ACTION_PANEL'              => [
                    'GROUPS' => [
                        'TYPE' => [
                            'ITEMS' => $this->getFormattedActions($this->groupActions ?? []),
                        ],
                    ],
                ],
                'ALLOW_COLUMNS_SORT'        => true,
                'ALLOW_COLUMNS_RESIZE'      => true,
                'ALLOW_HORIZONTAL_SCROLL'   => true,
                'ALLOW_SORT'                => true,
                'ALLOW_PIN_HEADER'          => true,
                'AJAX_OPTION_HISTORY'       => 'N',
            ]
        );
    }

    /**
     * @return array
     */
    private function getColumns(): array
    {
        $result = [];
        foreach ($this->columnList as $column) {
            $result[] = $column->toArray();
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getPageSizes(): array
    {
        $result = [];
        foreach ($this->pageSizes as $size) {
            $result[] = [
                'NAME' => $size,
                'VALUE' => $size,
            ];
        }

        return $result;
    }

    /**
     * @param int $limit
     * @param int $page
     * @return ModelQueryInterface
     */
    public function getQuery(int $limit, int $page): ModelQueryInterface
    {
        //$queryList = $this->request->getQueryList()->toArray();
        [
            'last_sort_by' => $sortField,
            'last_sort_order' => $sortOrder,
        ] = $this->gridOptions->getCurrentOptions();
        $this->modelService->setSortFields($this->sortList ?? []);
        return $this->modelService->query()
            ->loadFiler($this->filter->getFilter())
            ->loadSort([
                'field_sort' => $sortField,
                'order_sort' => $sortOrder,
            ])
            ->setLimit($limit)
            ->setPage($page);
    }

    /**
     * @return PageNavigation
     */
    private function getNav(): PageNavigation
    {
        [
            'page_size' => $pageSize,
        ] = $this->gridOptions->getCurrentOptions();
        $nav = new PageNavigation($this->code);
        $nav->allowAllRecords(false)
            ->setPageSize($pageSize)
            ->initFromUri();

        if ($nav->getPageSize() === 0) {
            $nav->setPageSize(20);
        }

        if (!empty($this->pageSizes)) {
            $nav->setPageSizes($this->getPageSizes());
        }

        return $nav;
    }

    /**
     * @param AbsOptimizedModel[]|ModelCollection $collection
     * @return array
     */
    private function getFormattedData(ModelCollection $collection): array
    {
        $result = [];
        foreach ($collection as $model) {
            $data = [];
            foreach ($this->columnList as $column) {
                $columnId = $column->id;
                $data[$columnId] = $column->getValue($model);
            }

            $result[] = [
                'id' => $model[$this->primaryKey],
                'data' => $data,
                'actions' => $this->getFormattedActions($this->singleActions ?? [], $model),
            ];
        }

        return $result;
    }

    private function processActions()
    {
        $gridId = $this->request->getPost('grid_id');
        if ($gridId !== $this->code) {
            return;
        }

        $action = $this->request->getPost('action');
        $type = $this->request->getPost('type');
        if ($type === 'group') {
            $groupAction = $this->groupActions[$action] ?? null;
            if ($groupAction instanceof GroupAction) {
                $ids = (array)($this->request->getPost('id') ?? []);
                $groupAction->exec($ids);
            }
        } else {
            $singleAction = $this->singleActions[$action];
            if ($singleAction instanceof SingleAction) {
                $id = (int)$this->request->getPost('id');
                $singleAction->exec($id);
            }
        }
    }

    /**
     * @param BaseAction[] $actions
     * @param AbsOptimizedModel|null $model
     * @return array
     */
    private function getFormattedActions(array $actions, AbsOptimizedModel $model = null): array
    {
        $result = [];
        foreach ($actions as $action) {
            if ($action instanceof GroupAction) {
                $result[] = $action->toArray();
            }
            elseif ($action instanceof ConditionalSingleAction) {
                if ($model !== null && $action->isActionAllowedForModel($model)) {
                    $result[] = $action->toArray((int)$model[$this->primaryKey]);
                }
            }
            elseif ($action instanceof SingleAction) {
                $result[] = $action->toArray((int)$model[$this->primaryKey]);
            }
        }

        return $result;
    }
}