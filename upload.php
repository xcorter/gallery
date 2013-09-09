<?php
    require_once('api/uploader.php');
    $uploader = new Uploader();
    $msg = $uploader->upload();
    echo $msg;
    return $msg;
?>