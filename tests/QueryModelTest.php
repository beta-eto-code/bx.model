<?php

namespace Bx\Model\Tests;

use Bitrix\Main\ORM\Fields\Field;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\Models\PaginationInterface;
use Bx\Model\ModelCollection;
use Bx\Model\QueryModel;
use Bx\Model\Tests\Samples\EmptyModelService;

class QueryModelTest extends QueryTest
{
    /**
     * @var EmptyModelService
     */
    private $modelService;
    /**
     * @var ModelCollection
     */
    private $defaultListCollection;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->defaultListCollection = new ModelCollection(
            [
                $this->initModel(['id' => 1]),
                $this->initModel(['id' => 2]),
                $this->initModel(['id' => 3])
            ],
            AbsOptimizedModel::class
        );

        $this->modelService = new EmptyModelService();
        $this->query = new QueryModel($this->modelService);
    }

    private function initModel(array $data): AbsOptimizedModel
    {
        return new class($data) extends AbsOptimizedModel {

            protected function toArray(): array
            {
                return $this->data;
            }
        };
    }

    public function testGetList()
    {
        $this->modelService->resultList = $this->defaultListCollection;
        $this->assertEquals($this->defaultListCollection, $this->query->getList());
    }

    public function testSetRuntimeField()
    {
        $this->assertEquals([], $this->query->getRuntimeFields());

        $fieldName = 'some_field';
        $fieldExpression = new Field();
        $runtimeFields = [$fieldName => $fieldExpression];
        $this->query->setRuntimeField($fieldName, $fieldExpression);
        $this->assertEquals($runtimeFields, $this->query->getRuntimeFields());
    }

    public function testGetListPaginationData()
    {
        $defaultResult = [
            'items' => [],
            'pagination' => [
                'currentPage' => 1,
                'itemsCount' => 0,
                'itemsCountOnPage' => 0,
                'pagesCount' => 1,
                'itemsPerPage' => 0,
            ],
        ];
        $this->assertEquals($defaultResult, $this->query->getListPaginationData());

        $this->modelService->resultCount = $this->defaultListCollection->count();
        $this->modelService->resultList = $this->defaultListCollection;
        $result = [
            'items' => $this->defaultListCollection->jsonSerialize(),
            'pagination' => [
                'currentPage' => 1,
                'itemsCount' => $this->defaultListCollection->count(),
                'itemsCountOnPage' => $this->defaultListCollection->count(),
                'pagesCount' => 1,
                'itemsPerPage' => 0,
            ],
        ];
        $this->assertEquals($result, $this->query->getListPaginationData());
    }

    public function testLoadFiler()
    {
        $this->modelService::$allowedFilterFields = ['id', 'name'];

        $this->query->loadFiler(['from_id' => 3, 'like_name' => 'test', 'other_key' => 'test']);
        $this->assertEquals([
            '>=id' => 3,
            '%name' => 'test',
        ], $this->query->getFilter());

        $this->query->loadFiler(['to_id' => 3, 'name' => 'test1,test2,test3', 'other_key' => 'test']);
        $this->assertEquals([
            '<=id' => 3,
            '=name' => [
                'test1',
                'test2',
                'test3',
            ],
        ], $this->query->getFilter());

        $this->query->loadFiler(['id' => 3, 'strict_name' => 'test1,test2,test3', 'other_key' => 'test']);
        $this->assertEquals([
            '=id' => 3,
            '=name' => 'test1,test2,test3',
        ], $this->query->getFilter());
    }

    public function testGetPagination()
    {
        $defaultPaginationData = [
            'currentPage' => 1,
            'itemsCount' => 0,
            'itemsCountOnPage' => 0,
            'pagesCount' => 1,
            'itemsPerPage' => 0,
        ];
        $this->assertTrue($this->query->getPagination() instanceof PaginationInterface);
        $this->assertEquals($defaultPaginationData, $this->query->getPagination()->jsonSerialize());

        $paginationData = [
            'currentPage' => 1,
            'itemsCount' => $this->defaultListCollection->count(),
            'itemsCountOnPage' => $this->defaultListCollection->count(),
            'pagesCount' => 1,
            'itemsPerPage' => 0,
        ];
        $this->modelService->resultCount = $this->defaultListCollection->count();
        $this->modelService->resultList = $this->defaultListCollection;
        $this->assertEquals($paginationData, $this->query->getPagination()->jsonSerialize());
    }

    public function testGetTotalCount()
    {
        $this->assertEquals(0, $this->query->getTotalCount());

        $this->modelService->resultCount = $this->defaultListCollection->count();
        $this->assertEquals($this->modelService->resultCount, $this->query->getTotalCount());
    }

    public function testLoadSort()
    {
        $this->modelService::$allowedSortFields = ['id', 'name'];

        $this->query->loadSort([
            'field_sort' => 'id',
            'order_sort' => 'desc',
        ]);
        $this->assertEquals(['id' => 'desc'], $this->query->getSort());

        $this->query->loadSort([
            'field_sort' => 'id',
            'order_sort' => 'asc',
        ]);
        $this->assertEquals(['id' => 'asc'], $this->query->getSort());

        $this->query->loadSort([
            'field_sort' => 'name',
            'order_sort' => 'some_state',
        ]);
        $this->assertEquals(['name' => 'asc'], $this->query->getSort());

        $this->query->loadSort([
            'field_sort' => 'name',
        ]);
        $this->assertEquals(['name' => 'asc'], $this->query->getSort());

        $this->query->loadSort([
            'field_sort' => 'other_field',
            'order_sort' => 'asc',
        ]);
        $this->assertEquals([], $this->query->getSort());
    }

    public function testAddFilter()
    {
        $this->query->addFilter([
            '=code' => 'test_code'
        ]);
        $this->assertEquals([
            '=code' => 'test_code',
        ], $this->query->getFilter());

        $this->query->addFilter([
            '=id' => 2
        ], 'entity');
        $this->assertEquals([
            '=code' => 'test_code',
            '=entity.id' => 2,
        ], $this->query->getFilter());
    }

    public function testGetRuntimeFields()
    {
        $this->assertEquals([], $this->query->getRuntimeFields());

        $fieldName = 'some_field';
        $fieldExpression = new Field();
        $runtimeFields = [$fieldName => $fieldExpression];
        $this->query->setRuntimeField($fieldName, $fieldExpression);
        $this->assertEquals($runtimeFields, $this->query->getRuntimeFields());
    }

    public function testLoadPagination()
    {
        $this->query->loadPagination(['limit' => 5, 'page' => 2]);
        $this->assertEquals(5, $this->query->getLimit());
        $this->assertEquals(2, $this->query->getPage());
    }
}
