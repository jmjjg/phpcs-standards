= CakePHP 2.x.x PHPCheckStyle Sniffs

- Classes/AppUses: checks that every extended class get an App::uses call for it.

== Tests

ant build

== To-do list

- Memory problems

=== Memory problems

```bash
vendors/bin/phpcs --standard=app/Contrib/phpcs/Cake2CodesnifferParanoid/ruleset.xml --report=checkstyle app
PHP Fatal error:  Allowed memory size of 134217728 bytes exhausted (tried to allocate 64 bytes) in vendors/squizlabs/php_codesniffer/CodeSniffer/File.php on line 2449
```

== @see

- https://pear.php.net/package/PHP_CodeSniffer/docs/2.7.0/