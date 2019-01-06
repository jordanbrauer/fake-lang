<?php

declare(strict_types = 1);

namespace FakeLang\Exception;

final class UnexpectedEndOfFile extends \RuntimeException
{
    public function __construct(\Exception $previous = null)
    {
        parent::__construct('Lexical error: unexpected end of file', 0, $previous);
    }
}