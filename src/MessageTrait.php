<?php

namespace Effectra\Http\Message;

use Psr\Http\Message\StreamInterface;

/**
 * Trait implementing functionality common to requests and responses.
 */
trait MessageTrait {
    /**
     * @var string The HTTP protocol version.
     */
    private string $protocolVersion = '1.1';

    /**
     * @var array An array of headers.
     */
    private array $headers = [];

    /**
     * @var StreamInterface|string The message body.
     */
    private $body;


    /**
     * Retrieves the HTTP protocol version.
     *
     * @return string The HTTP protocol version.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }
    /**
     * Retrieves a header value by name.
     *
     * @param string $name The header name.
     * @return array An array of header values.
     */
    public function getHeader(string $name): array
    {
        return $this->headers[$name] ?? [];
    }
    /**
     * Retrieves the message headers.
     *
     * @return array An array of headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
    /**
     * Retrieves a header value as a comma-separated string.
     *
     * @param string $name The header name.
     * @return string The header value as a comma-separated string.
     */
    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }
    /**
     * Retrieves the message body.
     *
     * @return StreamInterface The message body as a StreamInterface instance.
     */
    public function getBody(): StreamInterface
    {
        if (!$this->body instanceof StreamInterface) {
            return new Stream((string) $this->body);
        }
        return $this->body;
    }
    /**
     * Returns an instance with the specified protocol version.
     *
     * @param string $version The HTTP protocol version.
     * @return self
     */
    public function withProtocolVersion(string $version): self
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }
    /**
     * Checks if a header exists.
     *
     * @param string $name The header name.
     * @return bool True if the header exists, false otherwise.
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }
    /**
     * Returns an instance with the specified header value.
     *
     * @param string          $name  The header name.
     * @param string|string[] $value The header value.
     * @return self
     * @throws \InvalidArgumentException If the header value is not a string or an array of strings.
     */
    public function withHeader(string $name, $value): self
    {
        if (!is_string($value) && !is_array($value)) {
            throw new \InvalidArgumentException('Header value must be a string or an array of strings');
        }

        $normalizedValue = $this->normalizeHeaderValues($value);

        $new = clone $this;
        $new->headers[$name] = $normalizedValue;
        return $new;
    }
    /**
     * Returns an instance with the specified headers.
     *
     * @param array $headers An array of headers.
     * @return self
     */
    public function withHeaders(array $headers): self
    {
        $clone = clone $this;
        foreach ($headers as $header) {
            $clone->headers[strtolower(key($header))] = (array) $header[key($header)];
        }
        return $clone;
    }
    /**
     * Returns an instance with the specified appended header value.
     *
     * @param string          $name  The header name.
     * @param string|string[] $value The header value.
     * @return self
     * @throws \InvalidArgumentException If the header value is not a string or an array of strings.
     */
    public function withAddedHeader(string $name, $value): self
    {
        if (!is_string($value) && !is_array($value)) {
            throw new \InvalidArgumentException('Header value must be a string or an array of strings');
        }

        $normalizedValue = $this->normalizeHeaderValues($value);

        $new = clone $this;
        if (isset($new->headers[$name])) {
            $new->headers[$name] = array_merge($new->headers[$name], $normalizedValue);
        } else {
            $new->headers[$name] = $normalizedValue;
        }
        return $new;
    }
    /**
     * Returns an instance without the specified header.
     *
     * @param string $name The header name.
     * @return self
     */
    public function withoutHeader(string $name): self
    {
        $new = clone $this;
        unset($new->headers[$name]);
        return $new;
    }
    /**
     * Returns an instance with the specified message body.
     *
     * @param StreamInterface|string $body The message body.
     * @return self
     */
    public function withBody(StreamInterface|string $body): self
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }
    /**
     * Normalize header values.
     *
     * This method takes a value and normalizes it to an array of strings representing header values.
     *
     * @param string|array $value The value to normalize.
     * @return array The normalized header values as an array of strings.
     */
    private function normalizeHeaderValues($value): array
    {
        if (is_string($value)) {
            return [$value];
        }

        if (is_array($value)) {
            return array_map(function ($item) {
                return (string) $item;
            }, $value);
        }

        return [];
    }
}
