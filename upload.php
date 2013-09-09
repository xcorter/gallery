<?php
    require_once('api/uploader.php');
	echo "1";
    $uploader = new Uploader();
	echo "2";
    $msg = $uploader->upload();
    echo $msg;
    return $msg;
?>