# HTTP заголовки

Пакет `yiisoft/http` предоставляет инструменты для парсинга и генерирования заголовков с учётом их особенностей

## Правила парсинга

Каждый заголовок состоит из нечувствительного к регистру имени заголовка, двоеточия и затем поля значения, которое может
быть обособлено необязательными пробелами.

В поле значения может находиться как одно, так и несколько значений заголовка, как с параметрами, так и без них.
Существуют также заголовки без значений, но с параметрами (Forwarded, Keep-Alive), либо с директивами (Forwarded).

Пример реальных заголовков, использующихся в сообщениях запросов и ответов:

```text
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng
Accept: */*;q=0.8,application/signed-exchange;v=b3;q=0.9
Allow: GET, POST, HEAD
Content-Range: bytes 88080384-160993791/160993792
Connection: Keep-Alive
Keep-Alive: timeout=5, max=100
Date: Mon, 16 Mar 2020 09:59:58 GMT
ETag: W/"3d19ee-418a3-5a0d6b89d613d;5a0f5e1acb07d"
Content-Disposition: attachment; filename="filename.jpg"
```

С целью выделения общих правил парсинга в контексте пакета `yiisoft/http` HTTP-заголовки разбиты на несколько частично
пересекающихся групп:

1. Заголовки, имеющие одно значение (Date, ETag) или несколько, но в особом формате (Content-Range).
2. Заголовки, которые позволяют иметь множество значений (список). Значения таких заголовков могут быть перечислены в
   одном заголовке через запятую или в нескольких одноимённых заголовках. Это такие заголовки, как Accept, Allow,
   Keep-Alive и т.д.
3. Заголовки, которые могут иметь параметры. Параметры отделяются символом ";" от значения. В примере выше это заголовки
   Accept, Keep-Alive и Content-Disposition.
4. Группа заголовков со множественными значениями и параметрами, среди которых присутствует особый параметр "q"
   (quality), который может означает приоритет, "качество" или "вес" значения. В приведённом примере это заголовок
   Allow.

Правила обработки каждого заголовка описываются в отдельных классах, наследующихся от класса
`\Yiisoft\Http\Header\Value\BaseHeaderValue`.
Для того, чтобы отнести заголовок к той или иной группе из списка выше, в описываемом заголовок классе следует указать
интерфейс, соответствующий этой группе.
По умолчанию, если класс заголовка не имеет интерфейсов, задающих правила парсинга, то он обрабатывается как заголовок
первой группы.

Пример:

```php
use \Yiisoft\Http\Header\Rule\ListedValues;
use \Yiisoft\Http\Header\Rule\WithParams;
use \Yiisoft\Http\Header\Value\BaseHeaderValue;

# В поле значения заголовка передаётся только одно значение
final class Date extends BaseHeaderValue {}
# Заголовок со списком значений
final class Allow extends BaseHeaderValue implements ListedValues {}
# Со списком значений и параметрами
final class Forwarded extends BaseHeaderValue implements WithParams, ListedValues {}
```

Указание интерфейсов влияет только на процессы парсинга и генерирования поля значения заголовка. Таким образом, при
отсутствующем интерфейсе `WithParams` вам будут доступны методы `withParams()` и `getParams()`, но при генерировании
строки параметры будут игнорироваться, если метод генератора не переопределён.

Рассмотрим поведение парсера на примере произвольного заголовка `X-Header`:

```php
$header = XHeader::createHeader()->add('foo;param1=test;q=0.5, bar');
```
 - Если класс `XHeader` не будет иметь интерфейс `ListedValues`, то вся строка, передаваемая в метод `add()` станет
   единственным значением заголовка.
 - При наличии только одного интерфейса `ListedValues` у заголовка будет два значения: `foo;param1=test;q=0.5` и `bar`.
 - `ListedValues` и `WithParams` вместе дадут ожидаемый разбор на значения и параметры, однако параметр `q` не будет
   учитываться в качестве параметра сортировки, а метод `getQuality()` будет возвращать "1", как значение по умолчанию.
 - Указание интерфейса `WithQualityParam` добавит автоматическую сортировку значений, а метод `getQuality()` будет
   зависеть от параметра `q`.

## Класс Header

Абсолютно любой заголовок в поле заголовков HTTP-сообщения может быть указан несколько раз, даже если это не разрешается
правилами самого заголовка. Поэтому работа с любым заголовком происходит как с коллекцией валидных и не валидных
значений.

Базовым классом для коллекции значений заголовков является `\Yiisoft\Http\Header\Header`. \
Для заголовков, поддерживающих множество значений, бывает важно соблюдение последовательности этих значений. Поэтому для
сортируемых списков значений выделена отдельная коллекция `\Yiisoft\Http\Header\SortableHeader`, а также специальная
коллекция `\Yiisoft\Http\Header\AcceptHeader` для семейства заголовков `Accept`. В сортируемых коллекциях значения сразу
занимают места по порядку согласно параметру `q` или иным правилам, описанным в коллекции.

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

## Примеры



## Заголовки

### Date

Для заголовков `Date` и других, значением которых является дата, выделен отдельный класс-коллекция `DateHeader`,
который поддерживает работу со значениями, реализующими интерфейс `\DateTimeInterface`.

```php
use \Yiisoft\Http\Header\Value\Date;
/** @var \Psr\Http\Message\ResponseInterface $response */

$response = Date::createHeader()
    ->withValue(new DateTimeImmutable())
    ->inject($response);
```

Вы можете использовать класс `Date` для конвертирования объекта `\DateTimeInterface` в строку формата времени HTTP:

```php
use \Yiisoft\Http\Header\Value\Date;

// В $date будет записано "Wed, 01 Jan 2020 00:00:00 GMT"
$date = (string)(new Date(new DateTimeImmutable('2020-01-01 00:00:00 +0000')));
```

## Заголовки кеширования

[RFC7234](https://tools.ietf.org/html/rfc7234)

### Age

В заголовке `Age` задаётся количество секунд с момента модификации ресурса. Поэтому все значения, имеющие символы,
отличные от десятичных цифр, будут помечены как не валидные.

### Expires

Дата истечения срока актуальности сущности. Класс заголовка `Expires` наследуется от класса `Date`.

```php
$dateHeader = \Yiisoft\Http\Header\Value\Cache\Expires::createHeader()
    ->withValue(new DateTimeImmutable('+1 day'));
```

### Warning

Заголовок для дополнительной информации об ошибках.

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

Если вы не хотите описывать заголовок в отдельном классе, либо целевой заголовок не имеет заранее определённое имя, то
вы можете использовать заготовленные классы:

```php
/** @var \Psr\Http\Message\ServerRequestInterface $request */
/** @var \Psr\Http\Message\ResponseInterface $response */

// извлечь из запроса заголовок с перечисляемыми значениями
$listed = \Yiisoft\Http\Header\Value\ListedValue::createHeader('My-List')
    ->extract($request);

// создать заголовок с перечисляемыми сортируемыми значениями
$sorted = \Yiisoft\Http\Header\Value\SortedValue::createHeader('My-Sorted-List')
    ->withValues(['foo', 'bar;q=0.5', 'baz;q=0']);
```
