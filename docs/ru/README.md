# HTTP Package

## Method constants

Individual HTTP methods could be referenced as

```php
use Yiisoft\Http\Method;

Method::GET;
Method::POST;
Method::PUT;
Method::DELETE;
Method::PATCH;
Method::HEAD;
Method::OPTIONS;
```

To have a list of these, use:

```php
use Yiisoft\Http\Method;

Method::ANY;
```

## HTTP status codes

Status codes could be referenced by name as:

```php
use Yiisoft\Http\Status;

Status::NOT_FOUND;
```

Status text could be obtained as the following:

```php
use Yiisoft\Http\Status;

Status::TEXTS[Status::NOT_FOUND];
```

## HTTP заголовки

Из совокупности всех рассмотренных HTTP заголовков были выделены некоторые важные особенности:

- Некоторые заголовки могут иметь не одно значение, а целый список. Значения таких заголовков могут быть перечислены
  через запятую в одном заголовке или нескольких одноимённых заголовках.
- Некоторые заголовки могут иметь параметры. Параметры отделяются символом ";" от значения.
- Заголовки со множественными значениями могут иметь особый тип параметра "q" (quality), который может означает
приоритет, "качество" или "вес" значения.
- Существуют заголовки, не имеющие вышеперечисленные качества, либо имеющие особый формат.

Указанные свойства прививаются заголовкам с помощью интерфейсов. Например:

```php
/** @see https://tools.ietf.org/html/rfc7231#section-7.4.1 */
final class Allow extends BaseHeaderValue implements ListedValues {}
/** @see https://tools.ietf.org/html/rfc7239 */
final class Forwarded extends BaseHeaderValue implements WithParams, ListedValues {}
```

Указание интерфейсов влияет только на процессы парсинга и генерирования строки поля значения заголовка.
Таким образом, при отсутствующем интерфейсе `WithParams` вам будут доступны методы `withParams()` и `getParams()`, но
при генерировании строки параметры будут игнорироваться.

Рассморим поведение парсинга на примере:

```php
$header = XHeader::createHeader()->add('foo;param1=test;q=0.5, bar');
```
- Если класс `XHeader` не будет иметь интерфейс `ListedValues`, то вся строка станет единственным значением заголовка.
- При наличии только интерфейса `ListedValues` у заголовка будет два значения: `foo;param1=test;q=0.5` и `bar`.
- `ListedValues` и `WithParams` вместе дадут ожидаемый разбор на значения и параметры, однако параметр `q` не будет
  учитываться в качестве параметра сортировки, а метод `getQuality()` будет всегда возвращать "1".
- Введение интерфейса `WithQualityParam` добавит автоматическую сортировку значений, метод `getQuality()` будет зависеть
  от параметра `q`.

Любой заголовок при вводе/выводе может быть перечислен несколько раз, поэтому работа с любым заголовком подразумевается
как с коллекцией значений. Базовым классом для коллекции значений заголовков является `\Yiisoft\Http\Header\Header`. \
Для заголовков, поддерживающих множество значений, важно соблюдение последовательности этих значений. Поэтому для
сортируемых списков значений выделена отдельная коллекция `\Yiisoft\Http\Header\SortableHeader`, а также специальная
коллекция `\Yiisoft\Http\Header\AcceptHeader` для семейства заголовков `Accept`.

Рассмотрим пример с заголовком Forwarded [[RFC](https://tools.ietf.org/html/rfc7239)].\
Заголовок представляет собой список из пустых значений с возможными параметрами `by`, `for`, `host` и `proto`:

```
Forwarded: for=192.0.2.43,for="[2001:db8:cafe::17]",for=unknown
Forwarded: for=192.0.2.60;proto=http;by=203.0.113.43
```

Следующий код выводит все значения параметров `for`:

```php
<?php
use \Yiisoft\Http\Header\Value\Forwarded;

/** @var \Psr\Http\Message\ServerRequestInterface $request */
$header = Forwarded::createHeader()->extract($request);

foreach ($header->getValues() as $headerValue) {
    $paramFor = $headerValue->getParams()['for'] ?? null;
    if ($paramFor === null) {
        continue;
    }
    echo $paramFor . "\r\n";
}
?>
```



```php
/** @var \Psr\Http\Message\ResponseInterface $response */
$dateHeader = \Yiisoft\Http\Header\Value\Date::createHeader()
    ->withValue(new DateTimeImmutable())
    ->inject($response);
$dateHeader = \Yiisoft\Http\Header\Value\Cache\Expires::createHeader()
    ->withValue(new DateTimeImmutable('+1 day'))
    ->inject($response);
```

### Заголовки кеширования [RFC7234](https://tools.ietf.org/html/rfc7234)

Сюда относятся заголовки:

- `Age` - простой заоловок, в котором задаётся количество секунд с момента модификации ресурса.
- `Cache-Control` - список директив правил кеширования.
- `Expires` - дата истечения срока актуальности сущности. Класс заголовка `Expires` наследуется от класса `Date`.
- `Pragma` - устаревший заголовок из HTTP/1.0, использующийся для обратной совместимости.
  Класс `Pragma` наследуется от класса `CacheControl`.
- `Warning` - заголовок для дополнительной информации об ошибках.

#### Cache-Control

```php
$header = \Yiisoft\Http\Header\Value\Cache\CacheControl::createHeader()
    ->withDirective('max-age', '27000')
    ->withDirective('no-transform')
    ->withDirective('foo', 'bar');
```

Для всех стандартных директив, которые описаны в [главе 5.2 RFC7234](https://tools.ietf.org/html/rfc7234#section-5.2),
присваиваемые значения (аргументы) будут проходить этап валидации. Если вы указываете директиву вручную, будьте готовы к
тому, что метод `withDirective()` может выбросить исключение.

```php
use Yiisoft\Http\Header\Value\Cache\CacheControl;
// исключения будут брошены в каждой строчке ниже
(new CacheControl())->withDirective('max-age'); // у директивы max-age должно быть аргумент
(new CacheControl())->withDirective('max-age', 'not numeric'); // аргумент директивы max-age должен быть числовым
(new CacheControl())->withDirective('max-age', '-456'); // допускаются только цифры
(new CacheControl())->withDirective('private', 'ETag,'); // нарушение синтаксиса списка заголовков
(new CacheControl())->withDirective('no-store', 'yes'); // директива no-store не принимает аргумент
```

### Пользовательские заголовки

Если вы не хотите описывать заголовок в отдельном классе, то можете использовать заготовленные классы:
```php
/** @var \Psr\Http\Message\ResponseInterface $response */

// создать заголовок с перечисляемыми значениями
$myListHeader = \Yiisoft\Http\Header\Value\ListedValue::createHeader('My-List')
    ->withValues(['foo', 'bar', 'baz']);
// создать заголовок с перечисляемыми сортируемыми значениями
\Yiisoft\Http\Header\Value\SortedValue::createHeader('My-Sorted-List')
    ->withValues(['foo', 'bar;q=0.5', 'baz;q=0'])
    ->inject($response);
```
