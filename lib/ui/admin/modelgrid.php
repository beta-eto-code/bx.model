<?php


namespace Bx\Model\UI\Admin;


use Bitrix\Main\Application;
use Bitrix\Main\UI\Filter\Options;
use Bitrix\Main\UI\PageNavigation;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\ModelInterface;
use Bx\Model\Interfaces\ModelQueryInterface;
use Bx\Model\Interfaces\ModelServiceInterface;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\ModelCollection;
use Bx\Model\Services\ProxyService;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Request;
use Bx\Model\UI\AdminButtonAction;
use Bx\Model\UI\AdminButtonLink;
use Bx\Model\UI\BaseAdminButton;
use Bx\Model\UI\BaseGridColumn;
use Bx\Model\UI\Fields\BaseFilterField;
use Bx\Model\UI\Fields\DateFilterField;
use Bx\Model\UI\Fields\ListFilterField;
use Bx\Model\UI\Fields\NumberFilterField;
use Bx\Model\UI\Fields\SearchFilterField;
use Bx\Model\UI\Fields\StringFilterField;
use Bx\Model\UI\GridActions;
use Bx\Model\UI\GridCalculateColumn;
use Bx\Model\UI\GridColumn;
use Bx\Model\UI\GroupAction;
use Bx\Model\UI\SingleAction;
use CAdminUiList;
use CAdminUiListRow;
use CAdminUiSorting;
use Closure;

/**
 * Пример использования:
 *
 * ```php
 *  $grid = new ModelGrid(new SomeModelService(), 'some_id');
 *  $grid->addColumn('id', 'ID');
 *  $grid->addColumn('name', 'Название');
 *  $grid->addColumn('sort', 'Индекс сортировки');
 *  $grid->addCalculateColumn('custom_field', function(SomeModel $model) {
        return $model->ref->getField();
 *  }, ['custom_field' => 'ref.field']);
 *
 *  $grid->addNumericFilterField('sort', 'Индекс сортировки');
 *  $grid->addStringFilterField('name', 'Название');
 *  $grid->addListFilterField('field', 'Поле с выбором значений', [1 => 'one', 2 => 'two', 3 => 'three']);
 *
 *  $grid->show();
 * ```
 * Class ModelGrid
 * @package Bx\Model\UI\Admin
 */
class ModelGrid
{
    /**
     * @var HttpRequest|Request
     */
    private $request;
    /**
     * @var string
     */
    private $code;
    /**
     * @var ModelServiceInterface|ProxyService
     */
    private $modelService;
    /**
     * @var CAdminUiList
     */
    private $grid;
    /**
     * @var CAdminUiSorting
     */
    private $sort;
    /**
     * @var array
     */
    private $sortList;
    /**
     * @var BaseGridColumn[]
     */
    private $columnList;
    /**
     * @var PageNavigation
     */
    private $nav;
    /**
     * @var string
     */
    private $primaryKey;
    /**
     * @var BaseFilterField[]
     */
    private $filterFields;
    /**
     * @var ModelQueryInterface
     */
    private $query;
    /**
     * @var AbsOptimizedModel[]|ModelCollection
     */
    private $collection;
    /**
     * @var GroupAction[]
     */
    private $groupActions;
    /**
     * @var GridActions
     */
    private $actionHelper;
    /**
     * @var SingleAction[]
     */
    private $singleActions;
    /**
     * @var BaseAdminButton[]
     */
    private $menu;
    /**
     * @var \Bitrix\Main\Grid\Options
     */
    private $options;
    /**
     * @var array
     */
    private $defaultFilter;
    /**
     * @var UserContextInterface
     */
    private $userContext;
    /**
     * @var callable
     */
    private $defaultRowLinkFunction;
    /**
     * @var string
     */
    private $defaultRowLinkTitle;

    public function __construct(ModelServiceInterface $modelService, string $code, string $primaryKey = 'ID')
    {
        $this->primaryKey = $primaryKey;
        $this->request = Application::getInstance()->getContext()->getRequest();
        $this->code = $code;
        $this->modelService = $modelService instanceof ProxyService ? $modelService : new ProxyService($modelService);
        $this->nav = new PageNavigation('nav_'.$code);
        $this->options = new \Bitrix\Main\Grid\Options($code);
        $this->options->save();

        $this->nav->setPageSizes([5, 10, 20, 50, 100]);
        $pageSize = (int)($this->options->getCurrentOptions()['page_size'] ?? 0);
        if ($pageSize > 0) {
            $this->nav->setPageSize($pageSize);
        }
        $this->nav->initFromUri();

        $this->actionHelper = new GridActions($code);
        $this->sort = new CAdminUiSorting($code);
        $this->grid = new CAdminUiList($code, $this->sort);
        //$this->grid->AddGroupActionTable();
        //$this->grid->setNavigation($this->nav, 'Навигация');
    }

    /**
     * Добавляем столбец
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
     * @param array $filter
     */
    public function setFilter(array $filter)
    {
        $this->defaultFilter = $filter;
    }

    /**
     * @param UserContextInterface $userContext
     */
    public function setUserContext(UserContextInterface $userContext)
    {
        $this->userContext = $userContext;
    }

    /**
     * Добавляем вычисляемый столбец
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
     * Добавляем поле для фильтрации
     * @param BaseFilterField $field
     */
    public function addFilterField(BaseFilterField $field)
    {
        $id = $field->getId();
        $this->filterFields[$id] = $field;
    }

    /**
     * Добавляем целочисленное поле для фильтрации
     * @param string $id
     * @param string $title
     * @return NumberFilterField
     */
    public function addNumericFilterField(string $id, string $title): NumberFilterField
    {
        $field = new NumberFilterField($id, $title);
        $this->addFilterField($field);

        return $field;
    }

    /**
     * Добавляем стоковое поля для фильтрации
     * @param string $id
     * @param string $title
     * @return StringFilterField
     */
    public function addStringFilterField(string $id, string $title): StringFilterField
    {
        $field = new StringFilterField($id, $title);
        $this->addFilterField($field);

        return $field;
    }

    /**
     * Добавляем поле с выбором дат для фильтрации
     * @param string $id
     * @param string $title
     * @return DateFilterField
     */
    public function addDateFilterField(string $id, string $title): DateFilterField
    {
        $field = new DateFilterField($id, $title);
        $this->addFilterField($field);

        return $field;
    }

    /**
     * Добавляем поле с выбором значений для фильтрации
     * @param string $id
     * @param string $title
     * @param array $options
     * @return ListFilterField
     */
    public function addListFilterField(string $id, string $title, array $options): ListFilterField
    {
        $field = new ListFilterField($id, $title, $options);
        $this->addFilterField($field);

        return $field;
    }

    /**
     * Добавляем строкое поле по которому будет проходить поиск по-умолчанию при фильтрации
     * @param string $id
     * @param string $title
     * @return SearchFilterField
     */
    public function addSearchFilterField(string $id, string $title): SearchFilterField
    {
        $field = new SearchFilterField($id, $title);
        $this->addFilterField($field);

        return $field;
    }

    /**
     * Формируем массив полей фильтрации для отображения в компоненте
     * @return array
     */
    private function prepareFilterFields(): array
    {
        $result = [];
        foreach ($this->filterFields as $field) {
            $result[] = $field->toArray();
        }

        return $result;
    }

    /**
     * Формируем фильтр для передачи в сервис
     * @return array
     */
    private function getFilterData(): array
    {
        $options = new Options($this->code);
        $result = [];
        foreach ($this->filterFields as $field) {
            $filterField = $field->getFilterField($options);
            if (!empty($filterField)) {
                $result = array_merge($result, $filterField);
            }
        }

        return $result;
    }

    /**
     * Указываем лимиты выводимых элементов на странице
     * @param int ...$pageSizes
     */
    public function setPageSizes(int ...$pageSizes)
    {
        $this->nav->setPageSizes(array_unique($pageSizes));
    }

    /**
     * @param string $title
     * @param string $action
     * @param string $class
     * @return GroupAction
     */
    public function setGroupAction(string $title, string $action, string $class = ''): GroupAction
    {
        return $this->groupActions[$action] = new GroupAction($this->actionHelper, $title, $action, $class);
    }

    /**
     * @param string $title
     * @param string $action
     * @param string $class
     * @return SingleAction
     */
    public function setSingleAction(string $title, string $action, string $class = ''): SingleAction
    {
        return $this->singleActions[$action] = new SingleAction($this->actionHelper, $title, $action, $class);
    }

    /**
     * @return array
     */
    private function getSort(): array
    {
        $sorting = $this->options->getSorting()['sort'] ?? [];
        $fieldSort = key($sorting) ?? null;
        $orderSort = current($sorting) ?? 'asc';
        if (empty($fieldSort)) {
            return [];
        }

        return [
            'field_sort' => $fieldSort,
            'order_sort' => $orderSort,
        ];
    }

    /**
     * @return ModelQueryInterface
     */
    private function getQuery(): ModelQueryInterface
    {
        if ($this->query instanceof ModelQueryInterface) {
            return $this->query;
        }

        $this->query = $this->modelService->query($this->userContext ?? null)
            ->loadSort($this->getSort())
            ->loadFiler($this->getFilterData())
            ->setLimit($this->nav->getPageSize())
            ->setPage($this->nav->getCurrentPage());

        if (!empty($this->defaultFilter)) {
            $this->query->addFilter($this->defaultFilter);
        }

        return $this->query;
    }

    /**
     * @param string $title
     * @param string $link
     * @param string $icon
     * @return AdminButtonLink
     */
    public function addAdminButtonLink(string $title, string $link, string $icon = ''): AdminButtonLink
    {
        return $this->menu[] = new AdminButtonLink($title, $link, $icon);
    }

    /**
     * @param string $title
     * @param string $action
     * @param string $icon
     * @return AdminButtonAction
     */
    public function addAdminButtonAction(string $title, string $action, string $icon = ''): AdminButtonAction
    {
        return $this->menu[] = new AdminButtonAction($this->actionHelper, $title, $action, $icon);
    }


    /**
     * @param string $linkTemplate
     * @param string|null $linkTitle
     */
    public function setDefaultRowLinkTemplate(string $linkTemplate, ?string $linkTitle = null)
    {
        $this->setDefaultRowLinkByCallback(
            function (ModelInterface $model) use ($linkTemplate) {
                $link = $linkTemplate;
                preg_match_all('/#(.+?)#/ui', $link, $matches);
                $replaces = [];
                if($matches && $matches[1]) {
                    foreach ($matches[1] as $replaceKey) {
                        if($model->hasValueKey($replaceKey)) {
                            $replaces['#'.$replaceKey.'#'] = (string)$model->getValueByKey($replaceKey);
                        }
                    }
                }
                if($replaces) {
                    $link = str_replace(array_keys($replaces), array_values($replaces), $link);
                }
                return $link;
            },
            $linkTitle
        );
    }

    /**
     * @param callable $fnCalcLink
     * @param string|null $linkTitle
     */
    public function setDefaultRowLinkByCallback(callable $fnCalcLink, ?string $linkTitle = null)
    {
        $this->defaultRowLinkFunction = $fnCalcLink;
        $this->defaultRowLinkTitle = $linkTitle;
    }

    /**
     * @return AbsOptimizedModel[]|ModelCollection
     */
    public function getList(): ModelCollection
    {
        if ($this->collection instanceof ModelCollection) {
            return $this->collection;
        }

        return $this->collection = $this->getQuery()->getList();
    }

    /**
     * Заполняем заголовки столбцов таблицы
     */
    private function fillHeaders()
    {
        $headers = [];
        foreach ($this->columnList as $column) {
            $headers[] = [
                'id' => $column->id,
                'content' => $column->title,
                'sort' => $column->id,
                'default' => $column->isDefault,
            ];
        }
        $this->grid->AddHeaders($headers);
    }

    private function preparePoxyModelService()
    {
        $this->modelService->setSortFields($this->sortList);

        $filterData = [];
        foreach ($this->filterFields as $field) {
            $filterData = array_merge($filterData, (array)$field->getFilterData());
        }
        $this->modelService->setFilterFields($filterData);
    }

    /**
     * Заолняем доступные действия для строки таблицы
     * @param CAdminUiListRow $row
     * @param AbsOptimizedModel $model
     */
    private function fillSingleActions(CAdminUiListRow $row, AbsOptimizedModel $model)
    {
        $separator = ['SEPARATOR' => true];
        $actions = [];
        foreach ($this->singleActions as $action) {
            $actions[] = $separator;
            $actions[] = $action->toArray((int)$model[$this->primaryKey]);
        }
        $row->AddActions($actions);
    }

    /**
     * Заполняем таблицу данными
     */
    private function fillGrid()
    {
        foreach ($this->getList() as $model) {
            $rowData = [];
            $rowViews = [];
            foreach ($this->columnList as $column) {
                $value = $column->getValue($model);
                $rowData[$column->id] = $value;
                if ($column instanceof GridCalculateColumn) {
                    $rowViews[$column->id] = $value;
                }
            }

            if(!is_null($this->defaultRowLinkFunction) && is_callable($this->defaultRowLinkFunction)) {
                $link = call_user_func($this->defaultRowLinkFunction, $model);
            }
            else {
                $link = false;
            }

            $title = $this->defaultRowLinkTitle ?: ($link ? 'Перейти' : false);

            $row = $this->grid->AddRow((int)$model[$this->primaryKey], $rowData, $link, $title);
            foreach ($rowViews as $id => $view) {
                $row->AddViewField($id, $view);
            }
            $this->fillSingleActions($row, $model);
        }
    }

    /**
     * Добавляек кнопки
     */
    private function fillAdminButtons()
    {
        $result = [];
        foreach ($this->menu as $item) {
            $result[] = $item->toArray();
        }

        $this->grid->AddAdminContextMenu($result);
    }

    /**
     * @return array
     */
    private function getFormattedGroupActions(): array
    {
        $result = [];
        foreach ($this->groupActions as $action) {
            if ($action instanceof GroupAction) {
                $result[] = $action->toArray();
            }
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
                try {
                    $groupAction->exec($ids);
                }
                catch (\Throwable $e) {
                    $this->grid->AddGroupError($e->getMessage());
                }
            }
        } else {
            $singleAction = $this->singleActions[$action];
            if ($singleAction instanceof SingleAction) {
                $id = (int)$this->request->getPost('id');
                try {
                    $singleAction->exec($id);
                }
                catch (\Throwable $e) {
                    $this->grid->AddUpdateError($e->getMessage(), $id);
                }
            }
        }
    }

    /**
     * Выводим таблицу с данными и фильтром (если указан)
     */
    public function show()
    {
        $this->processActions();
        $this->preparePoxyModelService();
        $this->fillHeaders();
        $this->fillGrid();
        $this->fillAdminButtons();

        $pagination = $this->getQuery()->getPagination();
        $this->nav->setRecordCount($pagination->getTotalCountElements());
        $this->grid->setNavigation($this->nav, 'Навигация');

        $filterFields = $this->prepareFilterFields();
        if (!empty($filterFields)) {
            $this->grid->DisplayFilter($filterFields);
        }

        $listParams = [];
        $groupActions = $this->getFormattedGroupActions();
        if($groupActions) {
            $listParams['ACTION_PANEL'] = [
                'GROUPS' => [
                    'TYPE' =>  [
                        'ITEMS' => $groupActions
                    ],
                ],
            ];
        }
        else {
            $listParams['ACTION_PANEL'] = [];
        }

        $this->grid->DisplayList($listParams);
    }
}
