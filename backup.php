<?php
header("Content-Type: text/html; charset=utf-8");
// 设置SQL文件保存文件名
$dbhost = $argv[1];//主机IP地址
$cfg_dbuser = $argv[2];//用户名
$cfg_dbpwd = $argv[3];//密码
$cfg_dbname = $argv[4];
$ignore_table = isset($argv[5]) ? "--ignore-table={$cfg_dbname}.{$argv[5]}" : '';
$ignore_table .= isset($argv[6]) ? " --ignore-table={$cfg_dbname}.{$argv[6]}" : '';
$filename = date("Ymd", time()) . "-" . $cfg_dbname . ".sql.gz";
// 获取当前页面文件路径，SQL文件就导出到指定文件夹内
// $savePath = './Public/upload/DB/';
$savePath = './backup/';
if (!file_exists($savePath)) {
    mkdir($savePath, 0777, true);
}
$tmpFile = $savePath . $filename;
if(file_exists($tmpFile)) {
    fopen($tmpFile, "r");
    chmod($tmpFile, 0777);
    //删除之前备份的数据
    $dh = opendir($savePath);
    if ($dh) {
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $savePath . "/" . $file;
                if (!is_dir($fullpath)) {
                    if (strpos($fullpath, $cfg_dbname) !== false) {
                        unlink($fullpath);
                    }
                }
            }
        }
        closedir($dh);
    } //删除之前备份的数据
}
// 用MySQLDump命令导出数据库
$bool_dump = exec("mysqldump --single-transaction -h$dbhost -u$cfg_dbuser -p$cfg_dbpwd --default-character-set=utf8 $cfg_dbname {$ignore_table} | gzip > " . $tmpFile);
if (0 == $bool_dump) {
    $arr = json_encode(array('msg' => 1, 'url' => $filename));
    echo $arr;
}else {
    $arr = json_encode(array('msg' => '备份失败，请联系管理员！'));
    echo $arr;
}
