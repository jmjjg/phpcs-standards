<?php
/**
 * Class to check the ParanoidCodeSnifferStandard CodeSniffer standard.
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
     */
    function foo()
    {
        // Generic.Formatting.NoSpaceAfterCast.SpaceFound
        $x = (array) NULL;
        // Generic.CodeAnalysis
        if ($x === 5) {

        }
        // Squiz.Strings.EchoedStrings.HasBracket
        echo($x);
        // Squiz.Commenting.FunctionCommentThrowTag
        throw new RuntimeException();
        // PHPCompatibility.PHP.ForbiddenCallTimePassByReference.NotAllowed
        $this->foo(&$x);
    }
}
