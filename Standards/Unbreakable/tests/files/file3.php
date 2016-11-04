<?php
/**
 *
 */

namespace UnbreakableStandardTestsFiles;

// PEAR.Files.IncludingFile.BracketsNotRequired
require( 'file1.php' );

// PEAR.Files.IncludingFile.UseRequire
include 'file1.php';

/**
 * Class to check the Unbreakable standard.
 */
class UnbreakableStandardViolationFile3Class1
{
    // PSR2.Classes.PropertyDeclaration.ScopeMissing
    var $var = 5;

    // Squiz.Scope.MethodScope.Missing
    function foo($x)
    {
        // Squiz.Strings.EchoedStrings.HasBracket
        echo($x);

        // PHPCompatibility.PHP.ForbiddenCallTimePassByReference.NotAllowed
        $this->foo(&$x);
    }

    // PSR2.Methods.MethodDeclaration.FinalAfterVisibility
    protected final function baz()
    {
    }
}

abstract class UnbreakableStandardViolationFile3Class2
{
    public static function foo()
    {
        // Squiz.Scope.StaticThisUsage.Found
        $foo = $this->foo();

        // Generic.PHP.NoSilencedErrors.Discouraged
        if (@$foo) {
            echo $foo;
        }

        // Generic.PHP.SAPIUsage.FunctionFound
        if (php_sapi_name() === 'cli') {
            echo "Hello, CLI user.";
        }
    }
}