# HTTP заголовки

## Список HTTP заголовков


Пакет `yiisoft/http` предоставляет инструменты для парсинга и генерирования заголовков с учётом их особенностей

## Класс Header

Абсолютно любой заголовок в поле заголовков HTTP-сообщения может быть указан несколько раз, даже если это не разрешается
правилами самого заголовка. Поэтому в результате парсинга любого заголовка вы получите коллекцию значений.

Базовым классом для коллекции значений заголовков является `\Yiisoft\Http\Header\Header`. Он подходит для заголовков,
в которых важно соблюдение порядка значений в заданной последовательности. Для сортируемых списков значений выделена
отдельная коллекция `\Yiisoft\Http\Header\SortableHeader`, а также специальная коллекция
`\Yiisoft\Http\Header\AcceptHeader` для семейства заголовков `Accept`. В сортируемых коллекциях значения сразу занимают
места по порядку согласно параметру `q` или иным правилам, описанным в коллекции.

Рассмотрим пример с заголовком `Forwarded` [RFC7239](https://tools.ietf.org/html/rfc7239).\
Заголовок представляет собой список из пустых значений с возможными параметрами `by`, `for`, `host` и `proto`:

```
Forwarded: for=192.0.2.43,for="[2001:db8:cafe::17]",for=unknown
Forwarded: for=192.0.2.60;proto=http;by=203.0.113.43
```

Следующий код выводит все значения параметров `for`:

```php
use \Yiisoft\Http\Header\Value\Forwarded;

/** @var \Psr\Http\Message\ServerRequestInterface $request */
$header = Forwarded::createHeader()->withValues($request->getHeader('Forwarded'));

foreach ($header->getValues() as $headerValue) {
    $paramFor = $headerValue->getParams()['for'] ?? null;
    if ($paramFor === null) {
        continue;
    }
    echo "{$paramFor}\n";
}
```

Каждый объект коллекции `Header` привязывается к указанному при создании классу заголовка. В коллекцию одного заголовка
нельзя передать значения другого заголовка, даже если один из них является наследником другого:

```php
use \Yiisoft\Http\Header\Value\Date;
use \Yiisoft\Http\Header\Value\Forwarded;

$header = Forwarded::createHeader();

// Будет брошено исключение
$header = $header->withValue(new Date(new DateTimeImmutable()));
```

Для взаимодействия с объектами PSR7, реализующими интерфейс `\Psr\Http\Message\MessageInterface`, в классе `Header`
предусмотрены методы импорта и экспорта заголовков: `exctract()` и `inject()`

```php
use \Yiisoft\Http\Header\Value\Allow;
/** @var \Psr\Http\Message\ServerRequestInterface $request */
/** @var \Psr\Http\Message\ResponseInterface $response */

// Получить коллекцию значений заголовка Allow из объекта запроса
$header = Allow::createHeader()->extract($request);

// Записать заголовки в объект ответа
$response = $header->inject($response, $replace = true);
```

## Заголовки

### Date

Вы можете использовать класс `Date` для конвертирования объекта `\DateTimeInterface` в строку формата времени HTTP.

```php
use \Yiisoft\Http\Header\Value\Date;

$date = new Date(new DateTimeImmutable('2020-01-01 00:00:00 +0000'));

echo new Date(new DateTimeImmutable('2020-01-01 00:00:00 +0000'));
// Wed, 01 Jan 2020 00:00:00 GMT

// Внедрить в ответ качестве заголовка
/** @var \Psr\Http\Message\ResponseInterface $response */
$response = $date->inject($response);
```

### ETag

```php
use Yiisoft\Http\Header\Value\Condition\ETag;
/** @var \Psr\Http\Message\ResponseInterface $response */

$etag = (new ETag())->withTag('56d-9989200-1132c580', true)->inject($response);
```

## Заголовки кеширования

[RFC7234](https://tools.ietf.org/html/rfc7234)

### Age

В заголовке `Age` задаётся количество секунд с момента модификации ресурса. Поэтому все значения, имеющие символы,
отличные от десятичных цифр, будут помечены как не валидные.

### Expires

Дата истечения срока актуальности сущности. Класс заголовка `Expires` наследуется от класса `Date`.

```php
use Yiisoft\Http\Header\Value\Cache\Expires;
/** @var \Psr\Http\Message\ResponseInterface $response */

$dateHeader = (new Expires(new DateTimeImmutable('+1 day')))
    ->inject($response);
```

### Warning

Заголовок для дополнительной информации об ошибках.
```php
use \Yiisoft\Http\Header\Value\Cache\Warning;
/** @var \Psr\Http\Message\ResponseInterface $response */

$dateHeader = Warning::createHeader()
    ->withValue((new Warning())->withDataset(Warning::RESPONSE_IS_STALE, '-', 'Response is stale'))
    ->withValue((new Warning())->withDataset(Warning::REVALIDATION_FAILED, '-', 'Revalidation failed'))
    ->inject($response);
```

### Cache-Control

Заголовок `Cache-Control` задаёт правила кеширования. Его особенность заключается в том, что вместо значений и их
параметров используются директивы. С целью переиспользования кода парсера директива без аргумента парсится как значение,
а директива с аргументом — как параметр без значения. Однако для пользователя выделен отдельный метод для записи
директивы и аргумента `withDirective(string $directive, string $argument = null)`

```php
$header = \Yiisoft\Http\Header\Value\Cache\CacheControl::createHeader()
    ->withDirective('max-age', '27000')
    ->withDirective('no-transform')
    ->withDirective('foo', 'bar');
```

Для всех стандартных директив, которые описаны в [главе 5.2 RFC7234](https://tools.ietf.org/html/rfc7234#section-5.2),
аргументы будут проходить этап валидации. Если вы указываете директиву вручную, будьте готовы к тому, что метод
`withDirective()` может выбросить исключение.

```php
use Yiisoft\Http\Header\Value\Cache\CacheControl;

// исключения будут брошены в каждой строчке ниже
(new CacheControl())->withDirective('max-stale'); // у директивы max-stale должен быть аргумент
(new CacheControl())->withDirective('max-age', 'not numeric'); // аргумент директивы max-age должен быть числовым
(new CacheControl())->withDirective('max-age', '-456'); // допускаются только цифры
(new CacheControl())->withDirective('private', 'ETag,'); // нарушение синтаксиса списка заголовков
(new CacheControl())->withDirective('no-store', 'yes'); // директива no-store не принимает аргумент

// Исключение выброшено не будет, однако все элементы коллекции будут не валидными
CacheControl::createHeader()->withValue('max-stale, max-age=test, private="ETag,", no-store=yes');
```

### Pragma

Устаревший заголовок из HTTP/1.0, использующийся для обратной совместимости.
Класс заголовка `Pragma` наследуется от класса `CacheControl`.

## Пользовательские заголовки

Если для требуемого заголовка вы не нашли среди классов подходящий, либо целевой заголовок не имеет заранее определённое
имя, то вы можете также использовать классы безымянных заголовков с предопределёнными правилами парсинга:

```php
/** @var \Psr\Http\Message\ServerRequestInterface $request */
/** @var \Psr\Http\Message\ResponseInterface $response */

// извлечь из запроса заголовок My-List с перечисляемыми значениями
$values = \Yiisoft\Http\Header\Value\Unnamed\ListedValue::createHeader('My-List')
    ->extract($request)
    ->getValues();

// создать заголовок My-Sorted-List с перечисляемыми сортируемыми значениями
$sorted = \Yiisoft\Http\Header\Value\Unnamed\SortedValue::createHeader('My-Sorted-List')
    ->withValues(['foo', 'bar;q=0.5', 'baz;q=0']);
```
