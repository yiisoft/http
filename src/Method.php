<?php

namespace Yiisoft\Http;

final class Method
{
    /**
     * The GET method requests transfer of a current selected representation
     * for the target resource.  GET is the primary mechanism of information
     * retrieval and the focus of almost all performance optimizations.
     * Hence, when people speak of retrieving some identifiable information
     * via HTTP, they are generally referring to making a GET request.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.1
     */
    public const GET = 'GET';

    /**
     * The POST method requests that the target resource process the
     * representation enclosed in the request according to the resource's
     * own specific semantics.  For example, POST is used for the following
     * functions (among others):
     *
     * - Providing a block of data, such as the fields entered into an HTML
     *   form, to a data-handling process;
     * - Posting a message to a bulletin board, newsgroup, mailing list,
     *   blog, or similar group of articles;
     * - Creating a new resource that has yet to be identified by the
     * origin server; and
     * - Appending data to a resource's existing representation(s).
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.3
     */
    public const POST = 'POST';

    /**
     * The PUT method requests that the state of the target resource be
     * created or replaced with the state defined by the representation
     * enclosed in the request message payload.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.4
     */
    public const PUT = 'PUT';

    /**
     * The DELETE method requests that the origin server remove the
     * association between the target resource and its current
     * functionality.  In effect, this method is similar to the rm command
     * in UNIX: it expresses a deletion operation on the URI mapping of the
     * origin server rather than an expectation that the previously
     * associated information be deleted.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.5
     */
    public const DELETE = 'DELETE';

    /**
     * The PATCH method requests that a set of changes described in the
     * request entity be applied to the resource identified by the Request-
     * URI.
     *
     * @link https://tools.ietf.org/html/rfc5789#section-2
     */
    public const PATCH = 'PATCH';

    /**
     * The HEAD method is identical to GET except that the server MUST NOT
     * send a message body in the response (i.e., the response terminates at
     * the end of the header section).
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.2
     */
    public const HEAD = 'HEAD';

    /**
     * The OPTIONS method requests information about the communication
     * options available for the target resource, at either the origin
     * server or an intervening intermediary.  This method allows a client
     * to determine the options and/or requirements associated with a
     * resource, or the capabilities of a server, without implying a
     * resource action.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.7
     */
    public const OPTIONS = 'OPTIONS';

    /**
     * The TRACE method requests a remote, application-level loop-back of
     * the request message.The final recipient of the request SHOULD
     * reflect the message received, excluding some fields,
     * back to the client as the message body of a 200 (OK) response with a
     * Content-Type of "message/http" (Section 8.3.1 of [RFC7230]).  The
     * final recipient is either the origin server or the first server to
     * receive a Max-Forwards value of zero (0) in the request
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.8
     */
    public const TRACE = 'TRACE';

    /**
     * The CONNECT method requests that the recipient establish a tunnel to
     * the destination origin server identified by the request-target and,
     * if successful, thereafter restrict its behavior to blind forwarding
     * of packets, in both directions, until the tunnel is closed.  Tunnels
     * are commonly used to create an end-to-end virtual connection, through
     * one or more proxies, which can then be secured using TLS (Transport
     * Layer Security, [RFC5246]).
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.3.6
     */
    public const CONNECT = 'CONNECT';

    /**
     * Array of content related methods
     */
    public const ANY = [
        self::GET,
        self::POST,
        self::PUT,
        self::DELETE,
        self::PATCH,
        self::HEAD,
        self::OPTIONS,
    ];
}
