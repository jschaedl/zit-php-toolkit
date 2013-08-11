Zit\Upload
==========

A simple php script to upload files. It offers some configuration options and is really lightweight.

## Usage

	use Zit\Upload\Uploader;

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

    // imagine you submitted a form with input[type=file] named 'file'
	$uploader->receive('file');


## Unit Tests

To run the included unit tests go to tests directory and run:

	phpunit --verbose [UploaderTest.php]


## Roadmap

* create getters for uploader configuration values
* localize error messages
* check directory existence and permissions
* check configured max_file_size if higher than php.ini max_file_size


