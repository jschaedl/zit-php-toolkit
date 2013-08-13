<?php

namespace Zit\Upload;
use Zit\Upload\Exception\UploaderException;


class Uploader 
{
    protected $config = array(
        'locale_code' => 'de'
        , 'upload_dir' => './_upload'
        , 'supported_filetypes' => array(
            'image/jpeg'
        )
        , 'allow_multiple_uploads' => false
        , 'allow_override_existing' => true
        , 'max_file_size' => '8M'
    );

    public function __construct($uploadConfig=array())
    {
       $this->applyConfig($uploadConfig);
    }

    /**
     * @todo extension could be blown away, especially on mac osx systems, extract extension on mime type basis
     */
    public function receive($uploadedFile, $customFilename='')
    {
        if ($this->checkUploadedFile($_FILES[$uploadedFile]))
        {
            $file_temp = $_FILES[$uploadedFile]['tmp_name'];
            $file_name = strtolower($_FILES[$uploadedFile]['name']);

            if (isset($customFilename) && $customFilename != '') 
                $file_name_normalized = $customFilename;
            else
                $file_name_normalized = substr($file_name, 0, strpos($file_name, '.'));
                
            $file_ext_normalized = substr($file_name, strrpos($file_name, '.') + 1);
            $file_path = $this->config['upload_dir'] .  '/' . $file_name_normalized . '.' . $file_ext_normalized;

            if($this->config['allow_override_existing'])
            {
                if ($this->move_uploaded_file($file_temp, $file_path)) 
                {
                    chmod($file_path, 0775);
                    return true;
                }
            }
            else
            {
                throw new UploaderException(UPLOAD_ERR_FILE_EXISTS);
            }
        }

        return false;
    }

    public function applyConfig($config)
    {
        $this->config = array_merge($this->config, $config);
        $this->config['max_file_size'] = $this->convertMaxFileSize($this->config['max_file_size']);
    }

    /**
     * This method should be overriden for testing purpose especially in cli environments like
     * phpunit. 
     * @see ZitTest\Upload\TestableUploader
     */
    protected function is_uploaded_file($filename)
    {
        return is_uploaded_file($filename);
    }

    /**
     * This method should be overriden for testing purpose especially in cli environments like
     * phpunit. 
     * @see ZitTest\Upload\TestableUploader
     */
    protected function move_uploaded_file($filename, $destination)
    {
        return move_uploaded_file($filename, $destination);
    }

    private function checkUploadedFile($uploadedFile)
    {
        // check if uploaded file is empty
        if(!$this->is_uploaded_file($uploadedFile['tmp_name']))
        {
            throw new UploaderException(UPLOAD_ERR_FILE_IS_EMPTY);
        }
            
        // check for predifined php errors on upload
        if($uploadedFile['error']) 
        {
            throw new UploaderException($uploadedFile['error']);
        }

        // upload file seems to be ok
        if ($uploadedFile['error'] === UPLOAD_ERR_OK)
        {
            // now check supported filetype
            if (!$this->checkFileType($uploadedFile['type']))
            {
                throw new UploaderException(UPLOAD_ERR_UNSUPPORTED_FILETYPE);
            }
        }

        return true;
    }

    private function convertMaxFileSize($max_file_size) 
    {
        if (!isset($max_file_size))
            $max_file_size  = trim(ini_get('post_max_size'));

        $unity = strtolower($max_file_size[strlen($max_file_size)-1]);
        
        switch($unity) 
        {
            case 'g':   // giga
                $max_file_size *= 1024;
            case 'm':   // mega
                $max_file_size *= 1024;
            case 'k':   // kilo
                $max_file_size *= 1024;
        }
        
        return $max_file_size;
    }

    private function checkFileType($fileType)
    {
        foreach ($this->config['supported_filetypes'] as $supportedFileType)
        {
            if (strtolower($fileType) === $supportedFileType)
            {
                return true;
            }
        }

        return false;
    }
}

?>
