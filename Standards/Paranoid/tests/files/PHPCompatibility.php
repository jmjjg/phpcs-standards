<?php

/**
 *
 */

namespace ParanoidStandardTestsFiles;

/**
 * Class to check the Paranoid standard for rules takens from the PHPCompatibility
 * standard.
 */
class PHPCompatibilityStandardViolations
{
    public function baz($var)
    {
        // PHPCompatibility.PHP.ForbiddenCallTimePassByReference.NotAllowed
        $this->baz(&$var);

        // PHPCompatibility.PHP.DeprecatedFunctions
        $foo = split('a', $output);
    }

}
?>
<!-- PHPCompatibility.PHP.RemovedAlternativePHPTags.MaybeASPOpenTagFound -->
<%
date();
%>