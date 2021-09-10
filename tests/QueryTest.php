<?php

namespace Bx\Model\Tests;

use Bx\Model\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;
    /**
     * @var int
     */
    private $defaultLimit;
    /**
     * @var string[]
     */
    private $defaultSort;
    /**
     * @var string[]
     */
    private $defaultFilter;
    /**
     * @var string[]
     */
    private $defaultGroup;
    /**
     * @var int
     */
    private $defaultPage;
    /**
     * @var string[]
     */
    private $defaultSelect;
    /**
     * @var string[]
     */
    private $defaultFetchList;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->defaultLimit = 30;
        $this->defaultSort = ['name' => 'asc'];
        $this->defaultFilter = ['=name' => 'some name'];
        $this->defaultGroup = ['name'];
        $this->defaultPage = 3;
        $this->defaultSelect = ['id', 'name'];
        $this->defaultFetchList = ['user', 'article'];

        $this->query = new Query();
    }

    public function testGetGroup()
    {
        $this->assertEquals([], $this->query->getGroup());
        $this->query->setGroup($this->defaultGroup);
        $this->assertEquals($this->defaultGroup, $this->query->getGroup());
    }

    public function testSetSort()
    {
        $this->query->setSort($this->defaultSort);
        $this->assertEquals($this->defaultSort, $this->query->getSort());
    }

    public function testSetTotalCount()
    {
        $count = 10;
        $this->query->setTotalCount($count);
        $this->assertEquals($count, $this->query->getTotalCount());
    }

    public function testSetLimit()
    {
        $limit = 20;
        $this->query->setLimit($limit);
        $this->assertEquals($limit, $this->query->getLimit());
    }

    public function testHasFilter()
    {
        $this->assertFalse($this->query->hasFilter());
        $this->query->setFilter(['=name' => 'some name']);
        $this->assertTrue($this->query->hasFilter());
        $this->query->setFilter([]);
        $this->assertFalse($this->query->hasFilter());
    }

    public function testHasLimit()
    {
        $this->assertFalse($this->query->hasLimit());
        $this->query->setLimit(20);
        $this->assertTrue($this->query->hasLimit());
        $this->query->setLimit(0);
        $this->assertFalse($this->query->hasLimit());
    }

    public function testGetLimit()
    {
        $this->assertEquals(0, $this->query->getLimit());
        $this->query->setLimit($this->defaultLimit);
        $this->assertEquals($this->defaultLimit, $this->query->getLimit());
    }

    public function testSetPage()
    {
        $this->assertEquals(1, $this->query->getPage());

        $page = 11;
        $this->query->setPage($page);
        $this->assertEquals($page, $this->query->getPage());
    }

    public function testSetGroup()
    {
        $group = ['some_key'];
        $this->query->setGroup($group);
        $this->assertEquals($group, $this->query->getGroup());
    }

    public function testGetSort()
    {
        $this->assertEquals([], $this->query->getSort());
        $this->query->setSort($this->defaultSort);
        $this->assertEquals($this->defaultSort, $this->query->getSort());
    }

    public function testGetOffset()
    {
        $this->assertEquals(0, $this->query->getOffset());

        $limit = 5;
        $page = 33;

        $this->query->setLimit(5);
        $this->query->setPage($page);
        $this->assertEquals($limit * ($page - 1), $this->query->getOffset());

        $this->query->setPage(0);
        $this->assertEquals(0, $this->query->getOffset());

        $this->query->setPage(-1);
        $this->assertEquals(0, $this->query->getOffset());

        $this->query->setPage($page);
        $this->query->setLimit(0);
        $this->assertEquals(0, $this->query->getOffset());

        $this->query->setPage($page);
        $this->query->setLimit(-5);
        $this->assertEquals(0, $this->query->getOffset());
    }

    public function testSetFilter()
    {
        $filter = ['id' => 1];
        $this->query->setFilter($filter);
        $this->assertEquals($filter, $this->query->getFilter());
    }

    public function testHasSelect()
    {
        $this->assertFalse($this->query->hasSelect());

        $select = ['id', 'sort'];
        $this->query->setSelect($select);
        $this->assertTrue($this->query->hasSelect());

        $this->query->setSelect([]);
        $this->assertFalse($this->query->hasSelect());
    }

    public function testGetSelect()
    {
        $this->assertEquals([], $this->query->getSelect());
        $this->query->setSelect($this->defaultSelect);
        $this->assertEquals($this->defaultSelect, $this->query->getSelect());
    }

    public function testSetFetchList()
    {
        $fetchList = ['user', 'article'];
        $this->query->setFetchList($fetchList);
        $this->assertEquals($fetchList, $this->query->getFetchList());
    }

    public function testHasSort()
    {
        $this->assertFalse($this->query->hasSort());
        $this->query->setSort(['test' => 'desc']);
        $this->assertTrue($this->query->hasSort());

        $this->query->setSort([]);
        $this->assertFalse($this->query->hasSort());
    }

    public function testHasGroup()
    {
        $this->assertFalse($this->query->hasGroup());
        $this->query->setGroup(['test']);
        $this->assertTrue($this->query->hasGroup());

        $this->query->setGroup([]);
        $this->assertFalse($this->query->hasGroup());
    }

    public function testGetFetchList()
    {
        $this->assertEquals([], $this->query->getFetchList());
        $this->query->setFetchList($this->defaultFetchList);
        $this->assertEquals($this->defaultFetchList, $this->query->getFetchList());
    }

    public function testHasFetchList()
    {
        $this->assertFalse($this->query->hasFetchList());

        $this->query->setFetchList($this->defaultFetchList);
        $this->assertTrue($this->query->hasFetchList());

        $this->query->setFetchList(null);
        $this->assertFalse($this->query->hasFetchList());
    }

    public function testGetPage()
    {
        $this->assertEquals(1, $this->query->getPage());

        $this->query->setPage($this->defaultPage);
        $this->assertEquals($this->defaultPage, $this->query->getPage());

        $this->query->setPage(0);
        $this->assertEquals(1, $this->query->getPage());

        $this->query->setPage(-1);
        $this->assertEquals(1, $this->query->getPage());
    }

    public function testGetFilter()
    {
        $this->assertEquals([], $this->query->getFilter());

        $this->query->setFilter($this->defaultFilter);
        $this->assertEquals($this->defaultFilter, $this->query->getFilter());
    }

    public function testSetSelect()
    {
        $this->assertEquals([], $this->query->getSelect());

        $this->query->setSelect($this->defaultSelect);
        $this->assertEquals($this->defaultSelect, $this->query->getSelect());
    }

    public function testGetTotalCount()
    {
        $this->assertEquals(0, $this->query->getTotalCount());

        $this->query->setTotalCount(10);
        $this->assertEquals(10, $this->query->getTotalCount());
    }
}
