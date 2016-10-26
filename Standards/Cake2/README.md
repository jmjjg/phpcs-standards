# CakePHP 2.x.x CodeSniffer Standards

## Cake2/Classes/AppUsesSniff.php

### Cake2.Classes.AppUses.MissingParentClass

Every class in a CakePHP 2.x file should have access to the parent class, either
directly or through an App::uses call.

### Cake2.Classes.AppUses.AlreadyImported

Checks if the class has already been imported in the file (type-independant).

### Cake2.Classes.AppUses.WrongType

Every type used in an App::uses call is correct by checking it on a list.

## Build targets

```bash
ant build
```

```bash
ant quality
```

## Info

### Memory problems

```bash
vendors/bin/phpcs --standard=app/Contrib/phpcs/Cake2/ruleset.xml --report=checkstyle app
PHP Fatal error:  Allowed memory size of 134217728 bytes exhausted (tried to allocate 64 bytes) in vendors/squizlabs/php_codesniffer/CodeSniffer/File.php on line 2449
vendors/bin/phpcs -d memory_limit=512M --standard=app/Contrib/phpcs/Cake2/ruleset.xml --report=checkstyle app
```

## To-do list

## @see

- https://pear.php.net/package/PHP_CodeSniffer/docs/2.7.0/
