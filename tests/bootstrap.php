<?php

namespace ZitLib;

/*
 * Set error reporting to the level to which Es code must comply.
 */

error_reporting(E_ALL | E_STRICT);


/*
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */

chdir(dirname(__DIR__));
require('./ZitLib/upload/Uploader.php');


function is_uploaded_file($filename)
{
    return file_exists($filename);
}

function move_uploaded_file($filename, $destination)
{
    return copy($filename, $destination);
}
