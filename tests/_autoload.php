<?php

$zitRoot = realpath(dirname(__DIR__));
$zitLibrary = "$zitRoot/library";
$zitTests = "$zitRoot/tests";

/**
 * Setup autoloading for all library classes
 */
include_once $zitLibrary . '/autoload.php';

/**
 * Setup autoloading for all library classes
 */
function autoloadTests($class) {
    $zitRoot = realpath(dirname(__DIR__));
    $zitTests = "$zitRoot/tests";
    $class = $zitTests . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_readable($class)) {
        require_once ($class);
    }
}

spl_autoload_register('autoloadTests');
