php-uploadmanager
=================

A simple php script to upload files to remote server.

## Usage


	$uploadManger = new Zit_Misc_Upload_Manager(array(
		'supported_filetypes' => array(
			'image/jpeg'
		)
		, 'image_min_width' => 480
		, 'image_min_height' => 640
		, 'image_quality' => 75
		, 'keep_original' => false
	);

