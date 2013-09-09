<?php
require_once("user.php");
require_once("db.php");
require_once("../config.php");

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
        echo 3;
        $config = new Config();
        
        $arr = array();
        $temp = explode(".", $_FILES["image"]["name"]);
        $extension = end($temp);
		echo 4;
        if (in_array($_FILES["image"]["type"], $this->allowedMimes)
        && ($_FILES["image"]["size"] < $this->size)) {
			echo 5;
            if ($_FILES["image"]["error"] > 0) {
                echo '[{"error":'. $_FILES["image"]["error"] .', "response": ""}]';
            } else {
                $path = $this->folder.$user[1]."/".$_FILES["image"]["name"];
                if (file_exists($config->path.$path)) {
                    echo '[{"error":"already exists", "response": ""}]';
                } else {
                    if (!file_exists($config->path.$this->folder.$user[1])) {
                        mkdir($config->path.$this->folder.$user[1]);
                    }
                    $this->imageToDB($user[1], $path);
                    move_uploaded_file($_FILES["image"]["tmp_name"], $config->path.$path);
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