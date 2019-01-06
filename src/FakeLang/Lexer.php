<?php

declare(strict_types = 1);

namespace FakeLang;

final class Lexer
{
    const REGEX_WHITESPACE = '/\\s/';

    const REGEX_SYMBOL = '/[_a-zA-Z0-9]/';

    const HAYSTACK_OPERATORS = '+-*/%^';

    const HAYSTACK_STRINGS = '\'"';

    const ASSIGNMENT_OPERATOR = '=';

    const STATEMENT_TERMINATOR = ';';
    
    private $chars;
    
    public function __construct(string $input)
    {
        $input = \trim($input);
        
        $this->chars = new \ArrayIterator(
            (\mb_strlen($input)) ? \str_split($input) : []
        );
    }

    public function __toString(): string
    {
        return \implode('', (array) $this->chars);
    }

    public function characters(): \Generator
    {   
        do {
            $char = $this->chars->current();

            $this->chars->next();
            
            yield $char;
        } while ($this->chars->valid());
    }

    public function length(): int
    {
        return $this->chars->count();
    }

    public function position(): ?int
    {
        return $this->chars->key();
    }

    public function tokens(): \Generator
    {
        if ($this->length()) {
            if (null === $this->position()) {
                $this->chars->rewind();
            }
            
            foreach ($this->characters() as $char) {
                $tokenParameters = $this->scanToken($char);
                
                if (null === $tokenParameters) {
                    continue;
                } else if (empty($tokenParameters)) {
                    throw new Exception\UnknownCharacterEncountered($char);
                } else {
                    yield new Token(...$tokenParameters);
                }
            }
        }
    }

    private function isAssignmentOperator($char): bool
    {
        return self::ASSIGNMENT_OPERATOR == $char;
    }
    
    private function isMathematicalOperator($char): bool
    {
        return false !== \mb_stristr(self::HAYSTACK_OPERATORS, $char);
    }
    
    private function isNumeric($char): bool
    {
        return $char == '.' || \is_numeric($char);
    }

    private function isSymbolic($char): bool
    {
        return 1 === \preg_match(self::REGEX_SYMBOL, $char);
    }

    private function isStringDelimiter($char): bool
    {
        return false !== \mb_stristr(self::HAYSTACK_STRINGS, $char);
    }

    private function isWhitespace($char): bool
    {
        return 1 === \preg_match(self::REGEX_WHITESPACE, $char);
    }

    private function isStatementTerminator($char): bool
    {
        return self::STATEMENT_TERMINATOR == $char;
    }

    private function scan($leading, $chars, callable $callback = null)
    {
        $value = $leading;
        $isValid = function ($char) use ($callback) {
            if (null === $char) {
                throw new Exception\UnexpectedEndOfFile;
            }

            return (null === $callback) ? false : $callback($char);
        };

        while ($isValid($char = $chars->current())) {
            $value .= $char;

            $chars->next();
        }

        return $value;
    }
    
    private function scanString(string $delimiter, $chars): string
    {
        $string = $this->scan('', $chars, function ($char) use ($delimiter) {
            if (null === $char) {
                // NOTE: might be redundant because scan will throw EOF exception first..
                throw new Exception\UnexpectedUnescapedString($delimiter);
            }
            
            return $char != $delimiter;
        });

        $chars->next();

        if (null === $chars->current()) {
            throw new Exception\UnexpectedEndOfFile;
        }

        return $string;
    }

    private function scanToken($char)
    {
        if ($this->isWhitespace($char)) {
            $current = $this->position();
            
            if (!$current || (1 + $current) > $this->length()) {
                throw new Exception\UnexpectedEndOfFile;
            }
            
            return null;
        } else if ($this->isMathematicalOperator($char)) {
            return [
                Token::TYPE_OPERATOR, 
                $this->scan($char, $this->chars),
            ];
        } else if ($this->isAssignmentOperator($char)) {
            return [
                Token::TYPE_ASSIGNMENT, 
                $this->scan($char, $this->chars),
            ];
        } else if ($this->isStatementTerminator($char)) {
            return [
                Token::TYPE_END_OF_LINE, 
                $char,
            ];
        } else if ($this->isStringDelimiter($char)) {
            return [
                Token::TYPE_STRING, 
                $this->scanString($char, $this->chars),
            ];
        } else if ($this->isNumeric($char)) {
            return [
                Token::TYPE_NUMBER, 
                $this->scan($char, $this->chars, function ($char) {
                    return $this->isNumeric($char);
                }),
            ];
        } else if ($this->isSymbolic($char)) {
            return [
                Token::TYPE_SYMBOL,
                $this->scan($char, $this->chars, function ($char) {
                    return $this->isSymbolic($char);
                }),
            ];
        }

        return [];
    }
}
