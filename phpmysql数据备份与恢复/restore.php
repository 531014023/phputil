<?php
$host = $argv[1];
$port = 3306;
$user = $argv[2];
$password = $argv[3];
$db_name = $argv[4];
$db_source = isset($argv[5]) ? $argv[5] : $argv[4];
$savePath = './backup/';
$dh=opendir($savePath);
if($dh){
    while ($file=readdir($dh)) {
        if($file!="." && $file!="..") {
            $fullpath=$savePath."/".$file;
            if(!is_dir($fullpath)) {
                if(strpos($fullpath,$db_source) !== false) {
                    $result = exec("gunzip < {$fullpath} | mysql -h{$host} -P{$port} -u{$user} -p{$password} {$db_name}");
                    if(0 == $result) {
                        $arr = json_encode(array('msg' => 1, 'url' => $fullpath));
                        echo $arr;
                    }else {
                        $arr = json_encode(array('msg' => '备份失败，请联系管理员！'));
                        echo $arr;
                    }
                }
            }
        }
    }
    closedir($dh);
}
