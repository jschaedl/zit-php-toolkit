<?php

namespace ZitTest\Upload;

use ZitTest;
use ZitTest\Upload\TestableUploader;

class UploaderTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp() {
        // create a dummy file object
        $_FILES = array( 
                'file' => array( 
                        'name' => 'test.jpg', 
                        'type' => 'image/jpeg', 
                        'size' => 175132, 
                        'tmp_name' => __DIR__ . '/_temp/source-test.jpg', 
                        'error' => 0 
                ) 
        );
        
        $this->object = new TestableUploader(array( 
                'locale_code' => 'de', 
                'upload_dir' => __DIR__ . '/_temp', 
                'supported_filetypes' => array( 
                        'image/jpeg' 
                ), 
                'allow_multiple_uploads' => false, 
                'allow_override_existing' => true, 
                'max_file_size' => '8M' 
        ));
    }

    protected function tearDown() {
        unset($_FILES);
        unset($this->object);
    }

    /**
     * @covers \Zit\Upload\Uploader::receive
     * 
     * @todo Implement testReceive().
     */
    public function testReceive() {
        $this->assertTrue($this->object->receive('file'));
        @unlink(__DIR__ . '/_temp/test.jpg');
    }

    /**
     * @covers \Zit\Upload\Uploader::receive
     * 
     * @todo Implement testReceive().
     */
    public function testReceiveCustomFilename() {
        if ($this->object->receive('file', 'testImage')) {
            $this->assertTrue(file_exists(__DIR__ . '/_temp/testImage.jpg'));
            @unlink(__DIR__ . '/_temp/testImage.jpg');
        }
    }
}
