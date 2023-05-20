<?php

namespace Effectra\Http\Message;

use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

/**
 * Represents an HTTP uploaded file conforming to the PSR-7 standard.
 */
class UploadedFile implements UploadedFileInterface
{

    /**
     * @var string The temporary file path
     */
    private $file;

    /**
     * @var int|null The file size in bytes
     */
    private $size;

    /**
     * @var int The file upload error code
     */
    private $error;

    /**
     * @var string|null The client-provided file name
     */
    private $name;

    /**
     * @var string|null The client-provided file media type
     */
    private $type;

    /**
     * @var StreamInterface|null The uploaded file stream
     */
    private $stream;

    /**
     * @var bool Indicates whether the file has been moved
     */
    private $moved = false;

    /**
     * UploadedFile constructor.
     *
     * @param string $file The temporary file path
     * @param int $size The file size in bytes
     * @param int $error The file upload error code
     * @param string|null $name The client-provided file name
     * @param string|null $type The client-provided file media type
     */
    public function __construct($file, $size, $error, $name = null, $type = null)
    {
        $this->file = $file;
        $this->size = $size;
        $this->error = $error;
        $this->name = $name;
        $this->type = $type;
    }
    /**
     * Retrieves the file size in bytes.
     *
     * @return int|null The file size in bytes or null if unknown
     */
    public function getSize()
    {
        return $this->size;
    }
    /**
     * Retrieves the file upload error code.
     *
     * @return int The file upload error code
     */
    public function getError()
    {
        return $this->error;
    }
    /**
     * Retrieves the client-provided file name.
     *
     * @return string|null The client-provided file name or null if not provided
     */
    public function getClientFilename()
    {
        return $this->name;
    }

    /**
     * Retrieves the client-provided media type of the file.
     *
     * @return string|null The client-provided media type of the file or null if not provided
     */
    public function getClientMediaType()
    {
        return $this->type;
    }
    /**
     * Retrieves a stream representing the uploaded file.
     *
     * @return StreamInterface The uploaded file stream
     * @throws RuntimeException If the file has been moved or the stream cannot be created
     */
    public function getStream()
    {
        if ($this->moved) {
            throw new RuntimeException('Stream is no longer available');
        }

        if ($this->stream === null) {
            $this->stream = new Stream($this->file);
        }

        return $this->stream;
    }
    /**
     * Moves the uploaded file to a new location.
     *
     * @param string $targetPath The target path to move the uploaded file to
     * @throws RuntimeException If the file has already been moved, is not an uploaded file, or an error occurs during the move operation
     */
    public function moveTo($targetPath)
    {
        if ($this->moved) {
            throw new RuntimeException('File has already been moved');
        }

        if (!is_uploaded_file($this->file)) {
            throw new RuntimeException('File was not uploaded via HTTP POST');
        }

        if (!move_uploaded_file($this->file, $targetPath)) {
            throw new RuntimeException('Error occurred while moving uploaded file');
        }

        $this->moved = true;
    }
    /**
     * Checks if the file has been moved.
     *
     * @return bool True if the file has been moved, false otherwise
     */
    public function isMoved()
    {
        return $this->moved;
    }
}
