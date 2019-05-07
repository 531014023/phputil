<?php
echo 'mdyt backup..';
$mdyt = exec("php backup.php 172.18.181.19 root root mdyt");
if(0 == $mdyt) {
    echo 'mdyt restore..';
    $mdyt = exec("php restore.php 127.0.0.1 mdyt kSMpyc2P7DDc3ena mdyt");
}