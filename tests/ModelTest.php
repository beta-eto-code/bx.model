<?php

namespace Bx\Model\Tests;

use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\ModelInterface;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    /**
     * @var AbsOptimizedModel
     */
    private $model;
    /**
     * @var array
     */
    private $originalData;

    function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => null,
            'date' => \DateTime::createFromFormat('Y-m-d H:i:s', '2021-09-07 16:10:00'),
            'boolean' => true,
        ];
        $this->originalData = $data;
        $this->model = $this->initModel($data);
    }

    /**
     * @param array $data
     * @return ModelInterface
     */
    protected function initModel(array $data): ModelInterface
    {
        return new class($data) extends AbsOptimizedModel {

            protected function toArray(): array
            {
                return $this->data;
            }
        };
    }

    public function testOffsetExists()
    {
        $errMessage1 = 'Invalid result on call offsetExists method';
        $this->assertTrue($this->model->offsetExists('key1'), $errMessage1);

        $this->assertTrue(!$this->model->offsetExists('key3'), $errMessage1);
        $this->assertTrue(!$this->model->offsetExists('key4'), $errMessage1);

        $errMessage2 = 'Invalid result on access by array key';
        $this->assertTrue(isset($this->model['key1']), $errMessage2);
        $this->assertTrue(!isset($this->model['key3']), $errMessage2);
        $this->assertTrue(!isset($this->model['key4']), $errMessage2);
    }

    public function testGetIterator()
    {
        $this->assertTrue(method_exists($this->model, 'getIterator'));
        $this->assertTrue($this->model->getIterator() instanceof  \Iterator);

        $counter = 0;
        foreach ($this->model as $key => $value) {
            $counter++;
            $this->assertEquals($value, $this->originalData[$key]);
        }

        $this->assertCount($counter, $this->originalData);
    }

    public function testHasValueKey()
    {
        $this->assertTrue($this->model->hasValueKey('key1'));
        $this->assertTrue(!$this->model->hasValueKey('key3'));
        $this->assertTrue(!$this->model->hasValueKey('key4'));
    }

    public function testGetValueByKey()
    {
        $this->assertEquals($this->model->getValueByKey('key1'), $this->originalData['key1']);
        $this->assertEquals($this->model->getValueByKey('date'), $this->originalData['date']);
        $this->assertEquals($this->model->getValueByKey('boolean'), $this->originalData['boolean']);
    }

    public function testAssertValueByKey()
    {
        $this->assertTrue($this->model->assertValueByKey('key1', $this->originalData['key1']));
        $this->assertTrue($this->model->assertValueByKey('date', $this->originalData['date']));
        $this->assertTrue($this->model->assertValueByKey('boolean', $this->originalData['boolean']));
    }

    public function testOffsetGet()
    {
        $this->assertEquals($this->model->offsetGet('key1'), $this->originalData['key1']);
        $this->assertEquals($this->model->offsetGet('date'), $this->originalData['date']);
        $this->assertEquals($this->model->offsetGet('boolean'), $this->originalData['boolean']);

        $this->assertEquals($this->model['key1'], $this->originalData['key1']);
        $this->assertEquals($this->model['date'], $this->originalData['date']);
        $this->assertEquals($this->model['boolean'], $this->originalData['boolean']);
    }

    public function testJsonSerialize()
    {
        $assertValue = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => null,
            'date' => "2021-09-07T16:10:00+00:00",
            'boolean' => true,
        ];
        $this->assertEquals($this->model->jsonSerialize(), $assertValue);
    }

    public function testOffsetSet()
    {
        $newValue = 'new value';
        $this->assertNotEquals($this->model['key1'], $newValue);
        $this->assertNotEquals($this->model->getValueByKey('key1'), $newValue);
        $this->assertNotEquals($this->model->offsetGet('key1'), $newValue);
        $this->assertFalse($this->model->assertValueByKey('key1', $newValue));

        $this->model->offsetSet('key1', $newValue);
        $this->assertEquals($this->model['key1'], $newValue);
        $this->assertEquals($this->model->getValueByKey('key1'), $newValue);
        $this->assertEquals($this->model->offsetGet('key1'), $newValue);
        $this->assertTrue($this->model->assertValueByKey('key1', $newValue));
    }

    public function testGetApiModel()
    {
        $assertValue = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => null,
            'date' => "2021-09-07T16:10:00+00:00",
            'boolean' => true,
        ];
        $this->assertEquals($this->model->getApiModel(), $assertValue);
    }

    public function testOffsetUnset()
    {
        $this->assertTrue($this->model->offsetExists('key1'));
        $this->assertTrue(isset($this->model['key1']));

        $this->model->offsetUnset('key1');
        $this->assertFalse($this->model->offsetExists('key1'));
        $this->assertFalse(isset($this->model['key1']));
    }
}
