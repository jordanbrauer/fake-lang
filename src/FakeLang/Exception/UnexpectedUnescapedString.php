<?php

declare(strict_types = 1);

namespace FakeLang\Exception;

final class UnexpectedUnescapedString extends \RuntimeException
{
    public function __construct(string $delimiter, \Exception $previous = null)
    {
        parent::__construct('Unexpected unescaped string! Missing closing `'.$delimiter.'`', 0, $previous);
    }
}