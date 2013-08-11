<?php

/**
 * Setup autoloading for all library classes
 */
function autoloadLibrary ($class) {
	$zitRoot = realpath(dirname(__DIR__));
	$zitLibrary = "$zitRoot/library";
    $class = $zitLibrary . '/' . str_replace('\\', '/', $class) . '.php';
	if (is_readable($class))
	{
		require_once($class);
	}
}
spl_autoload_register('autoloadLibrary');

?>