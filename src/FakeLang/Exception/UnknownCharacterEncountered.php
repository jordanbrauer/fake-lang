<?php

declare(strict_types = 1);

namespace FakeLang\Exception;

final class UnknownCharacterEncountered extends \RuntimeException
{
    public function __construct(string $char, \Exception $previous = null)
    {
        parent::__construct('Unknown character encountered: "'.$char.'"', 0, $previous);
    }
}