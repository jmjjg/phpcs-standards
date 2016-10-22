<?php
/**
 *
 */
namespace ParanoidCheckstyleStandard;

/**
 * Class to check the ParanoidStandard CodeSniffer standard.
 */
class Foo
{

    /**
     * PSR2.Classes.PropertyDeclaration.VarUsed
     * PSR2.Classes.PropertyDeclaration.ScopeMissing
     * Squiz.Scope.MemberVarScope.Missing
     * Generic.PHP.UpperCaseConstant.Found
     */
    var $x = null;

    /**
     * Let's have some errors detected.
     *
     * Squiz.Scope.MethodScope.Missing
     * Generic.CodeAnalysis.UnusedFunctionParameter
     */
    function foo($bar = array()) {
        // Generic.Formatting.NoSpaceAfterCast.SpaceFound
        $x = (array) NULL;

        // Generic.CodeAnalysis.EmptyStatement
        if ($x === 5) {

        }

        // Generic.CodeAnalysis.ForLoopShouldBeWhileLoop
        for (; $x > 5;) {

        }

        // Generic.CodeAnalysis.ForLoopWithTestFunctionCall
        $a = array(1, 2, 3, 4);
        for ($i = 0; $i < count($a); $i++) {
            $a[$i] *= $i;
        }

        // Generic.CodeAnalysis.JumbledIncrementer
        for ($i = 0; $i < 10; $i++) {
            for ($k = 0; $k < 20; $i++) {
                echo 'Hello';
            }
        }

        // Generic.CodeAnalysis.UnconditionalIfStatement
        if (true) {

        }

        // Squiz.Strings.EchoedStrings.HasBracket
        echo($x);

        // Squiz.Commenting.FunctionCommentThrowTag
        throw new RuntimeException();

        // PHPCompatibility.PHP.ForbiddenCallTimePassByReference.NotAllowed
        $this->foo(&$x);
    }

}

final class Bar
{

    // Generic.CodeAnalysis.UnnecessaryFinalModifier
    final public function bar() {

    }

    // Generic.CodeAnalysis.UselessOverridingMethod
    public function __construct($foo, $bar) {
        parent::__construct($foo, $bar);
    }

}
