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
 * GoneHttpException represents a "Gone" HTTP exception with status code 410.
 *
 * Throw a GoneHttpException when a user requests a resource that no longer exists
 * at the requested url. For example, after a record is deleted, future requests
 * for that record should return a 410 GoneHttpException instead of a 404
 * [[NotFoundHttpException]].
 *
 * @see https://tools.ietf.org/html/rfc7231#section-6.5.9
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 * @since 2.0
 */
class GoneHttpException extends HttpException
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
        parent::__construct(Status::GONE, $message, $code, $previous);
    }
}
