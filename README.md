# fake-lang

This project is purely for my own educational purposes. As programmers we often think of languages as magic, so I wanted to demystify that for myself.

## Tokenization

Take some FakeLang script,

```
foo = 'bar';
```

Pass it to the lexer,

```php
$input = file_get_contents('script.fl'); # the name of our FakeLing file
$lexer = new \FakeLang\Lexer($input);

foreach ($lexer->tokens() as $token) {
    dump($token);
}
```

Would yeild the following output:

```
FakeLang\Token {#13
  +type: "symbol"
  +value: "foo"
}
FakeLang\Token {#17
  +type: "assignment"
  +value: "="
}
FakeLang\Token {#19
  +type: "string"
  +value: "bar"
}
FakeLang\Token {#20
  +type: "eol"
  +value: ";"
}
```
