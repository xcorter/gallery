<?php
require_once('db.php')

class Gallery {
    var $id;

    function __construct() {
        $db = new DB();
        $db->setSelect('id, id_username, path');
        $db->setTable('gallery');
        $db->setCondition(array("id_username"=>$username));
        $res = $db->getResult();
        
        if ($res['COUNT[id]']) {
            return true;
        }
        return false;
    }
    
    function getGallery($id_user) {
        $db = new DB();
        $db->setSelect('id, id_username, path');
        $db->setTable('gallery');
        $db->setCondition(array("id_username"=>$id_user));
        $res = $db->getResult();
    }
}
?>