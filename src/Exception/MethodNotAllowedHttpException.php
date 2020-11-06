<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Http\Exception;

use Throwable;
use Yiisoft\Http\Status;

/**
 * MethodNotAllowedHttpException represents a "Method Not Allowed" HTTP exception with status code 405.
 *
 * @see https://tools.ietf.org/html/rfc7231#section-6.5.5
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MethodNotAllowedHttpException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string|null $message error message
     * @param int $code error code
     * @param Throwable|null $previous The previous exception used for the exception chaining.
     */
    public function __construct(string $message = null, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(Status::METHOD_NOT_ALLOWED, $message, $code, $previous);
    }
}
