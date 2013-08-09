<?php

/**
 * This is a simple class for uploading image files.
 * 
 * @author Jan Schädlich <mail@janschaedlich.de>
 */
class Zit_Misc_Upload_Manager 
{
    protected $supportedFileTypes; // default: image/jpeg

    protected $imageMinWidth; // default: 480
    
    protected $imageMinHeight; // default: 640
    
    protected $imageQuality; // default: 75
    
    protected $keepOriginal; // default: false
    

    function __construct($uploadConfig=array())
    {
        $this->supportedFileTypes = isset(
            $uploadConfig['supported_filetypes']) 
                ? $uploadConfig['supported_filetypes'] 
                : array('image/jpeg');

        $this->imageMinWidth = isset(
            $uploadConfig['image_min_width']) 
                ? $uploadConfig['image_min_width']
                : 480;

        $this->imageMinHeight = isset(
            $uploadConfig['image_min_height'])
                ? $uploadConfig['image_min_height']
                : 640;

        $this->imageQuality = isset(
            $uploadConfig['image_quality'])
                ? $uploadConfig['image_quality']
                : 75;

        $this->keepOriginal = isset(
            $uploadConfig['keep_original'])
                ? $uploadConfig['keep_original']
                : false;
    }


    /**
     * Use this method to upload a file already handled by PHP. This method provides 
     * a clear interface to process an uploaded file from PHPs $_FILES array. 
     *
     * @param PHPs $_FILES['file'] which was uploaded
     *
     * @param Path to save the file
     *
     * @param A filename to use for saving the uploaded file. If the filename is empty, 
     * the filename is substracted from $_FILES['file']['name']and used in a 
     * normalized fashion
     *
     * @return The generated filename
     *
     * @throws Exception
     */
    public function upload($uploadedFile, $filePath,  $customFilename='')
    {
        // check if uploaded file is empty
        if(!isset($uploadedFile)) 
            throw new Exception('File is empty!');
        
        // check for errors on upload
        if($uploadedFile['error']) 
            throw new Exception($this->parseError($uploadedFile['error']));

        // check supported filetype
        if (!$this->checkFileType($uploadedFile['type']))
            throw new Exception('Unsupported FileType!');

        $file_temp = $uploadedFile['tmp_name'];
        $file_name = strtolower($uploadedFile['name']);

        if (isset($customFilename) && $customFilename != '' ) 
            $file_name_normalized = $customFilename;
        else
            $file_name_normalized = substr($file_name, 0, strpos($file_name, '.'));
            
        // TODO: extension could be blown away, especially on mac osx systems
        $file_ext_normalized = substr($file_name, strrpos($file_name, '.') + 1);

        $image_info = getimagesize($file_temp);
        $file_width_origin = $image_info[0];
        $file_height_origin = $image_info[1];
        $file_type = $image_info['mime'];
        

        // check supported filetype
        if (!$this->checkFileType($file_type))
            throw new Exception('Unsupported FileType!');

        // check for imageWidth and imageHeight
        if (!$this->checkDimension($file_width_origin, $file_height_origin))
            throw new Exception('Wrong dimension!');


        $filepath_orig = $filePath .  '/' . $file_name_normalized . '_orig_' 
            . $file_width_origin . 'x' . $file_height_origin . '.' . $file_ext_normalized;

        
        if (move_uploaded_file($file_temp, $filepath_orig)) 
        {
            chmod($filepath_orig, 0775);

            $filepath_big = $filePath . '/' . $file_name_normalized . '.' . $file_ext_normalized;
            $file_status = $this->resizeImage($filepath_orig, $filepath_big, $this->imageMinWidth, -1);

            $image_info = getimagesize($filepath_big);
            $file_height_big = $image_info[1];
            
            if ($file_height_big < 640) 
            {
                $file_status = $this->resizeImage( $filepath_orig, $filepath_big, $this->imageMinHeight, -2 );
            }
            
            if (!$file_status) 
            {
                throw new Exception('Error uploading File!');
            } 
            else 
            {
                if (!$this->keepOriginal) 
                {
                    unlink($filepath_orig);
                }

                return $file_name_normalized . '.' . $file_ext_normalized;
            }   
        }
        else 
        { 
            throw new Exception('Error uploading File!');
        }
    }

    private function checkFileType($fileType)
    {
        foreach ($this->supportedFileTypes as $supportedFileType)
        {
            if (strtolower($fileType) === $supportedFileType)
            {
                return true;
            }
        }

        return false;
    }

    private function checkDimension($widthOrigin, $heightOrigin)
    {
        if ($widthOrigin < $this->imageMinWidth)
        {
            return false;
        }
        else if ($heightOrigin < $this->imageMinHeight)
        {
            return false;
        }

        return true;
    }


    private function parseError($errorCode)
    {
        switch ($errorCode) 
        { 
            case UPLOAD_ERR_INI_SIZE: 
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini'; 
            case UPLOAD_ERR_FORM_SIZE: 
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'; 
            case UPLOAD_ERR_PARTIAL: 
                return 'The uploaded file was only partially uploaded'; 
            case UPLOAD_ERR_NO_FILE: 
                return 'No file was uploaded'; 
            case UPLOAD_ERR_NO_TMP_DIR: 
                return 'Missing a temporary folder'; 
            case UPLOAD_ERR_CANT_WRITE: 
                return 'Failed to write file to disk'; 
            case UPLOAD_ERR_EXTENSION: 
                return 'File upload stopped by extension'; 
            default: 
                return 'Unknown upload error'; 
        }
    }

    /**
     * Der Pfad, unter dem das Bild zu finden ist, welches als Vorlage dienen soll, wird mit $filepath_old übergeben, der Pfad, 
     * unter dem das neue, verkleinerte resp. vergrößerte Bild gespeichert werden soll, wird mit $filepath_new übergeben. 
     * Mit $image_dimension wird ein ganzzahliger Pixelwert übergeben, dessen Bedeutung erst durch $scale_mode definiert wird. 
     * Die möglichen Werte für diesen Parameter sind:
     * 
     * -1:  $image_dimension wird als neue Breite des Bildes aufgefasst; 
     *      die Höhe wird so angepasst, dass das Seitenverhältnis des Bildes 
     *      erhalten bleibt. Ideal, wenn das Bild in eine Spalte mit fester 
     *      Breite eingefügt werden soll. 
     * -2:  $image_dimension wird als neue Höhe des Bildes aufgefasst; 
     *      die Breite wird so angepasst, dass das Seitenverhältnis des Bildes 
     *      erhalten bleibt. Ideal, wenn das Bild in eine Zeile mit fester Höhe 
     *      eingefügt werden soll. 
     *  0:  [Standardwert] $image_dimension wird als neue längste Seite 
     *      des Bildes aufgefasst. Die andere Seite wird entsprechend verkleinert, 
     *      damit das Seitenverhältnis des Bildes erhalten bleibt. Ideal, 
     *      wenn das Bild in eine quadratische Box mit fester Größe 
     *      eingepasst werden soll (typisch für eine Thumbnail-Übersicht). 
     *  1:  $image_dimension wird als neue kürzeste Seite des Bildes aufgefasst. 
     *      Die andere Seite wird entsprechend vergrößert, 
     *      damit das Seitenverhältnis des Bildes erhalten bleibt. 
     *      Ideal, wenn das Bild eine Mindestgröße nicht unterschreiten soll.
     */
    private function resizeImage( $filepath_old, $filepath_new, $image_dimension, $scale_mode = 0 ) 
    {   
        if (!(file_exists($filepath_old))/* || file_exists($filepath_new)*/) 
            return false; 

        $image_attributes = getimagesize($filepath_old); 
        
        $image_width_old    = $image_attributes[0]; 
        $image_height_old   = $image_attributes[1]; 
        $image_filetype     = $image_attributes[2]; 

        if ($image_width_old <= 0 || $image_height_old <= 0) 
            return false; 
      
        $image_aspectratio = $image_width_old / $image_height_old; 

        if ($scale_mode == 0) 
        { 
            $scale_mode = ($image_aspectratio > 1 ? -1 : -2); 
        } 
        elseif ($scale_mode == 1) 
        { 
            $scale_mode = ($image_aspectratio > 1 ? -2 : -1); 
        } 

        if ($scale_mode == -1) 
        { 
            $image_width_new    = $image_dimension; 
            $image_height_new   = round($image_dimension / $image_aspectratio); 
        } 
        elseif ($scale_mode == -2) 
        { 
            $image_height_new   = $image_dimension; 
            $image_width_new    = round($image_dimension * $image_aspectratio); 
        } 
        else 
        { 
            return false; 
        } 

        switch ($image_filetype) 
        { 
            case 1: 
                $image_old = imagecreatefromgif($filepath_old); 
                $image_new = imagecreate($image_width_new, $image_height_new); 
                imagecopyresampled($image_new, $image_old, 0, 0, 0, 0, $image_width_new, $image_height_new, $image_width_old, $image_height_old); 
                imagegif($image_new, $filepath_new); 
                break; 
      
            case 2: 
                $image_old = imagecreatefromjpeg($filepath_old); 
                $image_new = imagecreatetruecolor($image_width_new, $image_height_new); 
                imagecopyresampled($image_new, $image_old, 0, 0, 0, 0, $image_width_new, $image_height_new, $image_width_old, $image_height_old); 
                imagejpeg($image_new, $filepath_new); 
                break; 

            case 3: 
                $image_old = imagecreatefrompng($filepath_old); 
                $image_colordepth = imagecolorstotal($image_old); 

                if ($image_colordepth == 0 || $image_colordepth > 255) 
                { 
                    $image_new = imagecreatetruecolor($image_width_new, $image_height_new); 
                } else 
                { 
                    $image_new = imagecreate($image_width_new, $image_height_new); 
                } 

                imagealphablending($image_new, false); 
                imagecopyresampled($image_new, $image_old, 0, 0, 0, 0, $image_width_new, $image_height_new, $image_width_old, $image_height_old); 
                imagesavealpha($image_new, true); 
                imagepng($image_new, $filepath_new); 
                break; 

            default: 
                return false; 
        } 

        imagedestroy($image_old); 
        imagedestroy($image_new); 
        return true; 
    }
}

?>
