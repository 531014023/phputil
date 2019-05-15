<?php
set_time_limit(0);
require_once __DIR__."/phpQuery/phpQuery.php";

/**
 * Class Ouchn
 * url
 * http://sichuan.ouchn.cn/mod/page/view.php?id=309009
 */
class Ouchn
{
    public $links = [];
    public $clickLinks = [];
    public $cookie = '';
    public $cookie_file;
    public $username;
    public $class_map = [];
    public $class_name_map = [];
    private $current_http_code;
    private $http_time_out = 0;
    public function setLinks($link = null)
    {
        if(in_array($link,$this->clickLinks))
        {
            return;
        }
        $this->links[] = $link;
        $this->links = array_unique($this->links);
    }

    /**
     * 获取一条未处理过的link
     */
    public function getLink()
    {
       return array_pop($this->links);
    }

    public function index($class_name)
    {
        switch (strtolower($class_name))
        {
            case 'faxue':
                $this->faxue();
                break;
            case 'kuaiji':
                $this->kuaiji();
                break;
            default:
                print_r('暂无此学科!');
                break;
        }
    }

    public function __construct()
    {
        $this->class_map['2019_up_faxue'] = [
            'hunyinjiatingfa',
            'minshisusongfa',
            'zhongcaifa'
        ];
        $this->class_map['2019_up_kuaiji'] = [
            'guanlikuaiji',
            'jinrongshichang',
            'qiyexinxiguanli',
            'caiwu',
            'caikuai',
        ];
        $this->class_name_map['2019_up_faxue'] = '2019法学上';
        $this->class_name_map['hunyinjiatingfa'] = '婚姻家庭法';
        $this->class_name_map['minshisusongfa'] = '民事诉讼法';
        $this->class_name_map['zhongcaifa'] = '仲裁法';
        $this->class_name_map['2019_up_kuaiji'] = '2019会计上';
        $this->class_name_map['guanlikuaiji'] = '管理会计';
        $this->class_name_map['jinrongshichang'] = '金融市场';
        $this->class_name_map['qiyexinxiguanli'] = '企业信息管理';
        $this->class_name_map['caiwu'] = '财务';
        $this->class_name_map['caikuai'] = '财会';
    }

    public function actionClickClass($key){
        echo "开始点击".$this->class_name_map[$key]."...".PHP_EOL;
        if(!is_cli()) {
            ob_flush();
            flush();
        }
        $class_arr = $this->class_map[$key];
        foreach ($class_arr as $class){
            call_user_func(array($this,$class));
            usleep(0.5*1000*1000);
        }
    }
    public function faxue($key = null)
    {

        echo "开始点击法学..".PHP_EOL;
//        $this->minfaxue1();
//        $this->minfaxue2();
//        $this->xingshisusongfa();
//        $this->xingfaxue2();
//        $this->huanjingbaohufa();
//        $this->falvwenshu();
        if($key){
            $class_arr = $this->class_map[$key];
            foreach ($class_arr as $class){
                call_user_func(array($this,$class));
            }
        }else {
            $this->hunyinjiatingfa();
            $this->minshisusongfa();
            $this->zhongcaifa();
        }
    }

    public function kuaiji($key = null)
    {
        echo "开始点击会计..".PHP_EOL;
//        $this->chengbenkuaiji();
//        $this->kuaijidiansuanhua();
//        $this->xifangjingjixue();
//        $this->jingjifalvjichu();
//        $this->zhongjicaiwukuaiji2();
        if($key){
            $class_arr = $this->class_map[$key];
            foreach ($class_arr as $class){
                call_user_func(array($this,$class));
            }
        }else {
            $this->guanlikuaiji();
            $this->jinrongshichang();
            $this->qiyexinxiguanli();
            $this->caiwu();
            $this->caikuai();
        }
    }

    public function writeLog($log_name,$content,$is_end = false,$is_write = true)
    {
        $date = date('Ymd');
        $log_name .= $this->username;
        if($is_write) {
            $dir = dirname(__FILE__) . '/ouchn_log/'.$date;
            if(!file_exists($dir)) {
                mkdir($dir);
            }
            $log_txt = $dir.'/' . $log_name . '.log';
            file_put_contents($log_txt, '|time:' . time() . '|时间:' . date('Y-m-d H:i:s', time()) . PHP_EOL, FILE_APPEND);
            file_put_contents($log_txt, $content . PHP_EOL, FILE_APPEND);
        }
        if($is_end) {
            echo $log_name . ': ' . PHP_EOL;
            echo $content . PHP_EOL;
            echo PHP_EOL;
            if(!is_cli()) {
                ob_flush();
                flush();
            }
        }
    }

    public function saveHtml($url,$content,$httpCode){
        $debug_backtrace = debug_backtrace();
        $len = count($debug_backtrace);
        foreach ($debug_backtrace as $i=>$trace){
            if($i < $len - 1) {
                if ($trace['function'] == 'http_get' && $debug_backtrace[$i + 1]['function'] != 'http_get') {
                    $i++;
                    break;
                }
            }else{
                $i = 1;
                break;
            }
        }
        $class_name = $debug_backtrace[$i]['function'];
        $dir = __DIR__.'/html';
        if(!file_exists($dir)){
            mkdir($dir);
        }
        $dir .= '/'.date('Ymd');
        if(!file_exists($dir)){
            mkdir($dir);
        }
        $file_map = $dir .'/file_map.txt';
        $dir .= '/'.$class_name;
        if(!file_exists($dir)){
            mkdir($dir);
        }
        if($this->username) {
            $dir .= '/' . $this->username;
            if(!file_exists($dir)){
                mkdir($dir);
            }
        }
        $file = $dir . '/' . $httpCode.'_'.md5($url).'.html';
        $file_map_text = $url.' => '.md5($url).PHP_EOL;
        file_put_contents($file_map,$file_map_text,FILE_APPEND);
        file_put_contents($file,$content);
    }

    public function check_log($log_name)
    {
        $date = date('Ymd');
        $log_name .= $this->username;
        $dir = $dir = dirname(__FILE__) . '/ouchn_log/'.$date;
        $log_txt = $dir.'/' . $log_name . '.log';
        if(file_exists($log_txt))
        {
            return false;
        }
        return true;
    }

    function http_get($url)
    {
        if(strpos($url,'http') === false){
            return '';
        }
        $cookie = $this->cookie_file ? : dirname(__FILE__).'/ouchn/cookie.txt';
        $ip = ip();
        $headers = [
            'X-FORWARDED-FOR: '.$ip,
            'CLIENT-IP: '.$ip,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'
        ];
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'GET');
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if(!empty($this->cookie)) {
            if(!file_exists($cookie))
            {
                curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
            }
        }
        if(file_exists($cookie)){
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        }
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        $this->current_http_code = $httpCode;
        curl_close($ch);
        if($httpCode != 200 && $this->http_time_out < 2){
            $this->http_time_out++;
            sleep(1);
            return $this->http_get($url);
        }
        $this->http_time_out = 0;
        $this->saveHtml($url, $res, $httpCode);
        return $res;
    }

    public function guanlikuaiji(){
        if(!$this->check_log('guanlikuaiji')){
            $this->writeLog('guanlikuaiji','管理会计已经学习了',true);
            return;
        }
        $base = 'http://sichuan.ouchn.cn/mod/book/';
//        $base = '';
        $link = "http://sichuan.ouchn.cn/mod/book/view.php?id=399041&chapterid=18218";
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $list = pq('#block-region-side-pre li a');
        $count = 0;
        foreach ($list as $item){
            $href = pq($item)->attr('href');
            if(strpos($href,'javascript') !== false){
                continue;
            }
            $url = $base.$href;
            $this->writeLog('guanlikuaiji',$url);
            $this->http_get($url);
            $count++;
            if($count>=20){
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('guanlikuaiji','管理会计cookie已失效',true,false);
        }else{
            $this->writeLog('guanlikuaiji','管理会计点击了:'.$count.'次',true);
        }
    }

    public function jinrongshichang(){
        if(!$this->check_log('jinrongshichang')){
            $this->writeLog('jinrongshichang','金融市场已经学习了',true);
            return;
        }
        $link1 = "http://sichuan.ouchn.cn/course/view.php?id=129&section=2";
        $link2 = "http://sichuan.ouchn.cn/course/view.php?id=129&section=4";
        $link_arr = compact('link1','link2');
        $count = 0;
        $stop = false;
        foreach ($link_arr as $link){
            $num = explode('&section=',$link);
            $content = $this->http_get($link);
            phpQuery::newDocumentHTML($content);
            $list = pq("#section-{$num[1]} li a");
            foreach ($list as $a){
                $href = pq($a)->attr('href');
                $this->writeLog('jinrongshichang',$href);
                $this->http_get($href);
                $count++;
                if($count>=20){
                    $stop = true;
                    break;
                }
            }
            if($stop)
            {
                break;
            }
            phpQuery::$documents = [];
            usleep(0.2*1000*1000);
        }
        if($count < 5)
        {
            $this->writeLog('jinrongshichang','金融市场cookie已失效',true,false);
        }else{
            $this->writeLog('jinrongshichang','金融市场点击了:'.$count.'次',true);
        }
    }

    public function qiyexinxiguanli(){
        if(!$this->check_log('qiyexinxiguanli')){
            $this->writeLog('qiyexinxiguanli','企业信息管理已经学习了',true);
            return;
        }
        $link = "http://sichuan.ouchn.cn/course/view.php?id=156";
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $count = 0;
        $stop = false;
        for ($i = 4;$i<9;$i++){
            $list = pq("#section-{$i} a");
            foreach ($list as $a){
                $href = pq($a)->attr('href');
                $this->writeLog('qiyexinxiguanli',$href);
                $this->http_get($href);
                $count++;
                if($count>=20){
                    $stop = true;
                    break;
                }
            }
            if($stop)
            {
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('qiyexinxiguanli','企业信息管理cookie已失效',true,false);
        }else{
            $this->writeLog('qiyexinxiguanli','企业信息管理点击了:'.$count.'次',true);
        }
    }

    public function caiwu(){
        if(!$this->check_log('caiwu')){
            $this->writeLog('caiwu','财务管理已经学习了',true);
            return;
        }
        $link = "http://sichuan.ouchn.cn/course/view.php?id=3435";
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $td_list = pq("#section-1 tr td:nth-child(2) a");
        $count = 0;
        $stop = false;
        foreach ($td_list as $td_a){
            $href = pq($td_a)->attr('href');
            $content = $this->http_get($href);
            phpQuery::newDocumentHTML($content);
            $list = pq("ul.img-text li a");
            foreach ($list as $item){
                $href = pq($item)->attr('href');
                $this->writeLog('caiwu',$href);
                $this->http_get($href);
                $count++;
                if($count>=20){
                    $stop = true;
                    break;
                }
            }
            array_pop(phpQuery::$documents);
            $this->writeLog('caiwu',$href);
            $count++;
            if($count>=20){
                break;
            }
            if($stop)
            {
                break;
            }
            usleep(0.2*1000*1000);
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('caiwu','财务管理cookie已失效',true,false);
        }else{
            $this->writeLog('caiwu','财务管理点击了:'.$count.'次',true);
        }
    }

    public function caikuai(){
        if(!$this->check_log('caikuai')){
            $this->writeLog('caikuai','财会法规与职业道德已经学习了',true);
            return;
        }
        $link = "http://sichuan.ouchn.cn/course/view.php?id=1414";
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $count = 0;
        $stop = false;
        for ($i = 1;$i<7;$i++){
            $list = pq("#section-{$i} li a");
            foreach ($list as $item){
                $href = pq($item)->attr('href');
                $this->writeLog('caikuai',$href);
                $this->http_get($href);
                $count++;
                if($count>=20){
                    $stop = true;
                    break;
                }
            }
            if($stop){
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('caikuai','财会法规与职业道德cookie已失效',true,false);
        }else{
            $this->writeLog('caikuai','财会法规与职业道德点击了:'.$count.'次',true);
        }
    }

    public function hunyinjiatingfa(){
        if(!$this->check_log('hunyinjiatingfa')){
            $this->writeLog('hunyinjiatingfa','婚姻家庭法已经学习了',true);
            return;
        }
        $link = "http://sichuan.ouchn.cn/course/view.php?id=24";
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $section = [4,7,8];
        $count = 0;
        $stop = false;
        foreach ($section as $i){
            $list = pq("#section-{$i} li a");
            foreach ($list as $item){
                $href = pq($item)->attr('href');
                $this->writeLog('hunyinjiatingfa',$href);
                $this->http_get($href);
                $count++;
                if($count>=20){
                    $stop = true;
                    break;
                }
            }
            if($stop){
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('hunyinjiatingfa','婚姻家庭法cookie已失效',true,false);
        }else{
            $this->writeLog('hunyinjiatingfa','婚姻家庭法点击了:'.$count.'次',true);
        }
    }

    public function minshisusongfa(){
        if(!$this->check_log('minshisusongfa')){
            $this->writeLog('minshisusongfa','民事诉讼法已经学习了',true);
            return;
        }
        $count = 0;
        $stop = false;
        for($i = 2;$i<22;$i++){
            $link = "http://sichuan.ouchn.cn/course/view.php?id=3362&section={$i}";
            $content = $this->http_get($link);
            phpQuery::newDocumentHTML($content);
            $list = pq("ul.section li a");
            foreach ($list as $item){
                $href = pq($item)->attr('href');
                if(strpos($href,'http://') === false){
                    continue;
                }
                $class = pq($item)->attr('class');
                if(strpos($class,'btn') !== false){
                    continue;
                }
                $this->writeLog('minshisusongfa',$href);
                $this->http_get($href);
                $count++;
                if($count>=20){
                    $stop = true;
                    break;
                }
            }
            if($stop){
                break;
            }
            phpQuery::$documents = [];
            usleep(0.2*1000*1000);
        }
        if($count < 5)
        {
            $this->writeLog('minshisusongfa','民事诉讼法cookie已失效',true,false);
        }else{
            $this->writeLog('minshisusongfa','民事诉讼法点击了:'.$count.'次',true);
        }
    }

    public function zhongcaifa(){
        if(!$this->check_log('zhongcaifa')){
            $this->writeLog('zhongcaifa','仲裁法已经学习了',true);
            return;
        }
        $link = "http://sichuan.ouchn.cn/course/view.php?id=1022";
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $list = pq("#section-3 li a");
        $count = 0;
        foreach ($list as $item){
            $href = pq($item)->attr('href');
            $this->writeLog('zhongcaifa',$href);
            $this->http_get($href);
            $count++;
            if($count>=20){
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('zhongcaifa','仲裁法cookie已失效',true,false);
        }else{
            $this->writeLog('zhongcaifa','仲裁法点击了:'.$count.'次',true);
        }
    }

    public function minfaxue1()
    {
        if(!$this->check_log('minfaxue1'))
        {
            $this->writeLog('minfaxue1','民法学1已经学习了',true);
            return;
        }
        $link = 'http://sichuan.ouchn.cn/course/view.php?id=146';
        $count = 0;
        $stop = false;
        for ($i = 0;$i<7;$i++)
        {
            $content = $this->http_get($link . '&section=' . ($i+1));
            phpQuery::newDocumentHTML($content);
            $as = pq('#section-'.($i+1).' a');
            foreach ($as as $a)
            {
                $href = pq($a)->attr('href');
                $this->writeLog('minfaxue1',$href);
                $this->http_get($href);
                $count++;
                if($count>=20){
                    $stop = true;
                    break;
                }
            }
            if($stop)
            {
                break;
            }
            phpQuery::$documents = [];
        }
        if($count < 5)
        {
            $this->writeLog('minfaxue1','民法学1cookie已失效',true,false);
        }else{
            $this->writeLog('minfaxue1','民法学1点击了:'.$count.'次',true);
        }
    }

    public function minfaxue2()
    {
        if(!$this->check_log('minfaxue2'))
        {
            $this->writeLog('minfaxue2','民法学2已经学习了',true);
            return;
        }
        $link = 'http://sichuan.ouchn.cn/course/view.php?id=147';
        $count = 0;
        $stop = false;
        for ($i = 0;$i<6;$i++)
        {
            $content = $this->http_get($link . '&section=' . ($i+1));
            phpQuery::newDocumentHTML($content);
            $as = pq('#section-'.($i+1).' a');
            foreach ($as as $a)
            {
                $href = pq($a)->attr('href');
                $this->writeLog('minfaxue2',$href);
                $this->http_get($href);
                $count++;
                if($count>=20){
                    $stop = true;
                    break;
                }
            }
            if($stop)
            {
                break;
            }
            phpQuery::$documents = [];
        }
        if($count < 5)
        {
            $this->writeLog('minfaxue2','民法学2cookie已失效',true,false);
        }else{
            $this->writeLog('minfaxue2','民法学2点击了:'.$count.'次',true);
        }
    }

    public function xingshisusongfa()
    {
        if(!$this->check_log('xingshisusongfa'))
        {
            $this->writeLog('xingshisusongfa','刑事诉讼法已经学习了',true);
            return;
        }
        $link = 'http://sichuan.ouchn.cn/mod/page/view.php?id=359530';
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $count = 0;
        $as = pq('#m-course-c4-content1 a');
        foreach ($as as $a)
        {
            $href = pq($a)->attr('href');
            $this->writeLog('xingshisusongfa',$href);
            $this->http_get($href);
            $count++;
            if($count>=20){
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('xingshisusongfa','刑事诉讼法cookie已失效',true,false);
        }else{
            $this->writeLog('xingshisusongfa','刑事诉讼法点击了:'.$count.'次',true);
        }
    }

    public function xingfaxue2()
    {
        if(!$this->check_log('xingfaxue2'))
        {
            $this->writeLog('xingfaxue2','刑法学2已经学习了',true);
            return;
        }
        $link = 'http://sichuan.ouchn.cn/course/view.php?id=384';
        $count = 0;
        $stop = false;
        for ($i = 0;$i<6;$i++)
        {
            $content = $this->http_get($link . '&section=' . ($i+1));
            phpQuery::newDocumentHTML($content);
            $as = pq('#section-'.($i+1).' a');
            foreach ($as as $a)
            {
                $href = pq($a)->attr('href');
                $this->writeLog('xingfaxue2',$href);
                $this->http_get($href);
                $count++;
                if($count>=20){
                    $stop = true;
                    break;
                }
            }
            if($stop)
            {
                break;
            }
            phpQuery::$documents = [];
        }
        if($count < 5)
        {
            $this->writeLog('xingfaxue2','刑法学2cookie已失效',true,false);
        }else{
            $this->writeLog('xingfaxue2','刑法学2点击了:'.$count.'次',true);
        }
    }

    public function huanjingbaohufa()
    {
        if(!$this->check_log('huanjingbaohufa'))
        {
            $this->writeLog('huanjingbaohufa','环境保护法已经学习了',true);
            return;
        }
        $link = 'http://sichuan.ouchn.cn/course/view.php?id=898';
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $count = 0;
        $stop = false;
        for ($i = 0;$i<13;$i++)
        {
            $as = pq('#section-'.$i.' a');
            foreach ($as as $a)
            {
                $href = pq($a)->attr('href');
                $this->writeLog('huanjingbaohufa',$href);
                $this->http_get($href);
                $count++;
                if($count>=20){
                    $stop = true;
                    break;
                }
            }
            if($stop)
            {
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('huanjingbaohufa','环境保护法cookie已失效',true,false);
        }else{
            $this->writeLog('huanjingbaohufa','环境保护法点击了:'.$count.'次',true);
        }
    }

    public function falvwenshu()
    {
        if(!$this->check_log('falvwenshu'))
        {
            $this->writeLog('falvwenshu','法律文书已经学习了',true);
            return;
        }
        $link = 'http://sichuan.ouchn.cn/course/view.php?id=1069';
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $count = 0;
        $stop = false;
        for ($i = 0;$i<8;$i++)
        {
            $as = pq('#section-'.$i.' a');
            foreach ($as as $a)
            {
                $href = pq($a)->attr('href');
                $this->writeLog('falvwenshu',$href);
                $this->http_get($href);
                $count++;
                if($count>=20){
                    $stop = true;
                    break;
                }
            }
            if($stop)
            {
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('falvwenshu','法律文书cookie已失效',true,false);
        }else{
            $this->writeLog('falvwenshu','法律文书点击了:'.$count.'次',true);
        }
    }

    public function chengbenkuaiji()
    {
        if(!$this->check_log('chengbenkuaiji'))
        {
            $this->writeLog('chengbenkuaiji','成本会计已经学习了',true);
            return;
        }
        $link = 'http://sichuan.ouchn.cn/course/view.php?id=2712';
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $count = 0;
        $as = pq('tr td:nth-child(2) a');
        foreach ($as as $a)
        {
            $href = pq($a)->attr('href');
            $this->writeLog('chengbenkuaiji',$href);
            $this->http_get($href);
            $count++;
            if($count>=20){
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('chengbenkuaiji','成本会计cookie已失效',true,false);
        }else{
            $this->writeLog('chengbenkuaiji','成本会计点击了:'.$count.'次',true);
        }
    }

    public function kuaijidiansuanhua()
    {
        if(!$this->check_log('kuaijidiansuanhua'))
        {
            $this->writeLog('kuaijidiansuanhua','会计电算化已经学习了',true);
            return;
        }
        $link = 'http://sichuan.ouchn.cn/course/view.php?id=2831&section=1';
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $count = 0;
        $as = pq('tr td:nth-child(2) a');
        foreach ($as as $a)
        {
            $href = pq($a)->attr('href');
            $this->writeLog('kuaijidiansuanhua',$href);
            $this->http_get($href);
            $count++;
            if($count>=20){
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('kuaijidiansuanhua','会计电算化cookie已失效',true,false);
        }else{
            $this->writeLog('kuaijidiansuanhua','会计电算化点击了:'.$count.'次',true);
        }
    }

    public function xifangjingjixue()
    {
        if(!$this->check_log('xifangjingjixue'))
        {
            $this->writeLog('xifangjingjixue','西方经济学已经学习了',true);
            return;
        }
        $link = 'http://sichuan.ouchn.cn/course/view.php?id=2756&section=1';
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $count = 0;
        $as = pq('tr td a');
        foreach ($as as $a)
        {
            $href = pq($a)->attr('href');
            $this->writeLog('xifangjingjixue',$href);
            $this->http_get($href);
            $count++;
            if($count>=20){
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('xifangjingjixue','西方经济学cookie已失效',true,false);
        }else{
            $this->writeLog('xifangjingjixue','西方经济学点击了:'.$count.'次',true);
        }
    }

    public function jingjifalvjichu()
    {
        if(!$this->check_log('jingjifalvjichu'))
        {
            $this->writeLog('jingjifalvjichu','经济法律基础已经学习了',true);
            return;
        }
        $link = 'http://sichuan.ouchn.cn/course/view.php?id=406';
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $count = 0;
        $as = pq('#inst86989 .barRow .progressBarCell');
        foreach ($as as $a)
        {
            $href = pq($a)->attr('onclick');
            $href = str_replace('document.location=','',$href);
            $href = trim($href,'\'');
            $this->writeLog('jingjifalvjichu',$href);
            $this->http_get($href);
            $count++;
            if($count>=20){
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('jingjifalvjichu','经济法律基础cookie已失效',true,false);
        }else{
            $this->writeLog('jingjifalvjichu','经济法律基础点击了:'.$count.'次',true);
        }
    }

    public function zhongjicaiwukuaiji2()
    {
        if(!$this->check_log('zhongjicaiwukuaiji2'))
        {
            $this->writeLog('zhongjicaiwukuaiji2','中级财务会计2已经学习了',true);
            return;
        }
        $link = 'http://sichuan.ouchn.cn/course/view.php?id=410';
        $content = $this->http_get($link);
        phpQuery::newDocumentHTML($content);
        $count = 0;
        $stop = false;
        for ($i = 0;$i<7;$i++)
        {
            $as = pq('#section-'.$i.' a');
            foreach ($as as $a)
            {
                $href = pq($a)->attr('href');
                $this->writeLog('zhongjicaiwukuaiji2',$href);
                $this->http_get($href);
                $count++;
                if($count>=20){
                    $stop = true;
                    break;
                }
            }
            if($stop)
            {
                break;
            }
        }
        phpQuery::$documents = [];
        if($count < 5)
        {
            $this->writeLog('zhongjicaiwukuaiji2','中级财务会计2cookie已失效',true,false);
        }else{
            $this->writeLog('zhongjicaiwukuaiji2','中级财务会计2点击了:'.$count.'次',true);
        }
    }

}

//$ouchn = new Ouchn();
//$cookie_faxue_file = 'D:\dy\py_work\selenium_work\cookie_faxue.txt';
//$cookie_kuaiji_file = 'D:\dy\py_work\selenium_work\cookie_kuaiji.txt';
//$img_file = 'D:\dy\py_work\selenium_work\photo.png';
//$code_file = 'D:\dy\py_work\selenium_work\code.txt';
//$count = 0;
//echo "等待获取cookie...\n";
//echo "等待获取图片...\n";
//while (true) {
//    if(file_exists($img_file)) {
//        require_once 'ouchn/code.php';
//        $v = new valite();
//        $v->setImage($img_file);
//        $v->getHec();
//        $code = $v->run();
//        file_put_contents($code_file,$code);
//        break;
//    }
//    sleep(1);
//}
//$ouchn->cookie = 'b_t_s_100300=98572d51-f500-45a2-8e0c-2b35d4d30afd; up_first_date=2018-06-10; Hm_lvt_a1d2fe485c96ce7e0b6dcb0d5ac8fb83=1528618405; up_beacon_user_id_100300=1751001459886; up_beacon_id_100300=98572d51-f500-45a2-8e0c-2b35d4d30afd-1540170715626; Hm_lvt_4dbd859086c9667369e2c2989fa278bc=1539652956,1539738562,1540022543,1540170716; CheckCode=NtzulZB1Ps4=; up_page_stime_100300=1540170975334; up_beacon_vist_count_100300=2; Hm_lpvt_4dbd859086c9667369e2c2989fa278bc=1540170976; MoodleSession=8pt6osuofj49ob7a3er7e7grs7';

//if(file_exists($cookie_faxue_file)) {
//    $ouchn->cookie = file_get_contents($cookie_faxue_file);
//    echo '获取法学cookie成功,开始点击...'.PHP_EOL;
//    $ouchn->index('faxue');
//    unlink($cookie_faxue_file);
//    $count++;
//    if($count >= 2) {
//        exit();
//    }
//}
//if(file_exists($cookie_kuaiji_file)) {
//    $ouchn->cookie = file_get_contents($cookie_kuaiji_file);
////        $ouchn->cookie = "moodle_course_format_tabtopics_course_1414=1; b_t_s_100300=98572d51-f500-45a2-8e0c-2b35d4d30afd; up_first_date=2018-06-10; Hm_lvt_a1d2fe485c96ce7e0b6dcb0d5ac8fb83=1528618405; up_beacon_user_id_100300=1751001459886; up_beacon_id_100300=98572d51-f500-45a2-8e0c-2b35d4d30afd-1555914240347; Hm_lvt_4dbd859086c9667369e2c2989fa278bc=1554974878,1555914242; firstVist=ture; banner=%3Cimg%20src%3D%22http%3A//sichuan.ouchn.cn/pluginfile.php/840266/course/section/65741/kuaiji.jpg%22%20height%3D%22233%22%20width%3D%22997%22%3E; CheckCode=76UZTDfKZAw=; up_page_stime_100300=1555919961268; up_beacon_vist_count_100300=3; Hm_lpvt_4dbd859086c9667369e2c2989fa278bc=1555919961; MoodleSession=v0t9ha5nuh9pjhmvf6gcuq7e36";
//    echo '获取会计cookie成功,开始点击...'.PHP_EOL;
//    $ouchn->index('kuaiji');
//    unlink($cookie_kuaiji_file);
//    $count++;
//    if($count >= 2) {
//        exit();
//    }
//}
