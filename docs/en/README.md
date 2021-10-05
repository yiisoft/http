# HTTP Package

All HTTP/1.1 messages consist of a start-line followed by a sequence of octets in a format similar to the Internet
Message Format [RFC5322](https://tools.ietf.org/html/rfc5322): zero or more header fields (collectively referred to as
the "headers" or the "header section"), an empty line indicating the end of the header section, and an optional message
body.

- [HTTP start line](http-start-line.md) (Method constants and status codes)
- [HTTP headers](http-headers.md)

Related links to RFC:

* [RFC7230](https://tools.ietf.org/html/rfc7230) HTTP/1.1: Message Syntax and Routing
* [RFC7231](https://tools.ietf.org/html/rfc7231) HTTP/1.1: Semantics and Content
* [RFC7232](https://tools.ietf.org/html/rfc7232) HTTP/1.1: Conditional Requests
* [RFC7233](https://tools.ietf.org/html/rfc7233) HTTP/1.1: Range Requests
* [RFC7234](https://tools.ietf.org/html/rfc7234) HTTP/1.1: Caching
* [RFC7235](https://tools.ietf.org/html/rfc7235) HTTP/1.1: Authentication
