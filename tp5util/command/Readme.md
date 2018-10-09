##这是onebase的后台自动生成工具,仅适用于onebase基础框架
###使用方法:
    1.将文件夹放在app目录下任意位置
    2.在command.php中添加`'app\common\command\Create'`,实际路径为你放的路径
    3.建好数据库并写好注释后在根目录控制台使用命令 `php think create -t 你的表名(驼峰命名,首字母小写) -m 后台菜单名(自动添加管理二字)
    4.将生成的文件上传服务器即可