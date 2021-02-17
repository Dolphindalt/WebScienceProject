<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';

use Dalton\Framework\ControllerBase;
use Dalton\ThreeLeaf\Models\FileModel;

const VALID_EXTENSIONS = ['jpg', 'png', 'jpeg', 'bmp', 'gif'];

class FileUpload extends ControllerBase {

    private $file_directory;

    public function __construct() {
        $this->file_directory = ROOT_PATH.'public/post_images/';
    }

    public function tryUploadFile() {
        $img_file = $_FILES['image']['name'];
        $file_extension = pathinfo($img_file, PATHINFO_EXTENSION);
        if ($img_file == '') {
            echo 'The image is required.';
            return null;
        } else if (!in_array($file_extension, VALID_EXTENSIONS)) {
            echo 'Image type not supported.';
            return null;
        } else if (($_FILES['image']['size'] > 2000000)) {
            echo 'Image size is larger than 2MB.';
            return null;
        }
        // Generate a UUID to use as the file name.
        $file_name = uniqid() . '.' . $file_extension;
        $target = $this->file_directory . $file_name;
        if (move_uploaded_file($img_file, $target)) {
            $result = FileModel::insertFileRecord($file_name);
            if ($result != '') {
                // Last inserted file id.
                return $result;
            }
        }
        echo 'Something went wrong when uploading the file.';
        return null;
    }

}

?>