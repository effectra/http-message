<?php

namespace Effectra\Http\Message;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Represents an HTTP server request conforming to the PSR-7 standard.
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * @var array The query parameters.
     */
    protected $queryParams;
    /**
     * @var mixed The parsed body.
     */
    protected $parsedBody;
    /**
     * @var array The request attributes.
     */
    protected $attributes;
    /**
     * @var string The request target.
     */
    protected $requestTarget;
    /**
     * @var array The uploaded files.
     */
    protected $uploadedFiles;

    /**
     * Creates a new ServerRequest instance.
     *
     * @param string $method          The HTTP method.
     * @param mixed  $uri             The request URI.
     * @param array  $headers         An array of headers.
     * @param string $body            The request body.
     * @param string $protocolVersion The HTTP protocol version.
     * @param array  $queryParams     The query parameters.
     * @param mixed  $parsedBody      The parsed body.
     * @param array  $attributes      The request attributes.
     */
    public function __construct(string $method, $uri, array $headers = [], $body = '', string $protocolVersion = '1.1', array $queryParams = [], $parsedBody = null, array $attributes = [])
    {
        parent::__construct($method, $uri, $headers, $body, $protocolVersion);
        $this->queryParams = $queryParams;
        $this->parsedBody = $parsedBody;
        $this->attributes = $attributes;
    }
    /**
     * Retrieves the server parameters.
     *
     * @return array The server parameters.
     */
    public function getServerParams(): array
    {
        return $_SERVER;
    }
    /**
     * Retrieves the cookie parameters.
     *
     * @return array The cookie parameters.
     */
    public function getCookieParams(): array
    {
        return $_COOKIE;
    }
    /**
     * Returns an instance with the specified cookie parameters.
     *
     * @param array $cookies The cookie parameters.
     * @return static
     */
    public function withCookieParams(array $cookies): static
    {
        $new = clone $this;
        $_COOKIE = $cookies;
        return $new;
    }
    /**
     * Retrieves the query parameters.
     *
     * @return array The query parameters.
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }
    /**
     * Returns an instance with the specified query parameters.
     *
     * @param array $query The query parameters.
     * @return static
     */
    public function withQueryParams(array $query): static
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }
    /**
     * Retrieves the uploaded files.
     *
     * @return array The uploaded files.
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }
    /**
     * Returns an instance with the specified uploaded files.
     *
     * @param array $uploadedFiles The uploaded files.
     * @return static
     * @throws \InvalidArgumentException If the uploaded files are invalid.
     */
    public function withUploadedFiles(array $uploadedFiles): static
    {
        foreach ($uploadedFiles as $fieldName => $file) {
            if (!$this->isValidUploadedFile($file)) {
                throw new \InvalidArgumentException('Invalid structure for uploaded files.');
            }
        }

        // Create a new instance of the ServerRequest with the updated uploaded files.
        // In this example, we assume the existence of a constructor that accepts the uploaded files.

        $newRequest = clone $this;
        $newRequest->uploadedFiles = $uploadedFiles;

        return $newRequest;
    }
    /**
     * Check if a given value is a valid UploadedFileInterface instance.
     *
     * @param mixed $value
     * @return bool
     */
    private function isValidUploadedFile($value)
    {
        // Perform your validation logic here.
        // For example, check if $value is an instance of UploadedFileInterface.

        return ($value instanceof UploadedFileInterface);
    }
    /**
     * Retrieves the parsed body.
     *
     * @return null|array|object The parsed body.
     */
    public function getParsedBody(): null|array|object
    {
        return $this->parsedBody;
    }
    /**
     * Returns an instance with the specified parsed body.
     *
     * @param null|array|object $data The parsed body.
     * @return static
     * @throws \InvalidArgumentException If the parsed body is invalid.
     */
    public function withParsedBody($data): static
    {
        // Validate the provided data type.
        if ($data !== null && !is_array($data) && !is_object($data)) {
            throw new \InvalidArgumentException('Invalid data type provided for parsed body.');
        }

        // Clone the current instance to create a new instance with the updated parsed body.
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }
    /**
     * Retrieves the request attributes.
     *
     * @return array The request attributes.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    /**
     * Retrieves a specific request attribute.
     *
     * @param string $name    The attribute name.
     * @param mixed  $default The default value to return if the attribute is not found.
     * @return mixed The attribute value.
     */
    public function getAttribute($name, $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }
    /**
     * Returns an instance with the specified request attribute.
     *
     * @param string $name  The attribute name.
     * @param mixed  $value The attribute value.
     * @return static
     */
    public function withAttribute($name, $value): static
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }
    /**
     * Returns an instance without the specified request attribute.
     *
     * @param string $name The attribute name.
     * @return static
     */
    public function withoutAttribute($name): static
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }
    /**
     * Replaces the specified header with the provided values.
     *
     * @param string $headerName   The header name.
     * @param array  $headerValues The header values.
     * @return array The updated headers.
     */
    public function replaceHeader(string $headerName, array $headerValues): array
    {
        $normalizedHeaderName = strtolower($headerName);
        $headers = $this->getHeaders();

        unset($headers[$normalizedHeaderName]);

        foreach ($headerValues as $headerValue) {
            $headers[$normalizedHeaderName][] = $headerValue;
        }

        return $headers;
    }
}
