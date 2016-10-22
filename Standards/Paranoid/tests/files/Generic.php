<?php

/**
 *
 */

namespace ParanoidStandardTestsFiles;

/**
 * Class to check the Paranoid standard for rules takens from the Generic
 * standard.
 */
class GenericStandardViolations
{

    /**
     *
     * @param boolean $var
     */
    public function doSomething($var)
    {
        // Generic.CodeAnalysis.EmptyStatement.DetectedIF
        if ($var) {

        }

        // Generic.CodeAnalysis.ForLoopShouldBeWhileLoop.CanSimplify
        $test = true;
        for (; $test;) {
            $test = $this->doSomething($var);
        }

        // Generic.CodeAnalysis.ForLoopWithTestFunctionCall.NotAllowed
        $foo = array(1, 2);
        for ($i = 0; $i < count($foo); $i++) {
            echo $foo[$i] . "\n";
        }

        // Generic.CodeAnalysis.JumbledIncrementer.Found
        for ($i = 0; $i < 10; $i++) {
            for ($j = 0; $j < 10; $i++) {
                $test = $this->doSomething($var);
            }
        }

        // Generic.CodeAnalysis.UnconditionalIfStatement.Found
        if (true) {
            $var = 1;
        }
    }

}

/**
 *
 */
final class FinalGenericStandardViolations
{

    // Generic.CodeAnalysis.UnnecessaryFinalModifier.Found
    public final function foo()
    {

    }

    // Generic.CodeAnalysis.UnusedFunctionParameter.Found
    public function addThree($a, $b, $c)
    {
        return $a + $b;
    }

    // Generic.CodeAnalysis.UselessOverridingMethod.Found
    public function bar()
    {
        return parent::bar();
    }

    public function baz($var)
    {
        // Generic.ControlStructures.InlineControlStructure.NotAllowed
        if (true === $var)
            echo $var;
    }

}

/**
 *
 */
class Foo
{

    // Generic.NamingConventions.ConstructorName.OldStyle
    public function Foo()
    {
        // Generic.PHP.BacktickOperator.Found
        $output = `ls -al`;
    }

}
?>
<!-- Generic.PHP.DisallowShortOpenTag -->
<?
?>
<?php
// Generic.PHP.ForbiddenFunctions.FoundWithAlternative
$foo = sizeof($bar);

// Generic.PHP.NoSilencedErrors.Discouraged
if (@$foo) {
    echo $foo;
}

// Generic.PHP.SAPIUsage.FunctionFound
if (php_sapi_name() === 'cli') {
    echo "Hello, CLI user.";
}

// Generic.Strings.UnnecessaryStringConcat.Found
echo 'Hello ' . 'World';