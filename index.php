<?php
    require_once('api/db.php');
    require_once('api/user.php');
    
    $user = new User();
    $resp = $user->is_auth();
?>
<!DOCTYPE html>
<html>
<head>
<script>

</script>

</head>
<body>
    <div id="header">
        <?php
            //$resp = $user->is_auth();
            if ($resp) {
            ?>
                Hello, <? echo $resp[0]; ?>! | <a href="?user=logout">Logout</a>
            <?
            } else {
            ?>
                <a href="registration.php">Registration</a> | <a href="login.php">Login</a>
            <?
            }
            ?>
    </div>
    
    <div id="content">
        <? if ($resp) {
            //$gallery = new Gallery();
            ?>
            <div>
                <input type="file" name="file" id="file-field" multiple="true" />
                <p>File can be less than 2 mb</p>
                <button onclick="sendImages();">Send</button>
                <button onclick="clearAll();">Clear</button>
            </div>
            <div id="img-container">
                <ul id="img-list"></ul>
            </div>
            <div id="gallery">
            <?
            foreach ($user->getGallery() as $image) {
                echo "<a href='".$image["path"]."' target='_blank'><img width='150' src='".$image["path"]."'/></a>";
            }
            ?>
            </div>
            <?
        }
        ?>
    </div>
    
    <div id="footer">
    
    </div>
    <script src="js/main.js"></script>
</body>

</html>