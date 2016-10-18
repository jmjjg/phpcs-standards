= CakePHP 2.x.x PHPCheckStyle Sniffs

- Classes/AppUses: checks that every extended class get an App::uses call for it.

== Tests

ant -f app/Contrib/phpcs/cake2/build.xml

== To-do list

- rename to cake2-codesniffer-paranoid (?)

=== Memory problems

```bash
vendors/bin/phpcs --standard=app/Contrib/phpcs/cake2/ruleset.xml --report=checkstyle app
PHP Fatal error:  Allowed memory size of 134217728 bytes exhausted (tried to allocate 64 bytes) in vendors/squizlabs/php_codesniffer/CodeSniffer/File.php on line 2449
```

== @see

- https://pear.php.net/package/PHP_CodeSniffer/docs/2.7.0/