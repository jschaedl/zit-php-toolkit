<?php

namespace ZitTest;

/*
 * Set error reporting to the level to which Es code must comply.
 */
error_reporting(E_ALL | E_STRICT);

/*
 * Determine the root, library and tests directories of the zit lib.
 */
$zitRoot = realpath(dirname(__DIR__));
$zitLibrary = "$zitRoot/library";
$zitTests = "$zitRoot/tests";

/*
 * Prepend the library/ and tests/ directories to the include_path. This allows the tests to run out of the box and helps prevent loading other copies of the framework code and tests that would supersede this copy.
 */
$path = array( 
        $zitLibrary, 
        $zitTests, 
        get_include_path() 
);
set_include_path(implode(PATH_SEPARATOR, $path));

/**
 * Setup autoloading
 */
include $zitTests . '/_autoload.php';
