<?php


namespace Bx\Model\UI;


use Bitrix\Main\Application;
use Bitrix\Main\UI\Filter\Options;
use Bx\Model\Interfaces\ModelServiceInterface;
use Bx\Model\Services\ProxyService;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Request;
use Bx\Model\UI\Fields\BaseFilterField;
use Bx\Model\UI\Fields\DateFilterField;
use Bx\Model\UI\Fields\ListFilterField;
use Bx\Model\UI\Fields\NumberFilterField;
use Bx\Model\UI\Fields\StringFilterField;

class ModelFilter
{
    /**
     * @var string
     */
    private $code;
    /**
     * @var ModelServiceInterface|ProxyService
     */
    private $modelService;
    /**
     * @var HttpRequest|Request
     */
    private $request;
    /**
     * @var BaseFilterField[]
     */
    private $fields;
    /**
     * @var Options
     */
    private $filterOptions;

    public function __construct(ModelServiceInterface $modelService, string $code)
    {
        $this->request = Application::getInstance()->getContext()->getRequest();
        $this->code = $code;
        $this->filterOptions = new Options($code);
        $this->modelService = $modelService instanceof ProxyService ? $modelService : new ProxyService($modelService);
    }

    /**
     * @param string $id
     * @param string $title
     * @param array $options
     * @param bool $isMultiple
     * @return ListFilterField
     */
    public function addListField(string $id, string $title, array $options, bool $isMultiple = false): ListFilterField
    {
        $this->fields[$id] = new ListFilterField($id, $title, $options);
        if ($isMultiple) {
            $this->fields[$id]->markAsMultiple();
        }

        return $this->fields[$id];
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        $result = [];
        foreach ($this->fields as $field) {
            $filterField = $field->getFilterField($this->filterOptions);
            if (!empty($filterField)) {
                $result = array_merge($result, $filterField);
            }
        }

        return $result;
    }

    /**
     * @param string $id
     * @param string $title
     * @return NumberFilterField
     */
    public function addNumberField(string $id, string $title): NumberFilterField
    {
        return $this->fields[$id] = new NumberFilterField($id, $title);
    }

    /**
     * @param string $id
     * @param string $title
     * @return DateFilterField
     */
    public function addDateField(string $id, string $title): DateFilterField
    {
        return $this->fields[$id] = new DateFilterField($id, $title);
    }

    /**
     * @param string $id
     * @param string $title
     * @return StringFilterField
     */
    public function addStringField(string $id, string $title): StringFilterField
    {
        return $this->fields[$id] = new StringFilterField($id, $title);
    }

    /**
     * @return array
     */
    private function getFormattedFilter(): array
    {
        $result = [];
        foreach ($this->fields ?? [] as $field) {
            if ($field instanceof BaseFilterField) {
                $result[] = $field->toArray();
            }
        }

        return $result;
    }

    public function show()
    {
        $filterData = [];
        foreach ($this->fields as $field) {
            $filterData = array_merge($filterData, (array)$field->getFilterData());
        }
        $this->modelService->setFilterFields($filterData);

        global $APPLICATION;
        $APPLICATION->IncludeComponent('bitrix:main.ui.filter', '', [
            'FILTER_ID' => $this->code,
            'GRID_ID' => $this->code,
            'FILTER' => $this->getFormattedFilter(),
            'ENABLE_LIVE_SEARCH' => true,
            'ENABLE_LABEL' => true
        ]);
    }
}