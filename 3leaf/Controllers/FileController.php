<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'3leaf/Models/FileModel.php';

use Dalton\Framework\ControllerBase;
use Dalton\ThreeLeaf\Models\FileModel;

const VALID_EXTENSIONS = ['jpg', 'png', 'jpeg', 'bmp', 'gif'];

class FileUpload extends ControllerBase {

    private $file_directory;

    public function __construct() {
        $this->file_directory = ROOT_PATH.'public/post_images/';
    }

    public function tryUploadFile($uploader_name) {
        $results = [];
        $img_file = $_FILES['image']['name'];
        $file_extension = pathinfo($img_file, PATHINFO_EXTENSION);
        if ($img_file == '') {
            $results['error'] = 'The image is required.';
            return $results;
        } else if (!in_array($file_extension, VALID_EXTENSIONS)) {
            $results['error'] = 'Image type not supported.';
            return $results;
        } else if (($_FILES['image']['size'] > 2000000)) {
            $results['error'] = 'Image size is larger than 2MB.';
            return $results;
        }
        // Generate a UUID to use as the file name.
        $file_name = uniqid() . '.' . $file_extension;
        $target = $this->file_directory . $file_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $results = FileModel::insertFileRecord($file_name, $uploader_name);
            if (!empty($results)) {
                // Last inserted file id.
                return $results;
            }
        }
        $results['error'] = 'Something went wrong when uploading the file.';
        return $results;
    }

    public function tryUploadOptionalImage($uploader_name) {
        $results = [];
        $img_file = $_FILES['image']['name'];
        $file_extension = pathinfo($img_file, PATHINFO_EXTENSION);
        if ($img_file == '') {
            $results['id'] = null;
            return $results;
        } else if (!in_array($file_extension, VALID_EXTENSIONS)) {
            $results['error'] = 'Image type not supported.';
            return $results;
        } else if (($_FILES['image']['size'] > 2000000)) {
            $results['error'] = 'Image size is larger than 2MB.';
            return $results;
        }
        // Generate a UUID to use as the file name.
        $file_name = uniqid() . '.' . $file_extension;
        $target = $this->file_directory . $file_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $results = FileModel::insertFileRecord($file_name, $uploader_name);
            if (!empty($results)) {
                // Last inserted file id.
                return $results;
            }
        }
        $results['error'] = 'Something went wrong when uploading the file.';
        return $results;
    }

}

?>