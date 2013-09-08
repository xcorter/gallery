<?php
require_once('api/db.php');
require_once('api/user.php');


if (User::is_auth()) {
    header("Location: index.php"); 
}

$user = new User();
if ($_POST['submit']) {
    $err = $user->login($_POST['username'], $_POST['password']);
    if ($err) header("Location: index.php");
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
            if (!$err) {
                echo '<p><b>Login was failed</b></p>';
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
                        <input type="password" name="password" />
                    </td>
                </tr>
            </table>
            <input type="submit" name="submit" value="Login">
        </form>
    </div>
    
</body>

</html>