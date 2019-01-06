<?php

declare(strict_types = 1);

namespace FakeLang;

final class Token
{
    const TYPE_OPERATOR = 'operator';

    const TYPE_SYMBOL = 'symbol';
    
    const TYPE_ASSIGNMENT = 'assignment';

    const TYPE_STRING = 'string';

    const TYPE_NUMBER = 'number';
    
    const TYPE_END_OF_LINE = 'eol';
    
    public $type;

    public $value;
    
    public function __construct(string $type, $value = null)
    {
        $this->type = $type;
        $this->value = $value;
    }
}