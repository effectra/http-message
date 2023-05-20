<?php

namespace Effectra\Http\Message;

use Psr\Http\Message\StreamInterface;

/**
 * Represents an HTTP stream conforming to the PSR-7 standard.
 */
class Stream implements StreamInterface
{
    /** @var resource|string|null The underlying resource or string */
    protected $resource;

    /** @var int|null The size of the stream, if known */
    protected $size;

    /**
     * Stream constructor.
     *
     * @param resource|string|null $resource The underlying resource or string
     * @param int|null $size The size of the stream, if known
     */
    public function __construct($resource, $size = null)
    {
        $this->resource = $resource;
        $this->size = $size;
    }
    /**
     * Converts the stream to a string.
     *
     * @return string The string representation of the stream
     */

    public function __toString(): string
    {
        if (is_string($this->resource)) {
            return $this->resource;
        }
        if (!$this->isReadable()) {
            return '';
        }
        try {
            $this->rewind();
            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }
    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */

    public function close(): void
    {
        if (is_resource($this->resource)) {
            fclose($this->resource);
        }
        $this->detach();
    }
    /**
     * Detaches the stream from the underlying resource, if any.
     *
     * @return mixed|null The underlying resource, if any
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        $this->size = null;
        return $resource;
    }
    /**
     * Retrieves the size of the stream, if known.
     *
     * @return int|null The size of the stream, if known, or null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }
    /**
     * Returns the current position of the stream.
     *
     * @return int The current position of the stream
     * @throws \RuntimeException if the position cannot be determined
     */
    public function tell(): int
    {
        $position = ftell($this->resource);
        if ($position === false) {
            throw new \RuntimeException('Error occurred while telling the position of the stream');
        }
        return $position;
    }
    /**
     * Checks if the stream pointer is at the end of the stream.
     *
     * @return bool True if the stream pointer is at the end, false otherwise
     */
    public function eof(): bool
    {
        return !$this->resource || feof($this->resource);
    }
    /**
     * Checks if the stream is seekable.
     *
     * @return bool True if the stream is seekable, false otherwise
     */
    public function isSeekable(): bool
    {
        return $this->resource && $this->getMetadata('seekable');
    }

    /**
     * Seeks to a specific position in the stream.
     *
     * @param int $offset The stream offset
     * @param int $whence Specifies how the cursor position will be calculated based on the offset
     * @throws \RuntimeException if the stream is not seekable or an error occurs while seeking
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Stream is not seekable');
        }

        if (fseek($this->resource, $offset, $whence) === -1) {
            throw new \RuntimeException('Error seeking within the stream');
        }
    }
    /**
     * Rewinds the stream to the beginning.
     *
     * @throws \RuntimeException if the stream is not seekable or an error occurs while rewinding
     */
    public function rewind(): void
    {
        $this->seek(0);
    }
    /**
     * Checks if the stream is writable.
     *
     * @return bool True if the stream is writable, false otherwise
     */
    public function isWritable(): bool
    {
        $mode = $this->getMetadata('mode');
        return strpos($mode, 'w') !== false || strpos($mode, 'a') !== false || strpos($mode, 'x') !== false || strpos($mode, '+') !== false;
    }
    /**
     * Writes data to the stream.
     *
     * @param string $string The string to write
     * @return int The number of bytes written
     * @throws \RuntimeException if the stream is not writable or an error occurs while writing
     */
    public function write(string $string): int
    {
        if (!$this->isWritable()) {
            throw new \RuntimeException('Cannot write to a non-writable stream');
        }

        $written = fwrite($this->resource, $string);

        if ($written === false) {
            throw new \RuntimeException('Error writing to stream');
        }

        $this->size = null;

        return $written;
    }
    /**
     * Checks if the stream is readable.
     *
     * @return bool True if the stream is readable, false otherwise
     */
    public function isReadable(): bool
    {
        $mode = $this->getMetadata('mode');
        return strpos($mode, 'r') !== false || strpos($mode, '+') !== false;
    }
    /**
     * Reads data from the stream.
     *
     * @param int $length The number of bytes to read
     * @return string The data read from the stream
     * @throws \RuntimeException if the stream is not readable or an error occurs while reading
     */
    public function read(int $length): string
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Cannot read from a non-readable stream');
        }

        return fread($this->resource, $length);
    }

    /**
     * Retrieves the remaining contents of the stream.
     *
     * @return string The contents of the stream
     * @throws \RuntimeException if the stream is not readable or an error occurs while reading
     */
    public function getContents(): string
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Cannot read from a non-readable stream');
        }

        $contents = stream_get_contents($this->resource);
        if ($contents === false) {
            throw new \RuntimeException('Error reading from stream');
        }

        return $contents;
    }
    /**
     * Retrieves metadata of the stream or a specific metadata value.
     *
     * @param string|null $key The metadata key to retrieve, or null to retrieve all metadata
     * @return mixed|null The metadata value, or null if the key is not found
     */
    public function getMetadata(?string $key = null)
    {
        $meta = stream_get_meta_data($this->resource);

        if ($key === null) {
            return $meta;
        }

        return isset($meta[$key]) ? $meta[$key] : null;
    }
}
