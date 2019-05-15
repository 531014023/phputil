<?php
/**
 * Created by PhpStorm.
 * User: dy
 * Date: 2018/10/3
 * Time: 11:56
 */
set_time_limit(0);
require 'code.php';
require_once __DIR__ . "/phpQuery/phpQuery.php";
function http_post($url, $post_data, $cookie, $ret_url = true)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    if (is_array($post_data)) {
        $post_str = http_build_query($post_data);
    }
    if (is_string($post_data)) {
        $post_str = urlencode($post_data);
    }
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_str);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    $ip = ip();
    $header = ["Content-Length: " . strlen($post_str), 'X-FORWARDED-FOR: ' . $ip, 'CLIENT-IP: ' . $ip, 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    $res = curl_exec($ch);
    if ($ret_url) {
        $res = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    }
    curl_close($ch);
    return $res;
}

function http_get($url, $cookie, $ret_url = false)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    $ip = ip();
    $header = ['X-FORWARDED-FOR: ' . $ip, 'CLIENT-IP: ' . $ip, 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    $res = curl_exec($ch);
    if ($ret_url) {
        $res = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    }
    curl_close($ch);
    return $res;
}

function ip()
{
    $ip_long = array(array('607649792', '608174079'), // 36.56.0.0-36.63.255.255
        array('1038614528', '1039007743'), // 61.232.0.0-61.237.255.255
        array('1783627776', '1784676351'), // 106.80.0.0-106.95.255.255
        array('2035023872', '2035154943'), // 121.76.0.0-121.77.255.255
        array('2078801920', '2079064063'), // 123.232.0.0-123.235.255.255
        array('-1950089216', '-1948778497'), // 139.196.0.0-139.215.255.255
        array('-1425539072', '-1425014785'), // 171.8.0.0-171.15.255.255
        array('-1236271104', '-1235419137'), // 182.80.0.0-182.92.255.255
        array('-770113536', '-768606209'), // 210.25.0.0-210.47.255.255
        array('-569376768', '-564133889'), // 222.16.0.0-222.95.255.255
    );
    $rand_key = mt_rand(0, 9);
    return $ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
}

/*
 判断当前的运行环境是否是cli模式
 */
function is_cli()
{
    return preg_match("/cli/i", php_sapi_name()) ? true : false;
}

class Login
{
    private $index_url = "http://shome.ouchn.cn/";
    private $url = "http://sso.ouchn.cn/Passport/Login?ru=http%3a%2f%2fshome.ouchn.cn%2f&to=-2&aid=6&ip=100.125.68.19&lou=http%3a%2f%2fshome.ouchn.cn%2f6%2fMCSAuthenticateLogOff.axd&sf=f737c7f6559c5c1d";
    private $code_url = "http://sso.ouchn.cn/ashx/CheckCode.ashx";
    private $logout_url = "http://sso.ouchn.cn/Passport/LogOff?redirectUrl=http://shome.ouchn.cn/";
    private $username;
    private $password;
    private $cookie_file;
    private $code_img;
    private $white_list = ['1751001459734', '1751001459886',];
    public function __construct($username, $password, $cookie_name = '')
    {
        $this->username = $username;
        $this->password = $password;
        $this->cookie_file = dirname(__FILE__) . '/cookie/cookie' . $cookie_name . '.txt';
        $this->code_img = __DIR__ . '/code.png';
        $this->url = http_get($this->index_url, $this->cookie_file, true);
    }
    public function checkAuth($username)
    {
        if (in_array($username, $this->white_list)) {
            return true;
        }
        return false;
    }
    public function getCodeImg()
    {
        if ($this->testLogin()) {
            return false;
        } else {
            $img = http_get($this->code_url, $this->cookie_file);
            file_put_contents($this->code_img, $img);
            return $this->code_img;
        }
    }
    public function login($code = null)
    {
        if (!$code) {
            http_get($this->url, $this->cookie_file);//设置cookie
            $img = http_get($this->code_url, $this->cookie_file);
            file_put_contents($this->code_img, $img);
//            fwrite(STDOUT, "请输入验证码: \n");
//            $code = trim(fgets(STDIN));
            $code = $this->parseCode();
            echo '验证码识别为: ' . $code . PHP_EOL;
        }
        $form_data['username'] = $this->username;
        $form_data['password'] = $this->password;
        $form_data['checkCode'] = $code;
        $form_data['isSaveUserName'] = 'on';
        print_r($form_data);
        if(!is_cli()) {
            ob_flush();
            flush();
        }
        $result = http_post($this->url, $form_data, $this->cookie_file);
        file_put_contents('html/post_login.html', $result);
        if (strpos($result, 'sso.ouchn.cn') === false) {
            $this->jump_return();
            return true;
        }
        return false;
    }
    public function log_out()
    {
        http_get($this->logout_url, $this->cookie_file);
        var_dump(http_get($this->url, $this->cookie_file));
    }
    public function parseCode()
    {
        $v = new valite();
        $v->setImage($this->code_img);
        $v->getHec();
        $code = $v->run();
        return $code;
    }
    public function getCookieFile()
    {
        return $this->cookie_file;
    }
    public function testLogin()
    {
        $url = http_get($this->url, $this->cookie_file, true);
//        print_r($url.PHP_EOL);
        if (strpos($url, 'sso.ouchn.cn') === false) {
            $this->jump_return();
            return true;
        }
        return false;
    }
    public function faxueStart()
    {
        echo '法学登录成功..' . PHP_EOL;
        require_once "ouchn.php";
        $ouchn1 = new Ouchn();
        $ouchn1->cookie_file = $this->getCookieFile();
        $ouchn1->faxue();
    }
    public function kuaijiStart()
    {
        echo '会计登录成功...' . PHP_EOL;
        require_once "ouchn.php";
        $ouchn2 = new Ouchn();
        $ouchn2->cookie_file = $this->getCookieFile();
        $ouchn2->kuaiji();
    }
    public function start($key, $username)
    {
        require_once "ouchn.php";
        $ouchn2 = new Ouchn();
        $ouchn2->cookie_file = $this->getCookieFile();
        $ouchn2->username = $username;
        $class_name = $ouchn2->class_name_map[$key];
        echo $class_name . '登录成功...' . PHP_EOL;
        $ouchn2->actionClickClass($key);
    }
    public function test_get($url = null)
    {
        if (!$url) {
            $url = "http://sichuan.ouchn.cn/course/view.php?id=3362&section=2";
        }
        $html = http_get($url, $this->getCookieFile());
        file_put_contents("html/test.html", $html);
        print_r($this->getCookieFile());
    }
    public function jump_return()
    {
        $base_url = 'http://shome.ouchn.cn/Learn/Course';
        $class_content = http_get($base_url, $this->getCookieFile());
        phpQuery::newDocumentHTML($class_content);
        $class_a = pq('#LearningCourseDiv li p a')->eq(0);
        $site = $class_a->attr('site');
        $roleid = $class_a->attr('roleid');
        $coursecode = $class_a->attr('coursecode');
        $curriculumid = $class_a->attr('curriculumid');
        $jump_url = $base_url . '/GetMoodleHub';
        $jump_url .= "?site=" . $site . '&rid=' . $roleid . '&courseCode=' . $coursecode . '&cid=' . $curriculumid;
        print_r('jump_url: ' . $jump_url . PHP_EOL);
        $result = http_get($jump_url, $this->getCookieFile());
        phpQuery::$documents = [];
        phpQuery::newDocumentHTML($result);
        $action = pq("#moodlehub")->attr('action');
        $data = pq("#CourseClass")->val();
        print_r($action . PHP_EOL);
        print_r('CourseClass=' . urlencode($data) . PHP_EOL);
        if(!is_cli()) {
            ob_flush();
            flush();
        }
        $res = http_post($action, ['CourseClass' => $data], $this->getCookieFile(), true);
        file_put_contents("html/test.html", $result);
        file_put_contents("html/test1.html", $res);
    }
}

if (isset($_GET['get'])) {
    if ($_GET['get'] == 'guake') {
        $username = $_GET['username'];
        $password = $_GET['password'];
        $class_name = $_GET['class_name'];
        $login_ = new Login($username, $password, $username);
        if (!$login_->checkAuth($username)) {
            die('请联系作者开通权限!');
        }
        if (!$login_->testLogin()) {
            $code = $_GET['code'];
            if ($login_->login($code)) {
                $login_->start($class_name, $username);
            }
        } else {
            $login_->start($class_name, $username);
        }
    } else if ($_GET['get'] == 'get_code') {
//        $username = $_GET['username'];
//        $password = $_GET['password'];
//        $class_name = $_GET['class_name'];
//        $login_ = new Login($username, $password, $username);
//        $code_img = $login_->getCodeImg();
//        if($code_img){
//            $code_img = str_replace(__DIR__.'/','',$code_img);
//            die(json_encode(['code'=>0,'data'=>$code_img]));
//        }
//        $login_->start($class_name,$username);
    } else if ($_GET['get'] == 'log') {
        $username = $_GET['username'];
        $password = $_GET['password'];
        $class_name = $_GET['class_name'];
        $dir = __DIR__ . '/ouchn_log/' . date('Ymd');
        require_once "ouchn.php";
        $ouchn = new Ouchn();
        $class_arr = $ouchn->class_map[$class_name];
        $result = '';
        foreach ($class_arr as $class) {
            $file_name = $dir . '/' . $class . $username . '.log';
            if (file_exists($file_name)) {
                $result .= PHP_EOL . '------------------------------------------------' . PHP_EOL;
                $result .= file_get_contents($file_name);
            }
        }
        $result = str_replace(PHP_EOL, '<br/>', $result);
        die($result);
    } else if ($_GET['get'] == 'get_class') {
        require_once "ouchn.php";
        $ouchn = new Ouchn();
        die(json_encode(['class_map' => $ouchn->class_map, 'class_name_map' => $ouchn->class_name_map]));
    }
} else {
//    $login_faxue = new Login("1751001459734", "19930615", 'faxue');
//    if (!$login_faxue->testLogin()) {
//        if ($login_faxue->login()) {
//            $login_faxue->start('2019_up_faxue',"1751001459734");
//        }
//    } else {
//        $login_faxue->start('2019_up_faxue',"1751001459734");
//    }
//    $login_kuaiji = new Login("1751001459886", '19930430', 'kuaiji');
//    if (!$login_kuaiji->testLogin()) {
//        if ($login_kuaiji->login()) {
//            $login_kuaiji->start('2019_up_kuaiji', "1751001459886");
//        }
//    } else {
//        $login_kuaiji->start('2019_up_kuaiji', "1751001459886");
//    }
}

