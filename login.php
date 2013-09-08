<?php
require_once('api/db.php');
require_once('api/user.php');


if (User::is_auth()) {
    header("Location: index.php"); 
}

$condition = true;

$user = new User();

if ($_POST['submit']) {
    $condition = $user->login($_POST['username'], $_POST['password']);
    if ($condition) header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <div id="content">
        <div>
        <?
            if (!$condition) {
                echo '<p><b>Login was failed</b></p>';
                echo '<p>Check password or activate account</p>';
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