<?php
require_once('api/db.php');
require_once('api/user.php');
require_once('config.php');

if ($_POST['submit']) {
    $user = new User();
    
    $err = $user->registration($_POST['username'], $_POST['password1'], $_POST['password2'], $_POST['email']);
    if (!$err) {
        $config = new Config();
        header("Location: ".$config->url);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <script src="js/main.js"></script>
</head>
<body>
    <div id="content">
        <div>
        <?
            if (count($err) != 0) {
                echo '<p><b>Registration was failed</b></p>';
                foreach ($err as $e) {
                    echo $e."<br/>";
                }
            }
        ?>
        </div>
        <form method="POST">
            <table>
                <tr>
                    <td>
                        Username
                    </td>
                    <td>
                        <input name="username" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Password
                    </td>
                    <td>
                        <input type="password" name="password1" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Password 2
                    </td>
                    <td>
                        <input type="password" name="password2" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Email
                    </td>
                    <td>
                        <input name="email" />
                    </td>
                </tr>
            </table>
            <input name="submit" type="submit" value="Registration">
        </form>
    </div>
</body>