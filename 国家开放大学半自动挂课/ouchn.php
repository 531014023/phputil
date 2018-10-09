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
    public $cookie = 'b_t_s_100300=98572d51-f500-45a2-8e0c-2b35d4d30afd; up_first_date=2018-06-10; Hm_lvt_a1d2fe485c96ce7e0b6dcb0d5ac8fb83=1528618405; up_beacon_id_100300=98572d51-f500-45a2-8e0c-2b35d4d30afd-1539054128889; Hm_lvt_4dbd859086c9667369e2c2989fa278bc=1538990398,1538990402,1538990886,1539054129; CheckCode=YN7uBgXOIm0=; up_beacon_user_id_100300=1751001459886; MoodleSession=nfo15vveudn6ggbk3ujl14sdq6; up_page_stime_100300=1539058296293; up_beacon_vist_count_100300=3; Hm_lpvt_4dbd859086c9667369e2c2989fa278bc=1539058296';
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

    public function index()
    {
        $this->http_get('http://shome.ouchn.cn/');
    }

    public function duyong()
    {
        $this->minfaxue1();
        $this->minfaxue2();
        $this->xingshisusongfa();
        $this->xingfaxue2();
        $this->huanjingbaohufa();
        $this->falvwenshu();
    }

    public function caihong()
    {
        $this->chengbenkuaiji();
        $this->kuaijidiansuanhua();
        $this->xifangjingjixue();
        $this->jingjifalvjichu();
        $this->zhongjicaiwukuaiji2();
    }

    public function writeLog($log_name,$content)
    {
        $log_txt = dirname(__FILE__) . '/ouchn_log/'.$log_name.'.log';
        file_put_contents($log_txt,'|time:'.time().'|时间:'.date('Y-m-d H:i:s',time()).PHP_EOL,FILE_APPEND);
        file_put_contents($log_txt,$content.PHP_EOL,FILE_APPEND);
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
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
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
                    $this->writeLog('minfaxue1','已经点了20个了');
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
    }

    public function minfaxue2()
    {
        if(!$this->check_log('minfaxue2'))
        {
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
                    $this->writeLog('minfaxue2','已经点了20个了');
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
    }

    public function xingshisusongfa()
    {
        if(!$this->check_log('xingshisusongfa'))
        {
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
                $this->writeLog('xingshisusongfa','已经点了20个了');
                break;
            }
        }
        phpQuery::$documents = [];
    }

    public function xingfaxue2()
    {
        if(!$this->check_log('xingfaxue2'))
        {
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
                    $this->writeLog('xingfaxue2','已经点了20个了');
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
    }

    public function huanjingbaohufa()
    {
        if(!$this->check_log('huanjingbaohufa'))
        {
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
                    $this->writeLog('huanjingbaohufa','已经点了20个了');
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
    }

    public function falvwenshu()
    {
        if(!$this->check_log('falvwenshu'))
        {
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
                    $this->writeLog('falvwenshu','已经点了20个了');
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
    }

    public function chengbenkuaiji()
    {
        if(!$this->check_log('chengbenkuaiji'))
        {
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
                $this->writeLog('chengbenkuaiji','已经点了20个了');
                break;
            }
        }
        phpQuery::$documents = [];
    }

    public function kuaijidiansuanhua()
    {
        if(!$this->check_log('kuaijidiansuanhua'))
        {
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
                $this->writeLog('kuaijidiansuanhua','已经点了20个了');
                break;
            }
        }
        phpQuery::$documents = [];
    }

    public function xifangjingjixue()
    {
        if(!$this->check_log('xifangjingjixue'))
        {
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
                $this->writeLog('xifangjingjixue','已经点了20个了');
                break;
            }
        }
        phpQuery::$documents = [];
    }

    public function jingjifalvjichu()
    {
        if(!$this->check_log('jingjifalvjichu'))
        {
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
                $this->writeLog('jingjifalvjichu','已经点了20个了');
                break;
            }
        }
        phpQuery::$documents = [];
    }

    public function zhongjicaiwukuaiji2()
    {
        if(!$this->check_log('zhongjicaiwukuaiji2'))
        {
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
                    $this->writeLog('zhongjicaiwukuaiji2','已经点了20个了');
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
    }

}

$ouchn = new Ouchn();
//$ouchn->duyong();
$ouchn->caihong();