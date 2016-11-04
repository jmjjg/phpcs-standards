# "Unbreakable" CodeSniffer Standards

The goal of this standard is to provide robust and clean PHP5+ OOP rules and
guidelines, regardless of whitespace, commenting or other character placement.

## Rationale

### What you cannot control

- the production server configuration
  * Generic.PHP.DisallowShortOpenTag.EchoFound
  * PHPCompatibility.PHP.RemovedAlternativePHPTags.MaybeASPOpenTagFound
- errors, so don't silence them
  * Generic.PHP.NoSilencedErrors.Discouraged

### PHP 5+

- cleaner PHP Object Oriented Programming
  * PSR2.Classes.PropertyDeclaration.ScopeMissing
  * Squiz.Scope.MethodScope.Missing
  * Generic.NamingConventions.ConstructorName.OldStyle
  * Generic.CodeAnalysis.UnnecessaryFinalModifier.Found
  * PSR2.Methods.MethodDeclaration.FinalAfterVisibility
  * Squiz.Scope.StaticThisUsage.Found
- cleaner PHP programming
  * Generic.PHP.ForbiddenFunctions.FoundWithAlternative
  * PHPCompatibility.PHP.DeprecatedFunctions
  * PEAR.Files.IncludingFile.BracketsNotRequired
  * PEAR.Files.IncludingFile.UseRequire
  * Squiz.Strings.EchoedStrings.HasBracket
  * PHPCompatibility.PHP.ForbiddenCallTimePassByReference.NotAllowed

### Various

- Generic.PHP.BacktickOperator.Found
- Generic.PHP.SAPIUsage.FunctionFound

## @see

- https://github.com/FierceMarkets/CodingStandards/blob/master/php-coding-standards.md