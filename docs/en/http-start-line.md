# HTTP start line

An HTTP message can be either a request from client to server or a response from server to client.  Syntactically, the
two types of message differ in the start-line, which is either a request-line (for requests) or a status-line
(for responses).

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

Method::ALL;
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

# ContentDispositionHeader usage

`ContentDispositionHeader` methods are static so usage is like the following:

```php
$name = \Yiisoft\Http\ContentDispositionHeader::name();

$value = \Yiisoft\Http\ContentDispositionHeader::value(
    \Yiisoft\Http\ContentDispositionHeader::INLINE,
     'avatar.png'
);

$value = \Yiisoft\Http\ContentDispositionHeader::inline('document.pdf');

$value = \Yiisoft\Http\ContentDispositionHeader::attachment('document.pdf');
```

# PSR-7 and PSR-17 PhpStorm meta

The package includes PhpStorm meta-files that help IDE to provide values when completing code in cases such as:

```php
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Http\Header;
use Yiisoft\Http\Status;

class StaticController
{
    private ResponseFactoryInterface $responseFactory;

    public function actionIndex(): ResponseInterface
    {
        return $this->responseFactory->createResponse()
            ->withStatus(Status::OK)
            ->withoutHeader(Header::ACCEPT);
    }
}
```
