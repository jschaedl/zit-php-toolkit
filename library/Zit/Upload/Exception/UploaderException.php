<?php

namespace Zit\Upload\Exception;

define('UPLOAD_ERR_FILE_IS_EMPTY', 101);
define('UPLOAD_ERR_UNSUPPORTED_FILETYPE', 102);
define('UPLOAD_ERR_FILE_EXISTS', 103);


class UploaderException extends \Exception
{
    public function __construct($code)
    {
        $message = $this->codeToString($code);
        parent::__construct($message, $code);
    }

    public function codeToString($code)
    {
        switch ($code) { 
            case UPLOAD_ERR_INI_SIZE: 
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini"; 
                break; 
            case UPLOAD_ERR_FORM_SIZE: 
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"; 
                break; 
            case UPLOAD_ERR_PARTIAL: 
                $message = "The uploaded file was only partially uploaded"; 
                break; 
            case UPLOAD_ERR_NO_FILE: 
                $message = "No file was uploaded"; 
                break; 
            case UPLOAD_ERR_NO_TMP_DIR: 
                $message = "Missing a temporary folder"; 
                break; 
            case UPLOAD_ERR_CANT_WRITE: 
                $message = "Failed to write file to disk"; 
                break; 
            case UPLOAD_ERR_EXTENSION: 
                $message = "File upload stopped by extension"; 
                break; 
            case UPLOAD_ERR_FILE_IS_EMPTY: 
                $message = "File is empty"; 
                break; 
            case UPLOAD_ERR_UNSUPPORTED_FILETYPE: 
                $message = "Unsupported filetype"; 
                break; 
            case UPLOAD_ERR_FILE_EXISTS: 
                $message = "File already exists"; 
                break; 

            default: 
                $message = "Unknown upload error"; 
                break; 
        } 
        return $message; 
    }
}

?>