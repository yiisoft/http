<?php

namespace Yiisoft\Http;

/**
 * HTTP status codes
 */
final class Status
{
    // Informational responses
    /**
     * This interim response indicates that everything so far is OK and that the client should continue the request,
     * or ignore the response if the request is already finished.
     * @link https://tools.ietf.org/html/rfc7231#section-6.2.1
     */
    public const CONTINUE = 100;
    /**
     * This code is sent in response to an `Upgrade` request header from the client,
     * and indicates the protocol the server is switching to.
     * @link https://tools.ietf.org/html/rfc7231#section-6.2.2
     */
    public const SWITCHING_PROTOCOLS = 101;
    /**
     * This code indicates that the server has received and is processing the request,
     * but no response is available yet.
     */
    public const PROCESSING = 102;

    // Successful responses
    /**
     * The request has succeeded. The meaning of the success depends on the HTTP method:
     *  - GET: The resource has been fetched and is transmitted in the message body.
     *  - HEAD: The entity headers are in the message body.
     *  - PUT or POST: The resource describing the result of the action is transmitted in the message body.
     *  - TRACE: The message body contains the request message as received by the server
     * @link https://tools.ietf.org/html/rfc7231#section-6.3.1
     */
    public const OK = 200;
    /**
     * The request has succeeded and a new resource has been created as a result.
     * This is typically the response sent after POST requests, or some PUT requests.
     * @link https://tools.ietf.org/html/rfc7231#section-6.3.2
     */
    public const CREATED = 201;
    /**
     * The request has been received but not yet acted upon. It is noncommittal, since there is no way in HTTP to later
     * send an asynchronous response indicating the outcome of the request.
     * It is intended for cases where another process or server handles the request, or for batch processing.
     * @link https://tools.ietf.org/html/rfc7231#section-6.3.3
     */
    public const ACCEPTED = 202;
    /**
     * This response code means the returned meta-information is not exactly the same as is available
     * from the origin server, but is collected from a local or a third-party copy.
     * This is mostly used for mirrors or backups of another resource.
     * Except for that specific case, the "200 OK" response is preferred to this status.
     * @link https://tools.ietf.org/html/rfc7231#section-6.3.4
     */
    public const NON_AUTHORITATIVE_INFORMATION = 203;
    public const NO_CONTENT = 204;
    public const RESET_CONTENT = 205;
    public const PARTIAL_CONTENT = 206;
    public const MULTI_STATUS = 207;
    public const ALREADY_REPORTED = 208;
    public const CONTENT_DIFFERENT = 210;
    public const IM_USED = 226;

    // Redirects
    public const MULTIPLE_CHOICES = 300;
    public const MOVED_PERMANENTLY = 301;
    public const FOUND = 302;
    public const SEE_OTHER = 303;
    public const NOT_MODIFIED = 304;
    public const USE_PROXY = 305;
    public const RESERVED = 306;
    public const TEMPORARY_REDIRECT = 307;
    public const PERMANENT_REDIRECT = 308;

    // Client errors
    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const PAYMENT_REQUIRED = 402;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const METHOD_NOT_ALLOWED = 405;
    public const NOT_ACCEPTABLE = 406;
    public const PROXY_AUTHENTICATION_REQUIRED = 407;
    public const REQUEST_TIMEOUT = 408;
    public const CONFLICT = 409;
    public const GONE = 410;
    public const LENGTH_REQUIRED = 411;
    public const PRECONDITION_FAILED = 412;
    public const ENTITY_TOO_LARGE = 413;
    public const URI_TOO_LONG = 414;
    public const UNSUPPORTED_MEDIA_TYPE = 415;
    public const RANGE_UNSATISFIABLE = 416;
    public const EXPECTATION_FAILED = 417;
    public const I_AM_A_TEAPOT = 418;
    public const MISDIRECTED_REQUEST = 421;
    public const UNPROCESSABLE_ENTITY = 422;
    public const LOCKED = 423;
    public const METHOD_FAILURE = 424;
    public const UNORDERED_FAILURE = 425;
    public const UPGRADE_REQUIRED = 426;
    public const PRECONDITION_REQUIRED = 428;
    public const TOO_MANY_REQUESTS = 429;
    public const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    public const RETRY_WITH = 449;
    public const BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;
    public const UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    // Server errors
    public const INTERNAL_SERVER_ERROR = 500;
    public const NOT_IMPLEMENTED = 501;
    public const BAD_GATEWAY_OR_PROXY_ERROR = 502;
    public const SERVICE_UNAVAILABLE = 503;
    public const GATEWAY_TIMEOUT = 504;
    public const HTTP_VERSION_NOT_SUPPORTED = 505;
    public const INSUFFICIENT_STORAGE = 507;
    public const LOOP_DETECTED = 508;
    public const BANDWITH_LIMIT_EXCEEDED = 509;
    public const NOT_EXTENDED = 510;
    public const NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * @var array list of HTTP status texts
     */
    public const STATUS_TEXTS = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Entity Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];
}
