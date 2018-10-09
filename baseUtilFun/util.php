<?php
/**
 * 生成随机数
 * @param int $len
 * @return string
 */
function getRandom($len = 6)
{
    $arr = range(0,9);
    $str = "";
    for ($i = 0;$i<$len;$i++)
    {
        $str .= array_rand($arr);
    }
    return $str;
}

/**
 * 随机字符串
 * @param int $len
 * @return string
 */
function randomStr($len = 32)
{
    $chars = "abcdefghijklmnopqrstuvwxyz";
    $shuffle = str_shuffle($chars);
    $result = '';
    for ($i=0;$i<$len;$i++)
    {
        $index = mt_rand(0,strlen($chars));
        $result .= substr($shuffle,$index,1);
    }
    return $result;
}


/**
 * 根据经纬度和半径计算出范围
 * @param string $lat 经度
 * @param String $lng 纬度
 * @param float $radius 半径
 * @return array 范围数组
 */
function calcScope($lat, $lng, $radius){
    $degree = (24901*1609)/360.0;
    $dpmLat = 1/$degree;

    $radiusLat = $dpmLat*$radius;
    $minLat = $lat - $radiusLat;       // 最小经度
    $maxLat = $lat + $radiusLat;       // 最大经度

    $mpdLng = $degree*cos($lat * (pi()/180));
    $dpmLng = 1 / $mpdLng;
    $radiusLng = $dpmLng*$radius;
    $minLng = $lng - $radiusLng;      // 最小纬度
    $maxLng = $lng + $radiusLng;      // 最大纬度

    /** 返回范围数组 */
    $scope = array(
        'minLat'    =>  $minLat,
        'maxLat'    =>  $maxLat,
        'minLng'    =>  $minLng,
        'maxLng'    =>  $maxLng
    );
    return $scope;
}

/**
 * 获取两个经纬度之间的距离
 * @param  string $lat1 经一
 * @param  String $lng1 纬一
 * @param  String $lat2 经二
 * @param  String $lng2 纬二
 * @return float  返回两点之间的距离
 */
function calcDistance($lat1, $lng1, $lat2, $lng2) {
    /** 转换数据类型为 double */
    $lat1 = doubleval($lat1);
    $lng1 = doubleval($lng1);
    $lat2 = doubleval($lat2);
    $lng2 = doubleval($lng2);
    /** 以下算法是 Google 出来的，与大多数经纬度计算工具结果一致 */
    $theta = $lng1 - $lng2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    //距离计算出来单位为英米
    $miles = $dist * 60 * 1.1515;
    //返回结果为米 整数
    return ceil($miles * 1609.344);
}

/**
 * 强制保留两位小数
 */
sprintf("%.2f", 1);//1.00

/**
 * 多维数组排序
 * @param object data 原始数据
 * @param string sort_order_field 排序依据的字段
 * @param int $sort_order 升序or降序
 * @param int $sort_type 排序字段类型
 * @return mixed 返回排序后的数据
 */
function my_array_multisort($data,$sort_order_field,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC){
    if(empty($data))
    {
        return $data;
    }
    foreach($data as $val){
        $key_arrays[]=$val[$sort_order_field];
    }
    array_multisort($key_arrays,$sort_order,$sort_type,$data);
    return $data;
}


/*
    *计算概率
    *作者：杜勇
    *时间：2017 2 15
    * 传入 构造的array 设置的概率和对应的说明
 * $award = [
 *      ['v'=>0.5,'m'=>小熊玩具一个],
 *      ['v'=>0.5,'m'=>玩具汽车一辆],
 * ];
    * 返回选中的k
    */
function chance($award){
    $r = randomFloat(1,100);
    $num = 0;
    foreach ($award as $k => $v) {
        $tmp = $num;
        $num += $v["v"]*100;
        if($r > $tmp && $r <= $num){
            return $k;
        }
    }
    return false;
}

function randomFloat($min = 0, $max = 1) {
    return round($min + mt_rand() / mt_getrandmax() * ($max - $min),2);
}
//================================微信开发start=========================================
/**
 * 获取access_token(全局)
 * @return mixed
 */
function access_token()
{
    $access_token = cache('access_token');
    if(empty($access_token)) {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . config('appid') . "&secret=" . config('appsecret');
        $res = json_decode(file_get_contents($url),true);
        cache('access_token',$res['access_token'],$res['expires_in']);
        return $res['access_token'];
    }else{
        return $access_token;
    }
}

/**
 * 获取jsapi_ticket(全局)
 * @return mixed
 */
function ticket()
{
    $ticket = cache('ticket');
    if(empty($ticket)) {
        $access_token = access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $access_token . "&type=jsapi";
        $res = json_decode(file_get_contents($url), true);
        cache('ticket', $res['ticket'], 7190);
        return $res['ticket'];
    }else{
        return $ticket;
    }
}

/**
 * js_sdk签名
 * @param $noncestr
 * @param $jsapi_ticket
 * @param $timestamp
 * @param $url
 * @return string
 */
function sign($noncestr,$jsapi_ticket,$timestamp,$url)
{
    $data['noncestr'] = $noncestr;
    $data['jsapi_ticket'] = $jsapi_ticket;
    $data['timestamp'] = $timestamp;
    $data['url'] = $url;
    ksort($data);
    $param = '';
    $count = 0;
    foreach ($data as $k=>$v)
    {
        if($count != 0)
        {
            $param .= '&';
        }
        $param .= $k.'='.$v;
        $count = 1;
    }
    return sha1($param);
}

//回调code处理
function getWxUserInfo($param = [])
{
    $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".config('appid').
        "&secret=".config('appsecret')."&code=".$param['code'].
        "&grant_type=authorization_code";
    $res = file_get_contents($url);
    $res = json_decode($res,true);
    $_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$res['access_token']."&openid=".$res['openid']."&lang=zh_CN";
    $userInfo = json_decode(file_get_contents($_url),true);

    $info = $this->modelUser->where('u_openid',$userInfo['openid'])->find();
    if(empty($info))
    {
        $this->Userreg($userInfo);
    }
    $this->Userlogin($userInfo);
    $header_url = url("index/index/index");
    header("Location:" . $header_url);exit();
}
//=======================微信开发end=======================================