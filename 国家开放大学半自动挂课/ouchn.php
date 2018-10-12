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

    public function faxue()
    {
        $this->minfaxue1();
        $this->minfaxue2();
        $this->xingshisusongfa();
        $this->xingfaxue2();
        $this->huanjingbaohufa();
        $this->falvwenshu();
    }

    public function kuaiji()
    {
        $this->chengbenkuaiji();
        $this->kuaijidiansuanhua();
        $this->xifangjingjixue();
        $this->jingjifalvjichu();
        $this->zhongjicaiwukuaiji2();
    }

    public function writeLog($log_name,$content,$is_end = false,$is_write = true)
    {
        if($is_write) {
            $log_txt = dirname(__FILE__) . '/ouchn_log/' . $log_name . '.log';
            file_put_contents($log_txt, '|time:' . time() . '|时间:' . date('Y-m-d H:i:s', time()) . PHP_EOL, FILE_APPEND);
            file_put_contents($log_txt, $content . PHP_EOL, FILE_APPEND);
        }
        if($is_end) {
            echo $log_name . ': ' . PHP_EOL;
            echo $content . PHP_EOL;
            echo PHP_EOL;
        }
    }

    public function check_log($log_name)
    {
        $log_txt = dirname(__FILE__) . '/ouchn_log/'.$log_name.'.log';
        if(file_exists($log_txt))
        {
            return false;
        }
        return true;
    }

    function http_get($url)
    {
        $cookie = dirname(__FILE__).'/cookie.txt';
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'
        ];
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'GET');
        curl_setopt($ch,CURLOPT_HEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
//        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        if(!empty($this->cookie)) {
            if(!file_exists($cookie))
            {
                curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
            }
        }
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
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
                    $this->writeLog('minfaxue1','已经点了20个了',true);
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
                    $this->writeLog('minfaxue2','已经点了20个了',true);
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
                $this->writeLog('xingshisusongfa','已经点了20个了',true);
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
                    $this->writeLog('xingfaxue2','已经点了20个了',true);
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
                    $this->writeLog('huanjingbaohufa','已经点了20个了',true);
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
                    $this->writeLog('falvwenshu','已经点了20个了',true);
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
                $this->writeLog('chengbenkuaiji','已经点了20个了',true);
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
                $this->writeLog('kuaijidiansuanhua','已经点了20个了',true);
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
                $this->writeLog('xifangjingjixue','已经点了20个了',true);
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
                $this->writeLog('jingjifalvjichu','已经点了20个了',true);
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
                    $this->writeLog('zhongjicaiwukuaiji2','已经点了20个了',true);
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

$ouchn = new Ouchn();
$ouchn->cookie = 'b_t_s_100300=98572d51-f500-45a2-8e0c-2b35d4d30afd; up_first_date=2018-06-10; Hm_lvt_a1d2fe485c96ce7e0b6dcb0d5ac8fb83=1528618405; up_beacon_id_100300=98572d51-f500-45a2-8e0c-2b35d4d30afd-1539308107249; Hm_lvt_4dbd859086c9667369e2c2989fa278bc=1538990886,1539054129,1539221274,1539308108; CheckCode=4VEZF0d47ko=; up_beacon_user_id_100300=1751001459734; up_page_stime_100300=1539320645796; up_beacon_vist_count_100300=4; Hm_lpvt_4dbd859086c9667369e2c2989fa278bc=1539320646; MoodleSession=889kc78275fdgf1f1ube5kltb3';
$ouchn->index('faxue');