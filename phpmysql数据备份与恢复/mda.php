<?php
echo 'mda backup..';
$mda = exec("php backup.php 172.18.181.19 root root mda");
if (0 == $mda) {
    echo 'mda restore..';
    $mda = exec("php restore.php 127.0.0.1 mda 53aYkabG55rPx3fa mda");
}