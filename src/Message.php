<?php

namespace Effectra\Http\Message;

use Psr\Http\Message\MessageInterface;

/**
 * Represents an HTTP message conforming to the PSR-7 standard.
 */
class Message implements MessageInterface
{
    use MessageTrait;
}