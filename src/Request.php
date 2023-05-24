<?php

namespace Effectra\Http\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Represents an HTTP request conforming to the PSR-7 standard.
 */
class Request implements RequestInterface
{
    use MessageTrait;
    /**
     * @var string The HTTP method.
     */
    protected string $method;
    /**
     * @var UriInterface|string The request URI.
     */
    protected UriInterface|string $uri;
    /**
     * Constructs a new Request instance.
     *
     * @param string                 $method  The HTTP method.
     * @param UriInterface|string    $uri     The request URI.
     * @param array                  $headers An array of headers.
     * @param StreamInterface|string $body    The message body.
     * @param string                 $version The HTTP protocol version.
     */
    public function __construct(string $method, UriInterface|string $uri, array $headers = [], $body = '', string $version = '1.1')
    {
        $this->method = $method;
        $this->uri = $uri;

        $this->headers = $headers;
        $this->body = $body;
        $this->protocolVersion = $version;
    }
    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string The HTTP method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    /**
     * Retrieves the request target.
     *
     * @return string The request target.
     */
    public function getRequestTarget(): string
    {
        $target = $this->uri->getPath();
        if ($query = $this->uri->getQuery()) {
            $target .= '?' . $query;
        }
        return $target;
    }
    /**
     * Retrieves the URI instance associated with the request.
     *
     * @return UriInterface The request URI.
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }
    /**
     * Returns an instance with the specific request target.
     *
     * @param mixed $requestTarget The request target.
     * @return static
     * @throws \InvalidArgumentException If the request target contains spaces.
     */
    public function withRequestTarget($requestTarget): static
    {
        if (strpos($requestTarget, ' ') !== false) {
            throw new \InvalidArgumentException('Invalid request target provided; must be a string and cannot contain spaces');
        }

        $clone = clone $this;
        $uri = $clone->uri->withPath('');
        if (strpos($requestTarget, '?') !== false) {
            list($path, $query) = explode('?', $requestTarget, 2);
            $uri = $uri->withPath($path)->withQuery($query);
        } else {
            $uri = $uri->withPath($requestTarget);
        }

        $clone->uri = $uri;
        return $clone;
    }

    /**
     * Returns an instance with the specific HTTP method.
     *
     * @param mixed $method The HTTP method.
     * @return static
     */
    public function withMethod($method): static
    {
        $clone = clone $this;
        $clone->method = strtoupper($method);
        return $clone;
    }
    /**
     * Returns an instance with the specific URI.
     *
     * @param UriInterface $uri          The request URI.
     * @param bool         $preserveHost Whether to preserve the Host header.
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false): static
    {
        $clone = clone $this;
        $clone->uri = $uri;

        if (!$preserveHost) {
            $hostHeader = $this->getHeaderLine('Host');
            if ($hostHeader !== '') {
                $clone = $clone->withoutHeader('Host');
            }
            if ($uri->getHost() !== '') {
                $clone = $clone->withHeader('Host', $uri->getHost());
                if ($uri->getPort() !== null) {
                    $clone = $clone->withHeader('Host', $uri->getHost() . ':' . $uri->getPort());
                }
            }
        }

        return $clone;
    }
}
