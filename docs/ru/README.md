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

Если вы не хотите описывать заголовок в отдельном классе, то можете использовать заготовленные классы:
```php
<?php
// создать заголовок с перечисляемыми значениями
$myListHeader = \Yiisoft\Http\Header\Value\ListedValue::createHeader('My-List')
    ->withValues(['foo', 'bar', 'baz']);
// создать заголовок с перечисляемыми сортируемыми значениями
\Yiisoft\Http\Header\Value\SortedValue::createHeader('My-Sorted-List')
    ->withValues(['foo', 'bar;q=0.5', 'baz;q=0'])
    ->inject($response);
?>
```
