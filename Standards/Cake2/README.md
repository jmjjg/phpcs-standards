# CakePHP 2.x.x CodeSniffer Standards

## Cake2/Classes/AppUsesSniff.php

### Configuration

```xml
<ruleset name="...">
	<!-- ... -->
	<rule ref="Cake2.Classes.AppUses">
		<properties>
			<!-- Extra classes available in every file -->
			<property name="extraAvailable" type="array" value=""/>
			<!-- Extra known types -->
			<property name="extraTypes" type="array" value=""/>
		</properties>
	</rule>
	<!-- ... -->
</ruleset>
```

### Cake2.Classes.AppUses.MissingParentClass

This error is given for every class that extends a class that is not accessible,
either directly directly or through an App::uses call.

### Cake2.Classes.AppUses.AlreadyImported

This warning is given for every class that is imported more than once in the
file (with the same type and plugin).

### Cake2.Classes.AppUses.AlreadyImportedDifferent

This warning is given for every class that is imported more than once in the
file (with a different type or plugin).

### Cake2.Classes.AppUses.WrongType

This error is given for every unknown type used in an App::uses call.

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
