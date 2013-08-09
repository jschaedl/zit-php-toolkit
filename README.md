php-uploadmanager
=================

A simple php script to upload image files to a remote server.

## Usage

	require_once('Zit_Misc_Upload_Manager.php');

	$uploadManger = new Zit_Misc_Upload_Manager(array(
		'supported_filetypes' => array(
			'image/jpeg'
		)
		, 'image_min_width' => 480
		, 'image_min_height' => 640
		, 'image_quality' => 75
		, 'keep_original' => false
	);

	$filename = $uploadManger->upload(
        $_FILES['file']
        , 'images'
    );

