<?php
require_once('db.php');

class User {
    var $id;
    var $name;
    
    function __construct() {
        $username = $this->is_auth();
        if ($username) {
            if ($_GET['user'] == 'logout') {
                $this->logout();
                header("refresh: 0.3;");
            } else if ($_GET['activate'] && $_GET['email']) {
                $this->activateUser($_GET['activate']);
            }
            $this->name = $username[0];
            $this->id = $username[1];
        }
    }
    
    function activateUser($id, $hash) {
        $db = new DB();
        $db->setSelect("hash, id, username");
        $db->setTable("user");
        $db->setCondition(array('id' => $id, 'hash' => $hash));
        $userdata = $db->getResult();
        if ($userdata) {
            $db->setTable('user');
            $db->setCondition(array('id'=> $id));
            $db->setCondition(array('is_active'=> 1));
            $db->update();
        }
    }

    static function is_auth() {
        if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
            $db = new DB();
            $db->setSelect("hash, id, username");
            $db->setTable("user");
            $db->setCondition(array('id' => intval($_COOKIE['id'])));
            $userdata = $db->getResult();
            $userdata = $userdata[0];
            
            if(($userdata['hash'] != $_COOKIE['hash']) or ($userdata['id'] != $_COOKIE['id'])) {
                setcookie("id", "", time() - 3600*24*30*12, "/");
                setcookie("hash", "", time() - 3600*24*30*12, "/");
            } else {
                return array($userdata['username'], $userdata['id']);
            }
        }
        return false;
    }
    
    function is_exist($username, $email) {
        $db = new DB();
        $db->setSelect('COUNT(id)');
        $db->setTable('user');
        $db->setCondition(array("username"=>$username, "email"=>$email), 0, "or");
        $res = $db->getResult();
        if ($res[0]['COUNT(id)']) {
            return true;
        }
        return false;
    }
    
    function registration($username, $pass, $pass2, $email) {
        $err = array();
        
        if ($pass == "") $err[] = "Pass cant be empty";
        if ($username == "") $err[] = "Username cant be empty";
        if ($pass != $pass2) $err[] = "Pass and pass2 are not equals";
        if (!preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $email)) $err[] = "Email not valid";
    
        $pass = md5(md5($pass));
        if ($this->is_exist($username, $email)) $err[] = "User with this username is exists";
        
        if (count($err) == 0) {
            $db = new DB();
            $hash = md5($this->generate_code(10));
            $db->setValues(array('username' => $username, 'password' => $pass, 'email' => $email, 'hash' => $hash));
            $db->setTable('user');
            if ($db->insert()) {
                $this->sendMail($email, $hash);
                return null;
            }
        }
        $err[] = "Couldnt create record in DB";
        return $err;
        
    }
    
    function sendMail($email, $hash) {
        $subject = "Please activate account";
        
        $message = '
        <html>
        <head>
        </head>
        <body>
            <a href="192.241.249.196/gallery/login.php?email='.$email.'&activate='.$hash.'">click here for activation</a>
        </body>
        </html>';
        
        $headers  = "Content-type: text/html; charset=windows-1251 \r\n";
        
        mail($email, $subject, $message, $headers); 
    }
    
    # Функция для генерации случайной строки
    private function generate_code($length=6) {
    
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;  
    
        while (strlen($code) < $length) {
                $code .= $chars[mt_rand(0,$clen)];  
        }
    
        return $code;
    }
    
    function login($username, $pass) {
        // die;
        $db = new DB();
        
        $db->setSelect('id, password, is_active');
        $db->setTable('user');
        $db->setCondition(array('username' => $username));
        
        $res = $db->getResult();
        # Соавниваем пароли
        if($res[0]['password'] === md5(md5($pass))) {
            if ($res[0]['is_active'] == 0) return false;
            $hash = md5($this->generate_code(10));
            
            $db->setTable('user');
            $db->setCondition(array('hash'=>$hash), 1);
            $db->setCondition(array('id'=>$res[0]['id']));
            if ($db->update()) {
                # Ставим куки
                setcookie("id", $res[0]['id'], time()+60*60*24*30);
                setcookie("hash", $hash, time()+60*60*24*30);
                return true;
            }
            return false;
        }
        
        return false;
    }
    
    function logout() {
        setcookie ("id", "", time() - 3600);
        setcookie ("hash", "", time() - 3600);
    }
    
    function getGallery() {
        $db = new DB();
        $db->setSelect("path");
        $db->setTable("gallery");
        $db->setCondition(array('id_username' => $this->id));
        $gallery = $db->getResult();
        return $gallery;
    }
}

?>