<?php
/**
*验证码自动识别
**/
class valite
{
    protected $DataArray;//二值化信息
    protected $ImageSize;//图片点阵信息
    protected $ImagePath;
    protected $data;//匹配后的数据
    protected $Keys;//特征码数组
    public $c1;//字符1
    public $c2;//字符二
    public $c3;//字符三
    public $c4;//字符四
    /*设置图片路径*/
    public function setImage($Image)
    {
        $this->ImagePath = $Image;
    }
    /*得到匹配结果*/
    public function getData()
    {
        return $this->data;
    }
    /*得到二值化数组*/
    public function getResult()
    {
        return $this->DataArray;
    }
    /*核心操作 验证码处理部分*/
    public function getHec()
    {
        /*读取图片*/
        $res = imagecreatefrompng($this->ImagePath);
        /*得到图片点阵信息*/
        $size = getimagesize($this->ImagePath);
        //size[0]为图片高，size[1]为图片宽
        /*定义用于接收二值化后的数组*/
        $data = array();
        $l=array();
        /*根据验证码的RGB信息对图片进行二值化--starts*/
        for($i=0; $i < $size[1]; ++$i)
        {
            for($j=0; $j < $size[0]; ++$j)
            {
                $rgb = imagecolorat($res,$j,$i);//取得某位置的RGB值
                // echo $rgb."<br>";
                $rgbarray = imagecolorsforindex($res, $rgb);//得到该点的RGB信息数组
                /*此处的判断需根据具体情况，在此是背景的判断是RGB三个值同时小于150*/
                if($rgbarray['red'] > 150 && $rgbarray['green']>150
                    && $rgbarray['blue'] > 150)
                {
//                    echo "0";
                    $data[$i][$j]=0;//设置背景为0
                }else{
//                    echo "-";
                    $data[$i][$j]=1;//设置背景为1
                }
            }
//            echo "<br>";
        }

//        echo "<br>";

        /*根据验证码的RGB信息对图片进行二值化--ends*/

        /*排除噪点--starts*/
        for($i=0; $i < $size[1]; ++$i)
        {
            for($j=0; $j < $size[0]; ++$j)
            {
                $num = 0;
                if($data[$i][$j] == 1)
                {
                    // 上
                    if(isset($data[$i-1][$j])){
                        $num = $num + $data[$i-1][$j];
                    }
                    // 下
                    if(isset($data[$i+1][$j])){
                        $num = $num + $data[$i+1][$j];
                    }
                    // 左
                    if(isset($data[$i][$j-1])){
                        $num = $num + $data[$i][$j-1];
                    }
                    // 右
                    if(isset($data[$i][$j+1])){
                        $num = $num + $data[$i][$j+1];
                    }
                    // 上左
                    if(isset($data[$i-1][$j-1])){
                        $num = $num + $data[$i-1][$j-1];
                    }
                    // 上右
                    if(isset($data[$i-1][$j+1])){
                        $num = $num + $data[$i-1][$j+1];
                    }
                    // 下左
                    if(isset($data[$i+1][$j-1])){
                        $num = $num + $data[$i+1][$j-1];
                    }
                    // 下右
                    if(isset($data[$i+1][$j+1])){
                        $num = $num + $data[$i+1][$j+1];
                    }
                }
                if($num < 2){
                    $data[$i][$j] = 0;//如果该点周围的8个点钟同为1的数目小于3则认为其是噪点
                }
            }
//            echo "<br>";
        }
        /*排除噪点--ends*/

        /*得到行与列的数组，为了进行字符的分割--starts*/
        for($i=0; $i < $size[1]; ++$i)
        {
            for($j=0; $j < $size[0]; ++$j)
            {
                $l[$j][]=$data[$i][$j];
                $h[$i][]=$data[$i][$j];
            }
        }
        /*得到行与列的数组，为了进行字符的分割--ends*/
        // var_dump($l[5])."<br>";
        // var_dump($h[0]);

        /*列分割的处理，4个字符，8个角标--starts*/
        $fgl=array();
        foreach($l as $k=>$v){
            $n=count($fgl);
            // echo $n."<br>";
            switch ($n) {
                case 0:
                    if(in_array(1, $v)&&in_array(1, $l[$k+2])){
                        $fgl[]=$k;
                    }
                    break;
                case 1:
                    if(in_array(1, $v)||in_array(1, $l[$k+2])){

                    }else{
                        $fgl[]=$k;
                    }
                    break;
                case 2:
                    if(in_array(1, $v)&&in_array(1, $l[$k+2])){
                        $fgl[]=$k;
                    }
                    break;
                case 3:
                    if(in_array(1, $v)||in_array(1, $l[$k+2])){

                    }else{
                        $fgl[]=$k;
                    }
                    break;
                case 4:
                    if(in_array(1, $v)&&in_array(1, $l[$k+2])){
                        $fgl[]=$k;
                    }
                    break;
                case 5:
                    if(in_array(1, $v)||in_array(1, $l[$k+2])){

                    }else{
                        $fgl[]=$k;
                    }
                    break;
                case 6:
                    if(in_array(1, $v)&&in_array(1, $l[$k+2])){
                        $fgl[]=$k;
                    }
                    break;
                case 7:
                    if(in_array(1, $v)||in_array(1, $l[$k+2])){

                    }else{
                        $fgl[]=$k;
                    }
                    break;
            }
        }
        /*列分割的处理，4个字符，8个角标--ends*/


        /*横向分割的处理，去除字符上下的空白，得到每个字符点阵数组--starts*/
        foreach ($h as $k => $v) {
            $h1[]=array_slice($v,$fgl[0],$fgl[1]-$fgl[0]+1);
            $h2[]=array_slice($v,$fgl[2],$fgl[3]-$fgl[2]+1);
            $h3[]=array_slice($v,$fgl[4],$fgl[5]-$fgl[4]+1);
            $h4[]=array_slice($v,$fgl[6],$fgl[7]-$fgl[6]+1);
        }
        $hl1=array();
        $hl2=array();
        $hl3=array();
        $hl4=array();
        foreach($h1 as $k=>$v){
            $n=count($hl1);
            $m=count($h4)-1;
            $min=min($k+2,$m);
            switch ($n) {
                case 0:
                    if(in_array(1, $v)&&in_array(1, $h1[$min])){
                        $hl1[]=$k;
                    }
                    break;
                case 1:
                    if(in_array(1, $v)||in_array(1, $h1[$min])){

                    }else{
                        $hl1[]=$k;
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }
        foreach($h2 as $k=>$v){
            $n=count($hl2);
            $m=count($h4)-1;
            $min=min($k+2,$m);
            switch ($n) {
                case 0:
                    if(in_array(1, $v)&&in_array(1, $h2[$min])){
                        $hl2[]=$k;
                    }
                    break;
                case 1:
                    if(in_array(1, $v)||in_array(1, $h2[$min])){

                    }else{
                        $hl2[]=$k;
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }
        foreach($h3 as $k=>$v){
            $n=count($hl3);
            $m=count($h4)-1;
            $min=min($k+2,$m);
            switch ($n) {
                case 0:
                    if(in_array(1, $v)&&in_array(1, $h3[$min])){
                        $hl3[]=$k;
                    }
                    break;
                case 1:
                    if(in_array(1, $v)||in_array(1, $h3[$min])){

                    }else{
                        $hl3[]=$k;
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }
        foreach($h4 as $k=>$v){
            $n=count($hl4);
            $m=count($h4)-1;
            $min=min($k+2,$m);
            switch ($n) {
                case 0:
                    if(in_array(1, $v)&&in_array(1, $h4[$min])){
                        $hl4[]=$k;
                    }
                    break;
                case 1:
                    if(in_array(1, $v)||in_array(1, $h4[$min])){

                    }else{
                        $hl4[]=$k;
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }
        /*横向分割的处理，去除字符上下的空白，得到每个字符点阵数组--ends*/


        /*将得到的四个字符的点阵数组转化为特征串--starts*/
        $c1="";$c2="";$c3="";$c4="";
        for ($i=$hl1[0]; $i <$hl1[1] ; $i++) {
            for($j=$fgl[0];$j<$fgl[1];$j++){
                $c1.=$data[$i][$j];
            }
        }
        for ($i=$hl2[0]; $i <$hl2[1] ; $i++) {
            for($j=$fgl[2];$j<$fgl[3];$j++){
                $c2.=$data[$i][$j];
            }
        }
        for ($i=$hl3[0]; $i <$hl3[1] ; $i++) {
            for($j=$fgl[4];$j<$fgl[5];$j++){
                $c3.=$data[$i][$j];
            }
        }
        for ($i=$hl4[0]; $i <$hl4[1] ; $i++) {
            for($j=$fgl[6];$j<$fgl[7];$j++){
                $c4.=$data[$i][$j];
            }
        }
        /*将得到的四个字符的点阵数组转化为特征串--ends*/
//        echo $c1."<br>";
//        echo $c2."<br>";
//        echo $c3."<br>";
//        echo $c4."<br>";


        $this->c1=$c1;
        $this->c2=$c2;
        $this->c3=$c3;
        $this->c4=$c4;
        $this->DataArray = $data;
        $this->ImageSize = $size;
    }
    /*根据自己的特征库匹配最佳结果*/
    public function run()
    {
        $result="";
        $data[]=$this->c1;
        $data[]=$this->c2;
        $data[]=$this->c3;
        $data[]=$this->c4;
        foreach($data as $numKey => $numString)
        {
            $max=0.0;
            $num = 0;
            foreach($this->Keys as $key => $value)
            {
                $percent=0.0;
                similar_text($key, $numString,$percent);
                if(intval($percent) > $max)
                {
                    $max = $percent;
                    $num = $value;
                    if(intval($percent) > 95)
                        break;
                }
            }
            $result.=$num;
        }
        $this->data = $result;
        return $result;
    }
    public function __construct()
    {
        /*初始化你的特征串数组，越多越精确，需要你自己丰富*/
        $this->Keys = array(
            '000111000011111110011000110110000011110000011110000011110000011110000011110000011110000011011000110011111110000111000'=>0,
            '00011111000011100011100111000111111110000111111000001111110000011111110000111111100011110111000111000011111000'=>0,
            '00011110000011111100011110111001110001100111000111011000011101100001110110000111111000011101110001100111000110011100111000111111000001111000'=>0,
            '001111000011111100111001110111000110111000111110000111110000111110000111110000111111000111111000110011001110011111100001111000'=>0,
            '0011110001111110111111111110011111100111110000111100001111000011110000111100001111100111111001110111111000111100'=>0,
            '000111100001111110011111111011000011011000011011111111011000011011100111011100111111000011011000011011101111001111110000111100'=>0,
            '0001111000001100110001100001100110000110111000011111100001111110000111111000011111100001111110000111111000011111100001111110000110011000011000110011000001111000'=>0,
            '00111110000111111100111101111011100011101110001110111000111011100011101110001111111000111011101111101111011110111101111001111111000011111000'=>0,
            '001111100011111110111001110111000111111000111111000111111000111111000111111000111111000111111000111111101110011111110001111100'=>0,
            '000111110000011100011100111100011110111100101111111100011110111100011111111100011110111100011110011110111100000111111000'=>0,
            '000111111000011110011100011100011110111100001110111100001111111100001111111100001111111100011110011110011100001111111000'=>0,
            '00011111000011111110011110111101110011110111000111111100011111110001111111000111111100011101110001110111001111011100111000111111100001111100'=>0,
            '001110001110011110001110011110001111011100000111111100000111011110001111011110001110001110001110000011111000000000100000'=>0,

            '000111000011111000011111000000011000000011000000011000000011000000011000000011000000011000000011000011111111011111111'=>1,
            '0001100001111001111100111110001111000111000011100001111001111111111111'=>1,
            '00001110001111001111111111111110111110011100001110000111000011100001110000111000011100001110000111'=>1,
            '000011001111011111111111111111110111000011000111000111000111000111000111000111000011'=>1,
            '01100111000110001100011000110001100011000110001100011000110001100011000111011111'=>1,
            '0011111100010000011011000001101110000111111000011000000001100000000110000000110000000110000000110000000110000000110000000110000011110000001111111111101111111110'=>2,
            '0011111100111001111011110011101110001110000001111000001110000011000000111111111111111111111111111111'=>2,
            '000111111000011110111110011110011110011110011110000000011100000000000000000110000100001111111110111111111111111111111111'=>2,
            '01111111100111101111101111001111011110011110000001111000000111000000111000000111111111101111111111011111111111'=>2,
            '00111111000011111110011111111101110001110000000111000000111100000111110000011110000011111000111110000011111000011111000001111111111111111111'=>2,
            '0011111100111001111011110011101110001111000000111000000000000011000000011111111111111111111111111111'=>2,
            '011111100011111110111111111111000111000000111000001111000011111000011110000111110011111000011111000111110000111111111111111111'=>2,
            '00011111000111111110011100111101110001110000000111000000111100000011100000111110000111110000111111000011110000011111110011111111111111111111'=>2,

            '011111000111111110100000110000000110000001100011111000011111100000001110000000111000000110100001110111111100011111000'=>3,
            '01111111001111011111111100111111110011110000001111000001111000001111000000001111000000111111100011111110001111111001111101111111000000010000'=>3,
            '00111111100011100111100111000111001110101111000010111100000001110000000111000000000111100000000111111100001111111100011111111001111000111111000'=>3,
            '00011111100011100111110111100111101110001111000000011100000001111000000111000000000011100000000111101110001111111100011110111001111100111111110'=>3,
            '001111000011111100111001110111001110000001110000111100000111100000001110000000111000000111111000111111001110011111110001111000'=>3,
            '0011110001111110111001111110011100000111001111000001111000011111000001110100001111000011111001110111111001111100'=>3,
            '00011110000011100111000111000111001100001110000000111100000011110000001111100000000111100000000111001100001111111100111101111011110001111111100'=>3,
            '001111100011111110111101111111001111000011110000111110000111110000001111000000111000000111111001111111101111011111110001111100'=>3,
            '0011111100010000011011100001100110000110000000011000000011000000011000000011100000000001100000000110000000001101100000111110000111110000011001100011000011111000'=>3,

            '000001100000011100000011100000111100001101100001101100011001100011001100111111111111111111000001100000001100000001100'=>4,
            '00000011000000011100000011111000001111100001111110001111110000110111100111011110111001111011111111111111111111000001111000000011100000001100'=>4,
            '00000011000000011100000011110000001111100001111110000110111000110011100111001110011000111011111111111111111111000000111000000011100000001100'=>4,
            '00000011100000001111000000011111000000111110000000111100000001111000000011100011000111101111111111111111111111000000111100000000110000000001100'=>4,

            '111111110111111110110000000110000000110000000111110000111111100000001110000000111000000110100001110111111100011111000'=>5,
            '00111111111001111111100011111111000100000000011000000000011111110001100011110000000011100000000111101110001111111100011100111001111000111111000'=>5,
            '011111110011111110011100000011000000111111100111111110111001111000000111000000111000000111111000111111001110011111110001111100'=>5,
            '001111110011111110011100000011000000111111100111111110111001110000000111000000111000000111111000111111001110011111110001111000'=>5,
            '000100000011111110011111110111110000111110000111111100111111110111111110000001111000000111110000111111001110111011110111111110011111100'=>5,
            '011111111011111111011100000011100000111111100111111110111001111000000111000000111000000111111000111111001111011111110001111100'=>5,
            '0011111110001111111100111111110110000000011000000001111111000010011110000000111100000001110110001111111100111101110111100011111100'=>5,
            '001111111101111111110011111111'=>5,
            '0111111111011111111101111111110100000000010000000011111111001110011111000000111100000011111110001111111000111111100111100111111100'=>5,
            '011111111100111111111001100000110011000001100110000011001100000000011000000000110111110001111111110011100001110110000001100000000011000000000110000000001111000000011111000001110111111111000111111100'=>5,

            '000111100001111110011000010011000000110000000110111100111111110111000111110000011110000011011000111011111110000111100'=>6,
            '00000011100000111100000011100000001111000000011100000001111111110011110011110111100011111111000111111110001111111100011110111001110000011111000'=>6,
            '0011110011111110111001111110001111111110111111101110011111100111111001111110001111100011111111110111111000111100'=>6,
            '00000011100000011100000011110000001111000000011100010001111111110011111011110111100011111111000111111110001111111100011110111100111000111111100'=>6,
            '001111100011111110111001110111000000111111100111111110111101110111000111111000111111000111111000111111101110011111110001111000'=>6,
            '001111100011111110111101111111000000111111100111111110111111111111001111111000111111000111111000111111101111011111110001111100'=>6,
            '001111100011111110111001111111000000111111100111111110111101110111000111111000111111000111111000111111101110011111110001111100'=>6,
            '001111100011111110011001111111000000111111100111111110111101110111000111111000111111000111111000111111101111011111110001111100'=>6,
            '000111111000011111111001110000111011000000111110000000011000000000110011110001101111110011110001110111000001111100000001111000000011110000000111100000001111100000111011100011100011111110000011111000'=>6,
            '00000011100000111100000011110000001111000000011100000001111111110011111001110111100011111111000111111110001111111100011110111101111000111111000'=>6,
            '0000001110000011100000111000000111000000011100000011111111101111000111111000011111100001111110000111111100011101110001100011111100'=>6,
            '00000011100000111100000011100000001110000000011100000001111111110011110011110111100011111111000111111110001111111100011110111001110000011111000'=>6,
            '00011111000011111110011110111001110000000111111100011111111001111011110111000111111100011101110001110111000111011110111100111111100001111100'=>6,

            '011111111011111111000000011000000010000000110000001100000001000000011000000010000000110000000110000001100000001100000'=>7,
            '1111111111111111111011111111101000000100100000110000000010000000011000000001000000001000000001100000000100000000110000000010000000'=>7,
            '1111111111111111000011110000111000001110000111000011100000111000001110000111100001110000011100000111000000110000'=>7,
            '1111111111111111000011110000111000001110000111000011100000111000001110000111000001110000011100000111000000110000'=>7,
            '111111111111111111000001110000001100000011100000011000000111000000110000001110000001110000001110000001100000001100000011100000'=>7,
            '111111111111111111000001111000001110000011100000011100000111000000111000001111000001110000001110000001110000001110000001100000'=>7,
            '111111111111111111000001110000001110000011100000011000000111000000110000001110000001110000001110000001100000001100000011100000'=>7,
            '01111111111111111111000000111000000111000000011100000011100000001110000001111000000111000000011100000001110000001110000000111000000011100000'=>7,
            '1111111111011111111101111111110100000110010000011000000001000000001100000000100000000100000000110000000010000000011000000001000000'=>7,
            '1111111111111111111011111111101000000100100000010000000010000000011000000001000000001000000001100000000100000000110000000010000000'=>7,
            '111111111111111111111111111100000010'=>7,

            '001111100011111110011000110011000110011101110001111100001111100011101110110000011110000011111000111011111110001111100'=>8,
            '001111100010000010110000011110000011110000011111000010011100100001111000000111000001111110011000110110000011110000011110000001110000001110000011011000010000111100'=>8,
            '001111100011111110111101110111001111111001111111101110011111110011111110111001111111000111111000111111101111111111110001111100'=>8,
            '00011111100011110011100111000111111110001111111110011100111111111000111111110011101111111111000111111110000111111100011110111000111000111111100'=>8,
            '001111100011111110111101111111001111111001111111101111011111110011111110111101111111000111111000111111001111111111110001111100'=>8,
            '00011111000011100011100111000111011110001110011110011100111111110000111111100011101111101110000111111100001111111000001111111000111000111111000'=>8,
            '001111111100011110011110011110011110011100001110011111111100001111111100001111111100001111111110011100001111111100001111011100001111011110001110000111111100000000110000'=>8,
            '001111100011111110111001110111000110111000110111001110011111100011111100111001110110000111111000111111001110111111110001111100'=>8,
            '000110000111111001111110111001111110011111100111111001111111111011111111111111111100001111100111111111111111111101111110'=>8,
            '001111111000011100011110011100011110111100011110011110011100011111111000001111111100011101111110111110011110111100001111111100001110011100011100001111111000'=>8,
            '00111110000111111100111101111011110111101111011110011101110001111110000111111100111001111011100011111110001111111111111001111111000011111000'=>8,
            '00011001100011100011101111000111111110001111111110011100111111110000011111100011101111111110000111111100000111111100001111111000111000111000000'=>8,
            '00011111100011100111101111000111011110001111111110011100111111110000111111100011101111101110000111111100001111111000011111111000111000111000000'=>8,
            '00011000000011100011101111000111011110001110011110011100111111110000111111100011101111101110000111111100001111111100001111111000111000111000000'=>8,
            '00011110000011111100011111111001100011100111001110011100111000111111000111111110011100111011100001111110000111011100111001111111100001111100'=>8,
            '00111111100011111111101111111111111111011111111110111111111101111111111011111111111111100111111110011111111111111110111111111101111111111011111111110111111111101111111111011111111111111110111111111000111111100'=>8,

            '001111000011111110111000111110000011110000011111000111011111111001111011000000011000000110010000110011111100001111000'=>9,
            '00111111100011111111101111111111111111011111111110111111111101111111111011111111110111111111101111111111011111111111111111111111111101111111111000000111111111101111111111011111111111111110111111111000111111100'=>9,

        );
    }
}
//for($p=0;$p<10;$p++)
//{
//    $im = imagecreatefrompng('http://sso.ouchn.cn/ashx/CheckCode.ashx');
//    imagepng($im,'tmp'.$p.'.png');
//    $img="tmp".$p.".png";//验证码地址
//    $v=new valite();
//    // $val=file_get_contents($img);
//    // file_put_contents(SAE_TMP_PATH.'1.jpg',$val);//写入SAE临时文件
//    $v->setImage($img);//传输该图片
//    $v->getHec();
//    $res= $v->run();//结果/*这个验证码的识别率在95%以上，如果对于有字母的需要为其扩充特征串类，对于有复杂变化的还需要进行其他处理*/
//    echo $res."<br>";
//}