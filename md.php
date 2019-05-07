<?php
echo 'md backup..';
$md = exec("php backup.php 172.18.181.19 root root md md_log");
if (0 == $md) {
    echo 'md restore..';
    $md = exec("php restore.php 127.0.0.1 md_93dd_top npxB6XDbATAHiTXB md_93dd_top md");
}