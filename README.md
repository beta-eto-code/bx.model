# Bitrix модели

  - [Установка](#установка)
  - [Модели](#модели)
    - [Пример описания модели](#пример-описания-модели)
    - [Пример работы с моделью](#пример-работы-с-моделью)
    - [Агрегационная модель](#агрегационная-модель)
      - [Пример описания агрегационной модели](#пример-описания-агрегационной-модели)
      - [Пример работы с агрегационной моделью](#пример-работы-с-агрегационной-моделью)
  - [Коллекции](#коллекции)
    - [Пример работы с коллекцией](#пример-работы-с-коллекцией)
    - [Коллекция моделей](#коллекция-моделей)
      - [Пример использования](#пример-использования)
    - [MappedCollection & MappedCollectionCallback](#mappedcollection--mappedcollectioncallback)
      - [Пример использования](#пример-использования-1)
  - [Сервисы](#сервисы)
    - [Пример описания сервиса](#пример-описания-сервиса)
    - [Пример использования сервиса](#пример-использования-сервиса)
    - [Query](#query)
      - [Пример использования](#пример-использования-2)
    - [Pagination](#pagination)
      - [Пример использования](#пример-использования-3)
    - [Fetcher](#fetcher)
    - [Пример описания сервиса](#пример-описания-сервиса-1)
      - [Пример использования](#пример-использования-4)
    - [Админ. интерфейсы](#админ-интерфейсы)
      - [Пример использования](#пример-использования-5)
  - [Базовые сервисы](#базовые-сервисы)
    - [Сервис для работы с пользователями](#сервис-для-работы-с-пользователями)
      - [Пример использования](#пример-использования-6)
    - [Сервис для работы с файлами](#сервис-для-работы-с-файлами)
      - [Пример использования](#пример-использования-7)

## Установка
```
composer require beta/bx.model
```

## Модели

Модели отражают сущности используемые в проекте: элементы инфоблоков, 
элементы hl блоков или записи в произвольной таблице. Представляют из
себя формализованные данные и предоставляют интерфейс для более удобного оперирования данными.

Каждая модель должна имплементировать интерфейс ModelInterface, в свою очередь данный интерфейс наследует интерфейсы ArrayAccess и IteratorAggregate, 
то есть с моделью можно работать как с ассоциативным массивом. Так же наследуется интерфейс CollectionItemInterface требующий реализации методов:

* assertValueByKey(string $key, $value): bool - проверяет соотвествие заначения по ключу перкданному значению
* hasValueKey(string $key): bool - проверяет наличие значений по ключу
* getValueByKey(string $key) - возвращает значение по ключу

В данном модуле присуствует не полная реализация интрефейса модели - AbsOptimizedModel, предполагается использовать как основу для создания моделей.

### Пример описания модели

```php
use Bx\Model\AbsOptimizedModel;

class CatalogProduct extends AbsOptimizedModel
{
    protected function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }

    public function getId(): int
    {
        return (int)$this['ID'];
    }

    public function setId(int $id)
    {
        $this['ID'] = $id;
    }

    public function getName(): string
    {
        return (string)$this['NAME'];
    }

    public function setName(string $name)
    {
        $this['NAME'] = $name;
    }
}
```

### Пример работы с моделью

```php
$modelData = [
    'ID' => 11,
    'NAME' => 'Some product name',
];

$product = new CatalogProduct($modelData);
$product->getId();                      // 11
$product->getName();                    // 'Some product name'
$product->setName('New product name');

$product['ID'];                         // 11
$product['NAME'];                       // 'New product name'
$procuct['NAME'] = 'One more product name';

$product->hasValueKey('ID');            // true
$product->getValueByKey('ID');          // 11
$product->assertValueByKey('ID', 11);   // true
$product->assertValueByKey('ID', 12);   // false

/**
 * Результат:
 * ID - 11
 * NAME - New product name
 */
foreach($product as $key => $value) {
    echo "{$key} - {$value}\n";
}

$product->getApiModel();                // ['id' => 1, 'name' => 'One more product name']
json_encode($product);                  // '{"id": 1, "name": "One more product name"}'
```

### Агрегационная модель

В некоторых случаях требуется предоставить формализованные данные на основе некоторой совокупности данных. Например вывести общую информацию по ценам конкретного товара в рамках одной модели: максимальная/минимальная цена, среднее значение и т.д. Для подобных задач описан интерфейс AggregateModelInterface. Есть так же не полная реализация данного интерфейса - BaseAggregateModel. 

#### Пример описания агрегационной модели

```php
use Bx\Model\BaseAggregateModel;

class AggregatePrice extends BaseAggregateModel
{
    protected function toArray(): array
    {
        return [
            'min' => $this->getMin(),
            'max' => $this->getMax(),
            'actual' => $this->getActual(),
            'description' => $this->getDescription(),
        ];
    }

    public function getMin(): ?Price
    {
        $min = null;
        foreach($this->getCollection() as $price) {
            if ($min === null) {
                $min = $price;
                continue;
            }

            if ($min->getValue() > $price->getValue()) {
                $min = $price;
            }
        }

        return $min;
    }

    public function getMax(): ?Price
    {
        $max = null;
        foreach($this->getCollection() as $price) {
            if ($max === null) {
                $max = $price;
                continue;
            }

            if ($max->getValue() < $price->getValue()) {
                $max = $price;
            }
        }

        return $max;
    }

    public function getActual(): ?Price
    {
        foreach($this->getCollection() as $price) {
            if (/** некоторая логика **/) {
                return $price;   
            }
        }
        return null;
    }

    public function getDescription(): string
    {
        return (string)$this['description'];
    }

    public function setDescription(string $description)
    {
        $this['description'] = $description;
    }
}
```

#### Пример работы с агрегационной моделью

```php
use Bx\Model\ModelCollection;

$price1 = [
    'value' => 1000,
    'currency' => 'RUB',
];
$price2 = [
    'value' => 2000,
    'currency' => 'RUB',
];
$price3 = [
    'value' => 5000,
    'currency' => 'RUB',
];

$priceCollection = new ModelCollection([
    $price1,
    $price2,
    $price3,
], Price::class);

$aggregatePrice = new AggregatePrice($priceCollection, [
    'description' => 'some description',
]);

$aggregatePrice->getApiModel();     // ['min' => ['value' => 1000, 'currency' => 'RUB'], 'max' => ['value' => 5000, 'currency' => 'RUB'], 'actual' => null, 'description' => 'some description']
```

## Коллекции

Для работы с совокупностью однотипных данных создана еще одна сущность - коллекция. Колекции предоставляют удобный интерфейс для работы с подобными наборами данных, который в себя включает: фильтрацию, поиск, добавление/удаление элементов из коллекции, выборка значений по ключу...

Для реализации колеекции необходимо имплементировать интерфейс CollectionInterface или ReadableCollectionInterface если преполагается что набор элементов не изменяется. Коллекции в свою очередь могут включать в себя любые объекты имплементирующие интерфейс CollectionItemInterface. В данном модуле уже есть полноценая реализация коллеции - Collection.

### Пример работы с коллекцией

```php
use Bx\Model\Collection;

$priceItem1 = new Price([
    'value' => 1000,
    'currency' => 'RUB',
    'group' = 1,
]);
$priceItem2 = new Price([
    'value' => 2000,
    'currency' => 'RUB',
    'group' = 1,
]);
$priceItem3 = new Price([
    'value' => 4000,
    'currency' => 'RUB',
    'group' = 2,
]);

$collection = new Collection(
    $priceItem1,
    $priceItem2,
    $priceItem3
);

$collection->findByKey('value', 2000);          // $priceItem2
$collection->find(function(Price $price) {      // $priceItem1
    return $price->getValueByKey('value') === 1000 && 
        $price->getValueByKey('currency') === 'RUB'
});

$collection->filterByKey('group', 1);           // вернет новую коллекию состоящую из $priceItem1 и $priceItem2
$collection->filter(function(Price $price) {    // вернет новую коллекию состоящую из $priceItem2 и $priceItem3
    return $price->getValueByKey('value') > 1000 && 
        $price->getValueByKey('currency') === 'RUB'
});

$collection->column('value');                   // [1000, 2000, 4000]
$collection->unique('currency');                // ['RUB']
$collection->remove($priceItem2);               // удаляем элемент $priceItem2 из коллекции
$collection->append(new Price([                 // добавляем новый элемент в коллекцию
    'value' => 7000,
    'currency' => 'RUB',
    'group' => 2,
]));

$collection->first();                           // $priceItem1
$collection->count();                           // 3
count($collection);                             // 3

json_encode($collection);                       // JSON представление коллекции
$collection->jsonSerialize();                   // вернет ассоциативный массив
```

### Коллекция моделей

В модуле есть отдельная реализация коллекции для моделей ModelCollection, данные коллекции могут содержать любые объекты имплементирующие интерфейс ModelInterface.

#### Пример использования

```php
use Bx\Model\ModelCollection;

$productData1 =[
    'ID' => 11,
    'NAME' => 'Product name 11',
];
$productData2 = [
    'ID' => 21,
    'NAME' => 'Product name 21',
];
$product3 = new CatalogProduct([
    'ID' => 31,
    'NAME' => 'Product name 31',
]);

$procutCollection = new ModelCollection([
    $productData1,
    $productData2,
    $product3
], CatalogProduct::class);

$productCollection->addModel(new CatalogProduct([
    'ID' => 41,
    'NAME' => 'Product name 41',
]));

$productCollection->add([
    'ID' => 51,
    'NAME' => 'Product name 51',
]);
```

### MappedCollection & MappedCollectionCallback

Для удобства созданы производные коллекции в которых можно указывать произвольный ключ доступа к элементам коллекции:

* MappedCollection - принимает в конструкторе произвольную коллекцию и создает карту доступа по указанному ключу.
* MappedCollectionCallback - принимает в конструкторе произвольную коллекцию и создает карту доступа по вычисляемому ключу.

#### Пример использования

```php
use Bx\Model\MappedCollection;
use Bx\Model\MappedCollectionCallback;

$mappedCollection = new MappedCollection($productCollection, 'ID');
$mappedCollection[41]->getName();                   // Product name 41

$mappedCollectionCallback = new MappedCollectionCallback(
    $productCollection, 
    function(CatalogProduct $product){
        return 'product_'.$product->getId();
    }
);
$mappedCollectionCallback['product_41']->getName(); // Product name 41
```

## Сервисы

Для интеграции с СУБД используются сервисы - ModelServiceInterface, данные сервисы
имплементируют базовые операции:

* Запрос списка элементов сущностей (по определенным критериям) - список моделей или коллекция
* Запрос конкретного элемента сущности - модель
* Добавление/обновление элемента сущности - модели
* Удаление элемента сущности - модели

Для реализации сервиса моделей необходимо имплементировать интерфейс ModelServiceInterface, данный интерфейс объединяет слудющие:

* QueryableModelServiceInterface
  * ReadableModelServiceInterface - опреции: getList, getCount и getById
  * FilterableInterface - необходим для указания правил фильтрации
  * SortableInterface - необходим для указания правил сотрировки
  * LimiterInterface - необходим для указания правила ограничения выборки
* SaveableModelServiceInterface - метод сохранения/обновления
* RemoveableModelServiceInterface - метод удаления

В модуле есть не полная реализаци сервиса на основе которой можно создать свой сервис - BaseModelService.

### Пример описания сервиса

```php
use Bx\Modle\BaseModelService;
use Bx\Modle\ModelCollection;

class CatalogProductService extends BaseModelService
{
    protected function getFilterFields(): array
    {
        return [
            // указываем разрешенные для фильтрации поля
        ];
    }

    protected function getSortFields(): array
    {
        return [
            // указываем разрешенные для сортировки поля
        ];
    }

    public function getList(array $params, UserContextInterface $userContext = null): ModelCollection
    { 
        $list = CatalogProductTable::getList($params)->fetchAll(); 
        return new ModelCollection($list, CatalogProduct::class);
    }

    public function getCount(array $params, UserContextInterface $userContext = null): int
    {
        $params['select'] = ['ID'];
        $params['count_total'] = true;
        $params['limit'] = 1;

        return $this->getList($params, $userContext)->first();
    }

    public function getById(int $id, UserContextInterface $userContext = null): ?AbsOptimizedModel;
    {
        $params = [
            'filter' => [
                '=ID' => $id,
            ],
            'limit' => 1,
        ];

        return $this->getList($params, $userContext)->first();
    }

    function save(AbsOptimizedModel $model, UserContextInterface $userContext = null): Result
    {
        $data = [
            'NAME' => $model->getName(), 
        ];

        if ($model->getId() > 0) {
            return CatalogProductTable::update($model->getId(), $data);
        }

        $result = CatalogProductTable::add($data);
        if ($result->isSuccess()) {
            $model['ID'] = $result->getId();
        }

        return $result;
    }

    function delete(int $id, UserContextInterface $userContext = null): Result
    {
        return CatalogProductTable::delete($id);
    }
}
```

### Пример использования сервиса

```php
$catalogProductService = new CatalogProductService();
$productCollection = $catalogProductService->getList([
    'filter' => [
        '=ID' => [1, 5, 10],
    ],
    'select' => [
        'ID',
        'NAME',
    ],
    'order' => [
        'NAME' => 'desc',
    ],
    'limit' => 5,
]);

$productCollection->unique('NAME');
$productCollection->column('ID');

$product = $productCollection->findByKey('ID', 5);
$product->setName('New product name');

$resultSave = $catalogProductService->save($product);
$resultDelete = $catalogProductService->delete(10);
```
### Query

В модуле представлена объектная модель параметров запроса из базы, описана в интерфейсе QueryInterface, так же есть полная реализация данного интрефейса - Query. Объекты данного класса могут содержать параметры: select, filter, order, group, limit,offset. Есть производный интерфейс ModelQueryInterface, в модуле есть полная реалзизация данного интерфейса QueryModel, работает непосредственно с сервисом моделей, сервис передается в конструктор и посредством двойной диспечиризации обеспецивается выборка данных, предоставляет дополнительные методы для выборки моделей:

* loadFiler - формирует фильтр на основе переданного ассоциативного массива в соотвествии с правилами фильтрации сервиса, описанными в методе ModelServiceInterface->getFilterFields()
* loadSort - формирует параметры сортировки данных на основе переданного ассоциативного массива в соотвествии с правилами сортировки сервиса, описанными в методе ModelServiceInterface->getSortFields()
* loadPagination - формирует параметры пагинации на основе переданного массива, используются ключи: limit и page
* getPagination - возвращает обект пагинации PaginationInterface
* getList - возвращает коллецию моделей на сонове сформированных параметров выборки

#### Пример использования

```php
use Bitrix\Main\Application;

$catalogProductService = new CatalogProductService();
$request = Application::getInstance()->getContext()->getRequest();
$queryParams = $request->getQueryParams();

$query = $catalogProductService->query();   // возвращается объект QueryModel
$query->loadFiler($queryParams)             // загружаем фильтр из http запроса
    ->loadSort($queryParams)                // загружаем параметры сотрировки
    ->loadPagination($queryParams);         // загружаем параметры пагинации

$query->hasFilter();                        // проверяет наличие параметров для фильтрации
$query->getFilter();                        // возвращает параметры для фильтрации

$query->hasSort();                          // проверяет наличие параметров для сортировки
$query->getSort();                          // возвращает параметры для сортировки

$query->hasLimit();                         // проверяет наличие параметров для ограничения выборки
$query->getLimit();                         // возвращает параметры ограничения выборки

$query->getPage();                          // номер страницы для пагинации
$query->getOffset();                        // номер элемента с которого начинается выборка

$productCollection = $query->getList();     // возвращает коллекцию товаров в соотвествии с сформированными параметрами выборки
```

### Pagination

Для более удобной работы с пагинацией описан интрефейс PaginationInterface, в модуле имеется полная реализация данного интерфейса - Pagination. Данный класс работает совместно с интерфейсом QueryInterface.

#### Пример использования

```php
use Bitrix\Main\Application;

$catalogProductService = new CatalogProductService();
$request = Application::getInstance()->getContext()->getRequest();
$queryParams = $request->getQueryParams();

$query = $catalogProductService->query();   // возвращается объект QueryModel
$query->loadFiler($queryParams)             // загружаем фильтр из http запроса
    ->loadSort($queryParams)                // загружаем параметры сотрировки
    ->loadPagination($queryParams);         // загружаем параметры пагинации

$pagination = $query->getPagination();      // возвращается объект Pagination
$pagination->getPage();                     // номер текущей страницы
$pagination->getCountPages();               // общее количество страниц
$pagination-getTotalCountElements();        // общее количество элементов
$pagination->getCountElements();            // количество элементов на текущей странице
$pagination->getLimit();                    // максимальное количество элементов на странице

json_encode($pagination);                   // JSON представление
$pagination->toArray();                     // представление в виде ассоциативного массива
```

### Fetcher

Довольно часто тербуется получить модель данных со связанными моделями (по внешним ключам БД или другим критериям), для упрощения этой задачи был описан интерфейс FetcherModelInterface, данный интерфейс имеет полуную реализацию - FetcherModel. Данный класс работает с сервисами моделей, для более простой реализации описан абстракный класс сервиса моделей BaseLinkedModelService на основе этого класса можно описать совой сервис для выборки моделей со связанными моделями.

### Пример описания сервиса

```php
use Bx\Model\BaseLinkedModelService;
use Bx\Model\FetcherModel;
use Bx\Model\Query;
use Bx\Model\Interfaces\FileServiceInterface;

class ExtendedCatalogProductService extends BaseLinkedModelService
{
    /**
     * @var FileServiceInterface
     */
    private $fileService;

    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }

    protected function getFilterFields(): array
    {
        return [
            // указываем разрешенные для фильтрации поля
        ];
    }

    protected function getSortFields(): array
    {
        return [
            // указываем разрешенные для сортировки поля
        ];
    }

    protected function getLinkedFields(): array
    {
        /**
         * В данном методе описываются внешние связи через FetcherModelInterface
         * в виде ассоциативного массива
         */

        $imageQuery = new Query();
        $imageQuery->setFetchList([]);  // пустой массив указывает на то что свзанные модели выбирать не нужно, по-умолчанию выбираются все связанные модели
        $imageQuery->setSelect(['ID', 'SIZE', 'DESCRIPTION']);
        $imageQuery->setFilter('=CONTENT_TYPE' => ['jpg', 'png', 'gif']);

        $docsQuery = new Query();
        $docsQuery->setFetchList([]);
        $docsQuery->setFilter('=CONTENT_TYPE' => ['doc', 'docx', 'pdf']);

        return [
            'image' => FetcherModel::initAsSingleValue( // будет выбрана одна модель
                $this->fileService,
                'image',        // ключ по которому будет доступна связанная модель
                'IMAGE_ID',     // внешний ключ текущей сущности
                'ID',           // первичный ключ связанной сущности
                $imageQuery     // указываем доп. параметры выборки
            ),
            'docs' => FetcherModel::initAsMultipleValue( // будет выбрана коллекция моделей
                $this->fileService,
                'docs',
                'ID',
                'PRODUCT_ID',
                $docsQuery
            )->castTo(AggreageDocumentModel::cass), // выбранная коллекция будет преобразована в указанную агрегационную модель
        ];
    }

    protected function getInternalList(array $params, UserContextInterface $userContext = null): ModelCollection
    { 
        $list = CatalogProductTable::getList($params)->fetchAll(); 
        return new ModelCollection($list, CatalogProduct::class);
    }

    public function getCount(array $params, UserContextInterface $userContext = null): int
    {
        $params['select'] = ['ID'];
        $params['count_total'] = true;
        $params['limit'] = 1;

        return $this->getList($params, $userContext)->first();
    }

    public function getById(int $id, UserContextInterface $userContext = null): ?AbsOptimizedModel;
    {
        $params = [
            'filter' => [
                '=ID' => $id,
            ],
            'limit' => 1,
        ];

        return $this->getList($params, $userContext)->first();
    }

    function save(AbsOptimizedModel $model, UserContextInterface $userContext = null): Result
    {
        $data = [
            'NAME' => $model->getName(), 
        ];

        if ($model->getId() > 0) {
            return CatalogProductTable::update($model->getId(), $data);
        }

        $result = CatalogProductTable::add($data);
        if ($result->isSuccess()) {
            $model['ID'] = $result->getId();
        }

        return $result;
    }

    function delete(int $id, UserContextInterface $userContext = null): Result
    {
        return CatalogProductTable::delete($id);
    }
}
```

#### Пример использования

```php

use Bx\Model\Services\FileService;

$fileService = new FileService();
$productService = new ExtendedCatalogProductService($fileService);

$collection1 = $productService->getList([]);                       // будут выбраны все связанные модели             
$collection2 = $productService->getList(['fetch' => []]);          // связанные модели не будут выбраны
$collection3 = $productService->getList(['fetch' => ['image']]);   // из связанных моделей будет выбораны только модели с ключом image

$firstModel = $collection1->first();
$firstModel['image'];      // Объект File
$firstModel['docs'];       // Объект AggreageDocumentModel
```

### Админ. интерфейсы

В модуле представлены инструменты для быстрого отображения списка моделей в админ. интерфейсе Битрикса, с возможностью фильтрации, поиска, создания произвольных множественных и одиночных событий.

#### Пример использования

```php
use Bx\Model\UI\Admin\ModelGrid;

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

$fileService = new FileService();
$productService = new ExtendedCatalogProductService($fileService);
$grid = new ModelGrid(
    $productService,        // указываем сервис для работы с данными
    'product_list',         // указываем символьный идентификатор списка сущности
    'ID'                    // ключ свойства модели для идентификации
);

/**
 * Указываем поля фильтрации
 */
$grid->addSearchFilterField('name', 'Название');
$grid->addNumericFilterField('id', 'ID');
$grid->addStringFilterField('article', 'Артикул');
$grid->addDateFilterField('date_create', 'Дата создания');
$grid->addListFilterField('status', 'Статус', [
    1 => 'Новый',
    2 => 'Опубликован',
    3 => 'Снят с публикации',
]);

/**
 * Указываем колонки для вывода в таблице
 */
$grid->addColumn('id', 'ID');
$grid->addColumn('name', 'Название');
$grid->addColumn('article', 'Артикул');
$grid->addColumn('date_create', 'Дата создания');
$grid->addCalculateColumn(
    'status', 
    function(ExtendedCatalogProduct $product) {
        return $product->getStatusName();
    },
    'Статус'
);

/**
 * Указываем действия над элементами
 */
$grid->setSingleAction('Удалить', 'delete')
    ->setCallback(function (int $id) use ($productService) {
        $productService->delete($id);
    });
$grid->setSingleAction('Перейти', 'redirect')
    ->setJs('location.href="/bitrix/admin/product_detail.php?id=#id#"');

/**
 * Указываем действия над группой элементов
 */
$grid->setGroupAction('Удалить', 'delete')
    ->useConfirm('Подтвердить')
    ->setCallback(function (array $ids) use ($productService) {
        foreach ($ids as $id) {
            $productService->delete((int)$id);
        }
    });
$grid->setGroupAction('Опубликовать', 'accept')
    ->useConfirm('Подтвердить')
    ->setCallback(function (array $ids) use ($productService) {
        $productCollection = $productService->getList([
            'filter' => [
                '=ID' => $ids,
            ],
            'fetch' => [],
        ]);
        foreach ($productCollection as $currentProduct) {
            $currentProduct->setStatus(2);
            $productService->save($currentProduct);
        }
    });

/**
 * Добавляем кнопку в шапку справа от фильтра (вторая и последующие добавятся как выпадающее меню)
 */
$grid->addAdminButtonLink('Добавить', '/bitrix/admin/product_detail.php?lang='.LANG, 'btn_new');

/**
 * Добавляем ссылку на строку таблицы (переход по двойному клику мышкой)
 * Если не задать второй аргумент title, по умолчанию будет Перейти
 * Если шаблон не подходит, можно использовать setDefaultRowLinkByCallback(callable $fnCalcLink, ?string $linkTitle = null)
 */
$grid->setDefaultRowLinkTemplate('/bitrix/admin/product_detail.php?id=#ID#', 'Изменить');

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
$grid->show();  // показываем собранную таблицу

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
```

## Базовые сервисы

В модуле есть несколько реализованных сервисов:

* FileService - позволяет работать с файлами из таблицы b_file
* UserService - позволяет работать с пользователями из таблицы b_user
### Сервис для работы с пользователями

Помимо стандартных методов сервиса моделей, предоставляет след. методы:

* login(string $login, string $password): ?UserContextInterface - авторизация по логину и паролю
* isAuthorized(): bool - проверка авторизации
* saveExtendedData(User $user, string ...$keyListForSave): Result - сохранение пользователя с произвольным набором данных
* updatePassword(User $user, string $password): Result - обновление пароля пользователя
* getCurrentUser(): ?UserContextInterface - запрос текущего пользователя, по факту возвращает объект контекста из которого уже можно получить модель пользователя

Как видно из описания методов, в ряде случаев сервис возвращает не саму модель а контекст модели - UserContextInterface, а уже из кнотекста можно запросить либо идентификатор пользователя getUserId или саму модель пользователя getUser. В данном интерфейсе есть 2 интересных метода: 

* setAccessStrategy(AccessStrategyInterface $accessStrategy) - позволяет укзать произвольную стратегию по разграничению доступа
* hasAccessOperation(int $operationId): bool - проверяет возможность выполнения пользователем произвольной операции в соотвествии с указанной стратегией

#### Пример использования

```php
use Bx\Model\Services\UserService;

$userService = new UserService();
$userContext = $userService->login('admin@mail.xyz', 'mypassword');
$isAuthorized = $userService->isAuthorized();
$userId = $userContext->getId();

$user = $userContext->getUser();
$user->getId();
$user->getName();

$userContext->setAccessStrategy(new SimpleAccessStrategy());
$userContext->hasAccessOperation(OperationListInterface::CAN_DELETE_FILES);
```


### Сервис для работы с файлами

Помимо стандартных методов сервиса моделей, предоставляет след. методы:

* saveFiles(string $baseDir, string ...$filePaths): ModelCollection - сохранятет файлы из перечисленных url/абслоютных путей и возвращает коллекцию
* function saveUploadFiles(string $baseDir, UploadedFileInterface ...$files): ModelCollection - сохраняет файлы из перечисленных объектов интерфейса UploadedFileInterface - [PSR-7](https://www.php-fig.org/psr/psr-7/)

#### Пример использования

```php
use Bx\Model\Services\FileService;

$fileService = new FileService();
$fileCollection = $fileService->saveFiles(
    'test_dir', 
    'https://some-site.xyz/image1.jpg',
    'https://some-site.xyz/image2.jpg',
    'https://some-site.xyz/image3.jpg' 
);
```