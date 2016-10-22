<?php

/**
 *
 */

namespace ParanoidStandardTestsFiles;

/**
 * Class to check the Paranoid standard for rules takens from the Squiz
 * standard.
 */
class SquizStandardViolations
{
    // Squiz.Scope.MemberVarScope.Missing
    var $var = 5;

    // Squiz.Scope.MethodScope.Missing
    function foo()
    {
    }

    /**
     * Squiz.Commenting.FunctionCommentThrowTag.Missing
     */
    protected function bar()
    {
        throw new \RuntimeException();
    }
}