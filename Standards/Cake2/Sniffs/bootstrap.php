<?php

/**
 * Basic CakePHP functionality adaptation from CakePHP 2.9.0 source code.
 *
 * @author Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @author Christian Buffin
 * @license https://opensource.org/licenses/MIT MIT License
 * @see http://cakephp.org
 */
define('ROOT', dirname(dirname(__FILE__)) . '/');

/**
 * CakePHP's debug basic function adaptation from CakePHP 2.9.0
 *
 * @param mixed $var Variable to show debug information for.
 * @param boolean $showHtml Whether to debug in HTML or console mode (null for auto).
 * @return void
 * @see http://book.cakephp.org/2.0/en/development/debugging.html#basic-debugging
 * @see http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#debug
 */
function debug($var, $showHtml = null)
{
    $lineInfo = '';

    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $file = str_replace(array(ROOT), '', $trace[0]['file']);
    $line = $trace[0]['line'];

    $html = <<<HTML
<div class="cake-debug-output">
%s
<pre class="cake-debug">
%s
</pre>
</div>
HTML;
    $text = <<<TEXT
%s
########## DEBUG ##########
%s
###########################

TEXT;

    $var = var_export($var, true);
    if (PHP_SAPI === 'cli' || false === $showHtml) {
        $template = $text;
        $lineInfo = sprintf('%s (line %s)', $file, $line);
    } elseif(false !== $showHtml) {
        $template = $html;
        $var = htmlentities($var);
        $lineInfo = sprintf('<span><strong>%s</strong> (line <strong>%s</strong>)</span>', $file, $line);
    }

    printf($template, $lineInfo, $var);
}

/**
 * CakePHP's pluginSplit basic function adaptation from 2.9.0
 *
 * @param string $name The name you want to plugin split.
 * @param string $plugin Optional default plugin to use if no plugin is found. Defaults to null.
 * @return array Array with 2 indexes. 0 => plugin name, 1 => class name
 * @see http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#pluginSplit
 */
function pluginSplit($name, $plugin = null)
{
    if (false !== strpos($name, '.')) {
        $parts = explode('.', $name, 2);
        return $parts;
    }
    return array($plugin, $name);
}
