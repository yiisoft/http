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
Method::CONNECT;
Method::TRACE;
```

To have a list of content related methods, use:

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
