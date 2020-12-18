<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii HTTP</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/http/v/stable.png)](https://packagist.org/packages/yiisoft/http)
[![Total Downloads](https://poser.pugx.org/yiisoft/http/downloads.png)](https://packagist.org/packages/yiisoft/http)

The package provides:

- Constants for HTTP protocol headers, methods and statuses. All along with short descriptions and RFC links. 
- PSR-7, PSR-17 PhpStorm meta for HTTP protocol headers, methods and statuses.

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

## PSR-7 and PSR-17 PhpStorm meta

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
