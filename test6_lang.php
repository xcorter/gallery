<?php

if ($handle = opendir('.')) {

    while (false !== ($entry = readdir($handle))) {

        if ($entry != "." && $entry != "..") {

            echo "$entry\n";
            echo file_get_conents($entry);
        }
    }

    closedir($handle);
}

?>
