## HTTP заголовки

Из совокупности всех рассмотренных HTTP заголовков были выделены некоторые важные особенности:

- Некоторые заголовки могут иметь не одно значение, а целый список. Значения таких заголовков могут быть перечислены
  через запятую в одном заголовке или по отдельности в одноимённых заголовках.
- Некоторые заголовки могут иметь параметры. Параметры отделяются символом ";" от значения.
- Заголовки со множественными значениями могут иметь особый тип параметра "q" (quality), который может означет
приоритет, "качество" или "вес" значения.
- Существуют заголовки, не имеющие вышеперечисленные качества, лиибо имебщие особый формат.

Указанные свойства прививаются заголовкам с помощью интерфейсов. Например:
```php
/** @see https://tools.ietf.org/html/rfc7231#section-7.4.1 */
final class Allow extends BaseHeaderValue implements ListedValues {}
/** @see https://tools.ietf.org/html/rfc7239 */
final class Forwarded extends BaseHeaderValue implements WithParams, ListedValues {}
```

Любой заголовок при вводе/выводе может быть перечислен несколько раз, поэтому работа с люибым заголовком подразумевается
как с коллекцией значений. Базовым классом для коллекции значений заголовков является `\Yiisoft\Http\Header\Header`. \
Для заголовков, поддерживающих множетво значений, важно соблюдение последовательности этих зачений. Поэтому для
сортируемых списков значений выделенна отдельная коллекция `\Yiisoft\Http\Header\SortableHeader`, а текже специальная
коллекция `\Yiisoft\Http\Header\AcceptHeader` для семейства заголовков `Accept`.

Рассмотрим пример с заголовком Forwarded [[RFC](https://tools.ietf.org/html/rfc7239)].\
Заголовок представляет из себя список из пустых значений с параметрами:

```
Forwarded: for=192.0.2.43,for="[2001:db8:cafe::17]",for=unknown
Forwarded: for=192.0.2.60;proto=http;by=203.0.113.43
```

Следующий код выводит все значения параметров `for`:

```php
<?php
use \Yiisoft\Http\Header\Value\Forwarded;

/** @var \Psr\Http\Message\ServerRequestInterface $request */
$header = Forwarded::createHeader()->addArray($request->getHeader(Forwarded::NAME));
foreach ($header->getValues() as $headerValue) {
    $paramFor = $headerValue->getParams()['for'] ?? null;
    if ($paramFor === null) {
        continue;
    }
    echo $paramFor . "\r\n";
}
?>
```

Если вы не хотите описывать заголвок в отдельном классе, то можете использовать заготовленные классы:
```php
<?php
// создать заголовок с перечисляемыми значениями
$myListHeader = \Yiisoft\Http\Header\Value\ListedValue::createHeader('My-List')
    ->addArray(['foo', 'bar', 'baz']);
// создать заголовок с перечисляемыми сортируемыми значениями
\Yiisoft\Http\Header\Value\SortedValue::createHeader('My-Sorted-List')
    ->addArray(['foo', 'bar;q=0.5', 'baz;q=0'])
    ->inject($response);
?>
```
