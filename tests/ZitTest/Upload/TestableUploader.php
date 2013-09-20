<?php

namespace ZitTest\Upload;

use Zit\Upload\Uploader;

/**
 * This class is used to override some methods which
 * are not functional in cli environments.
 */
class TestableUploader extends Uploader
{

    protected function is_uploaded_file($filename) {
        return file_exists($filename);
    }

    protected function move_uploaded_file($filename, $destination) {
        return copy($filename, $destination);
    }
}
