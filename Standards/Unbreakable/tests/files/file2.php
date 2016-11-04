<?php
namespace UnbreakableStandardTestsFiles;

/**
 *
 */
final class UnbreakableStandardViolationFile2Class1
{

    // Generic.CodeAnalysis.UnnecessaryFinalModifier.Found
    final public function foo()
    {

    }

}

/**
 *
 */
class UnbreakableStandardViolationFile2Class2
{

    // Generic.NamingConventions.ConstructorName.OldStyle
    public function UnbreakableStandardViolationFile2Class2()
    {
        // Generic.PHP.BacktickOperator.Found
        $output = `ls -al`;

        // Generic.PHP.ForbiddenFunctions.FoundWithAlternative
        $foo = sizeof(array());

        // PHPCompatibility.PHP.DeprecatedFunctions
        $foo = split('a', $output);
    }

}