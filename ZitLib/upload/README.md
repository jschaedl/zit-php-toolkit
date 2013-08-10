ZitLib\Uploader
=================

A simple php script to upload files. It offers some configuration options and is realy lightweight.

## Usage

	require_once('Uploader.php');

	$uploader = new Uploader(array(
        'locale_code' => 'de'
        , 'upload_dir' => __DIR__ . '/_upload'
        , 'supported_filetypes' => array(
            'image/jpeg'
        )
        , 'allow_multiple_uploads' => false
        , 'allow_override_existing' => true
        , 'max_file_size' => '8M'
      )
	);

	$uploader->receive('file');


## Unit Tests

To run the included unit tests go to tst directory and run:

	phpunit --verbose [UploaderTest.php]

