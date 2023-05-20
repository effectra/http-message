<?php

namespace Effectra\Http\Message;

use Psr\Http\Message\UriInterface;

/**
 * Represents an HTTP URI conforming to the PSR-7 standard.
 */
class Uri implements UriInterface
{
    /**
     * @var string The scheme component of the URI
     */
    private $scheme;

    /**
     * @var string The user information component of the URI
     */
    private $userInfo;

    /**
     * @var string The host component of the URI
     */
    private $host;

    /**
     * @var int|null The port component of the URI
     */
    private $port;

    /**
     * @var string The path component of the URI
     */
    private $path;

    /**
     * @var string The query component of the URI
     */
    private $query;

    /**
     * @var string The fragment component of the URI
     */
    private $fragment;

    /**
     * Uri constructor.
     *
     * @param string $uri The URI string
     */

    public function __construct($uri = '')
    {
        $parts = parse_url($uri);

        $this->scheme = isset($parts['scheme']) ? $parts['scheme'] : '';
        $this->userInfo = isset($parts['user']) ? $parts['user'] : '';
        $this->userInfo .= isset($parts['pass']) ? ':' . $parts['pass'] : '';
        $this->host = isset($parts['host']) ? $parts['host'] : '';
        $this->port = isset($parts['port']) ? $parts['port'] : null;
        $this->path = isset($parts['path']) ? $parts['path'] : '';
        $this->query = isset($parts['query']) ? $parts['query'] : '';
        $this->fragment = isset($parts['fragment']) ? $parts['fragment'] : '';
    }

    /**
     * Retrieves the scheme component of the URI.
     *
     * @return string The scheme component of the URI
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Retrieves the authority component of the URI.
     *
     * @return string The authority component of the URI
     */
    public function getAuthority(): string
    {
        $authority = $this->host;
        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }
        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }
        return $authority;
    }
    /**
     * Retrieves the user information component of the URI.
     *
     * @return string The user information component of the URI
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * Retrieves the host component of the URI.
     *
     * @return string The host component of the URI
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Retrieves the port component of the URI.
     *
     * @return int|null The port component of the URI
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * Retrieves the path component of the URI.
     *
     * @return string The path component of the URI
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Retrieves the query component of the URI.
     *
     * @return string The query component of the URI
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Retrieves the fragment component of the URI.
     *
     * @return string The fragment component of the URI
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * Returns an instance with the specified scheme.
     *
     * @param string $scheme The scheme to use
     * @return self A new instance with the specified scheme
     */
    public function withScheme($scheme): self
    {
        $new = clone $this;
        $new->scheme = strtolower($scheme);
        return $new;
    }
    /**
     * Returns an instance with the specified user information.
     *
     * @param string $user The user name to use
     * @param string|null $password The password associated with the user name, if any
     * @return self A new instance with the specified user information
     */
    public function withUserInfo($user, $password = null): self
    {
        $new = clone $this;
        $new->userInfo = $user;
        if ($password !== null) {
            $new->userInfo .= ':' . $password;
        }
        return $new;
    }
    /**
     * Returns an instance with the specified host.
     *
     * @param string $host The host to use
     * @return self A new instance with the specified host
     */
    public function withHost($host): self
    {
        $new = clone $this;
        $new->host = strtolower($host);
        return $new;
    }
    /**
     * Returns an instance with the specified port.
     *
     * @param int|null $port The port to use
     * @return self A new instance with the specified port
     */
    public function withPort($port): self
    {
        $new = clone $this;
        $new->port = $port;
        return $new;
    }
    /**
     * Returns an instance with the specified path.
     *
     * @param string $path The path to use
     * @return self A new instance with the specified path
     */
    public function withPath($path): self
    {
        $new = clone $this;
        $new->path = $path;
        return $new;
    }
    /**
     * Returns an instance with the specified query.
     *
     * @param string $query The query to use
     * @return self A new instance with the specified query
     */
    public function withQuery($query): self
    {
        $new = clone $this;
        $new->query = $query;
        return $new;
    }
    /**
     * Returns an instance with the specified fragment.
     *
     * @param string $fragment The fragment to use
     * @return self A new instance with the specified fragment
     */
    public function withFragment($fragment): self
    {
        $new = clone $this;
        $new->fragment = $fragment;
        return $new;
    }

    /**
     * Returns the string representation of the URI.
     *
     * @return string The string representation of the URI
     */
    public function __toString(): string
    {
        $uri = '';

        if ($this->scheme !== '') {
            $uri .= $this->scheme . ':';
        }

        if ($this->getAuthority() !== '') {
            $uri .= '//' . $this->getAuthority();
        }

        $uri .= $this->getPath();

        if ($this->getQuery() !== '') {
            $uri .= '?' . $this->getQuery();
        }

        if ($this->getFragment() !== '') {
            $uri .= '#' . $this->getFragment();
        }

        return $uri;
    }
}
