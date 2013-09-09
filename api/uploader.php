<?php
require_once("user.php");
require_once("db.php");

class Uploader {
    var $allowedExts = array("gif", "jpeg", "jpg", "png");
    var $allowedMimes = array("mage/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");
    
    var $folder = 'upload/';
    var $size = 2097152;

    function Upload() {
        $response = array();
        $user = User::is_auth();
        if (!$user) {
            echo "Please login";
            return false;
        }
        
        $arr = array();
        $temp = explode(".", $_FILES["image"]["name"]);
        $extension = end($temp);
        if (in_array($_FILES["image"]["type"], $this->allowedMimes)
        && ($_FILES["image"]["size"] < $this->size)) {
            if ($_FILES["image"]["error"] > 0) {
                echo '[{"error":'. $_FILES["image"]["error"] .', "response": ""}]';
            } else {
                $path = $this->folder.$user[1]."/".$_FILES["image"]["name"];
                if (file_exists($path)) {
                    echo '[{"error":"already exists", "response": ""}]';
                } else {
                    if (!file_exists($this->folder.$user[1])) {
                        mkdir($this->folder.$user[1]);
                    }
                    $this->imageToDB($user[1], $path);
                    move_uploaded_file($_FILES["image"]["tmp_name"], $path);
                    echo '[{"error":"", "response": "'.$path.'"}]';
                }
            }
        } else {
            echo '[{"error":"Invalid file", "response": ""}]';
        }
        
    }
    
    function imageToDB($id, $path) {
        $db = new DB();
        $db->setValues(array('path' => $path, 'id_username' => $id));
        $db->setTable('gallery');
        if ($db->insert()) {
            return true;
        }
        return false;
    }
}
?>