<?php 
// index.php
header('Content-type: text/html; charset=utf-8');
 
 // 引入文件
 require('rndChinaName.class.php');
 
 $name_obj = new rndChinaName(); 
 $name = $name_obj->getName(5);
 echo $name;