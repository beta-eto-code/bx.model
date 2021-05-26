# Bitrix модели


  - [Установка](#установка)
  - [Модели](#модели)
    - [Пример описания модели](#пример-описания-модели)
    - [Пример работы с моделью](#пример-работы-с-моделью)
    - [Агрегационная модель](#агрегационная-модель)
      - [Пример описания агрегационной модели](#пример-описания-агрегационной-модели)
      - [Пример работы с агрегационной моделью](#пример-работы-с-агрегационной-моделью)
  - [Коллекции](#коллекции)
  - [Сервисы](#сервисы)
    - [Query](#query)
    - [Pagination](#pagination)
    - [Fetcher](#fetcher)
    - [Админ. интерфейсы](#админ-интерфейсы)
  - [Базовые сервисы](#базовые-сервисы)
    - [Сервис для работы с пользователями](#сервис-для-работы-с-пользователями)
    - [Сервис для работы с файлами](#сервис-для-работы-с-файлами)

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

Для реализации колеекции необходимо имплементировать интерфейс CollectionInterface или ReadableCollectionInterface если преполагается что набор элементов изменяется. Коллекции в свою очередь могут включать в себя любые объекты имплементирующие интерфейс CollectionItemInterface. В данном модуле уже есть полноценая реализация коллеции Collection.

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

### Коллекции моделей

...

## Сервисы

...
### Query
...
### Pagination
...
### Fetcher
...
### Админ. интерфейсы
...

## Базовые сервисы

...
### Сервис для работы с пользователями

...
### Сервис для работы с файлами

...


Для интеграции с СУБД используются сервисы - ModelServiceInterface, данные сервисы
имплементируют базовые операции:

* Запрос списка элементов сущностей (по определенным критериям) - список моделей или коллекция
* Запрос конкретного элемента сущности - модель
* Добавление/обновление элемента сущности - модели
* Удаление элемента сущности - модели

В репозитории есть несколько реализованных сервисов:

* FileService - позволяет работать с файлами из таблицы b_file
* UserService - позволяет работать с пользователями из таблицы b_user

Для реализации новых моделей необходимо использовать абстрактный класс
AbsOptimizedModel, данный класс требует имплементации только одного метода toArray(): array

Все сервисы моделей должны имплементировать интерфейс ModelServiceInterface.
Есть вспомогательные абстрактные классы:

* BaseModelService 
* BaseLinkedModelService - предоставляет возможность подгрузки связанных моделей
* 