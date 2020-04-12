<?php

namespace PHPSTORM_META {

    expectedReturnValues(\Psr\Http\Message\RequestInterface::getMethod(),
        \Yiisoft\Http\Method::DELETE,
        \Yiisoft\Http\Method::GET,
        \Yiisoft\Http\Method::HEAD,
        \Yiisoft\Http\Method::OPTIONS,
        \Yiisoft\Http\Method::PATCH,
        \Yiisoft\Http\Method::POST,
        \Yiisoft\Http\Method::PUT,
    );

    expectedArguments(\Psr\Http\Message\RequestInterface::withMethod(),
        0,
        \Yiisoft\Http\Method::DELETE,
        \Yiisoft\Http\Method::GET,
        \Yiisoft\Http\Method::HEAD,
        \Yiisoft\Http\Method::OPTIONS,
        \Yiisoft\Http\Method::PATCH,
        \Yiisoft\Http\Method::POST,
        \Yiisoft\Http\Method::PUT,
    );

    expectedArguments(\Psr\Http\Message\RequestFactoryInterface::createRequest(),
        0,
        \Yiisoft\Http\Method::DELETE,
        \Yiisoft\Http\Method::GET,
        \Yiisoft\Http\Method::HEAD,
        \Yiisoft\Http\Method::OPTIONS,
        \Yiisoft\Http\Method::PATCH,
        \Yiisoft\Http\Method::POST,
        \Yiisoft\Http\Method::PUT,
    );

    expectedArguments(\Psr\Http\Message\ServerRequestFactoryInterface::createServerRequest(),
        0,
        \Yiisoft\Http\Method::DELETE,
        \Yiisoft\Http\Method::GET,
        \Yiisoft\Http\Method::HEAD,
        \Yiisoft\Http\Method::OPTIONS,
        \Yiisoft\Http\Method::PATCH,
        \Yiisoft\Http\Method::POST,
        \Yiisoft\Http\Method::PUT,
    );
}
