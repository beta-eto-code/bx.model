<?php

namespace Bx\Model\Tests;

use Bx\Model\AbsOptimizedModel;
use Bx\Model\Collection;
use Bx\Model\FetcherModel;
use Bx\Model\Interfaces\CollectionInterface;
use Bx\Model\Interfaces\ModelInterface;
use Bx\Model\ModelCollection;
use Bx\Model\Tests\Samples\EmptyModelService;
use PHPUnit\Framework\TestCase;

class FetcherModelTest extends TestCase
{
    /**
     * @var FetcherModel
     */
    private $fetcherModel;
    /**
     * @var EmptyModelService
     */
    private $linkedService;
    /**
     * @var Collection|CollectionInterface
     */
    private $linkedCollection;
    /**
     * @var Collection|CollectionInterface
     */

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->linkedCollection = $this->initCollection([
            [
                'id' => 21,
                'title' => 'model 21',
            ],
            [
                'id' => 22,
                'title' => 'model 22',
            ],
            [
                'id' => 23,
                'title' => 'model 23',
            ],
        ]);


        $this->linkedService = new EmptyModelService();
        $this->linkedService->resultList = $this->linkedCollection;

        $this->fetcherModel = new FetcherModel(
            $this->linkedService,
            'externalModel',
            'model_id',
            'id',
            false
        );
    }

    /**
     * @param array $data
     * @return ModelCollection
     */
    protected function initCollection(array $data): CollectionInterface
    {
        $collection = new ModelCollection([], AbsOptimizedModel::class);
        foreach ($data as $item) {
            $collection->append($this->initModel($item));
        }

        return $collection;
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

    public function testSetCompareCallback()
    {
        $list = [
            [
                'id' => 1,
                'model_id' => 23,
            ],
            [
                'id' => 2,
                'model_id' => 22,
            ],
            [
                'id' => 3,
                'model_id' => 21,
            ],
        ];

        $collection = $this->initCollection($list);
        $fetcher = FetcherModel::initAsSingleValue(
            $this->linkedService,
            'externalModel',
            'model_id',
            'id'
        );

        $fetcher->setCompareCallback(function (AbsOptimizedModel $model, AbsOptimizedModel $linkedModel) {
            return $linkedModel['id'] > 21 && $linkedModel['id'] === $model['model_id'];
        });

        $fetcher->fill($collection);
        $this->assertEquals(json_encode([
            [
                'id' => 1,
                'model_id' => 23,
                'externalModel' => [
                    'id' => 23,
                    'title' => 'model 23',
                ],
            ],
            [
                'id' => 2,
                'model_id' => 22,
                'externalModel' => [
                    'id' => 22,
                    'title' => 'model 22',
                ],
            ],
            [
                'id' => 3,
                'model_id' => 21,
            ],
        ]), json_encode($collection));
    }

    public function testInitAsSingleValue()
    {
        $assertValue = [
            [
                'id' => 1,
                'model_id' => 23,
                'externalModel' => [
                    'id' => 23,
                    'title' => 'model 23',
                ],
            ],
            [
                'id' => 2,
                'model_id' => 22,
                'externalModel' => [
                    'id' => 22,
                    'title' => 'model 22',
                ],
            ],
            [
                'id' => 3,
                'model_id' => 21,
                'externalModel' => [
                    'id' => 21,
                    'title' => 'model 21',
                ],
            ],
        ];

        $list = [
            [
                'id' => 1,
                'model_id' => 23,
            ],
            [
                'id' => 2,
                'model_id' => 22,
            ],
            [
                'id' => 3,
                'model_id' => 21,
            ],
        ];

        $collection = $this->initCollection($list);
        $fetcher = FetcherModel::initAsSingleValue(
            $this->linkedService,
            'externalModel',
            'model_id',
            'id'
        );

        $fetcher->fill($collection);
        $this->assertEquals(json_encode($assertValue), json_encode($collection));
    }

    public function testSetModifyCallback()
    {
        $list = [
            [
                'id' => 1,
                'model_id' => 23,
            ],
            [
                'id' => 2,
                'model_id' => 22,
            ],
            [
                'id' => 3,
                'model_id' => 21,
            ],
        ];

        $collection = $this->initCollection($list);
        $fetcher = FetcherModel::initAsSingleValue(
            $this->linkedService,
            'externalModel',
            'model_id',
            'id'
        );

        $fetcher->setModifyCallback(function (AbsOptimizedModel $linkedModel) {
            return [
                'modified_field' => "#{$linkedModel['id']} - {$linkedModel['title']}",
            ];
        });

        $fetcher->fill($collection);
        $this->assertEquals(json_encode([
            [
                'id' => 1,
                'model_id' => 23,
                'externalModel' => [
                    'modified_field' => '#23 - model 23',
                ],
            ],
            [
                'id' => 2,
                'model_id' => 22,
                'externalModel' => [
                    'modified_field' => '#22 - model 22',
                ],
            ],
            [
                'id' => 3,
                'model_id' => 21,
                'externalModel' => [
                    'modified_field' => '#21 - model 21',
                ],
            ],
        ]), json_encode($collection));
    }

    public function testInitAsMultipleValue()
    {
        $list = [
            [
                'id' => 1,
                'model_id' => [21, 23],
            ],
            [
                'id' => 2,
                'model_id' => [21, 22],
            ],
            [
                'id' => 3,
                'model_id' => [23],
            ],
        ];
        $collection = $this->initCollection($list);
        $fetcher = FetcherModel::initAsMultipleValue(
            $this->linkedService,
            'externalModel',
            'model_id',
            'id'
        );

        $fetcher->fill($collection);
        $this->assertEquals(json_encode([
            [
                'id' => 1,
                'model_id' => [21, 23],
                'externalModel' => [
                    [
                        'id' => 21,
                        'title' => 'model 21',
                    ],
                    [
                        'id' => 23,
                        'title' => 'model 23',
                    ],
                ],
            ],
            [
                'id' => 2,
                'model_id' => [21, 22],
                'externalModel' => [
                    [
                        'id' => 21,
                        'title' => 'model 21',
                    ],
                    [
                        'id' => 22,
                        'title' => 'model 22',
                    ],
                ],
            ],
            [
                'id' => 3,
                'model_id' => [23],
                'externalModel' => [
                    [
                        'id' => 23,
                        'title' => 'model 23',
                    ],
                ],
            ],
        ]), json_encode($collection));
    }

    public function testFill()
    {
        $assertValue = [
            [
                'id' => 1,
                'model_id' => 21,
                'externalModel' => [
                    'id' => 21,
                    'title' => 'model 21',
                ],
            ],
            [
                'id' => 2,
                'model_id' => 22,
                'externalModel' => [
                    'id' => 22,
                    'title' => 'model 22',
                ],
            ],
            [
                'id' => 3,
                'model_id' => 23,
                'externalModel' => [
                    'id' => 23,
                    'title' => 'model 23',
                ],
            ],
        ];

        $collection = $this->initCollection([
            [
                'id' => 1,
                'model_id' => 21,
            ],
            [
                'id' => 2,
                'model_id' => 22,
            ],
            [
                'id' => 3,
                'model_id' => 23,
            ],
        ]);

        $this->fetcherModel->fill($collection);

        $this->assertEquals(json_encode($assertValue), json_encode($collection));
    }
}
