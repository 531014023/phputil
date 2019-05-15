<?php
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
//            echo "<br>".PHP_EOL;
        }

//        echo "<br>".PHP_EOL;

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
//        echo $c1.PHP_EOL;
//        echo $c2.PHP_EOL;
//        echo $c3.PHP_EOL;
//        echo $c4.PHP_EOL;


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
    public function __construct($file = null)
    {
        /*初始化你的特征串数组，越多越精确，需要你自己丰富*/
        $this->importKeys($file);
    }

    public function getKeys(){
        return $this->Keys;
    }

    public function setKeys($keys){
        $this->Keys = $keys;
    }

    public function saveKeys($file = null){
        if(!$file){
            $file = 'code_keys.txt';
        }
        file_put_contents($file,json_encode($this->Keys));
    }

    public function importKeys($file = null){
        if(!$file){
            $file = 'code_keys.txt';
        }
        $json = file_get_contents($file);
        $this->Keys = json_decode($json,true);
    }
}

function xunlian()
{
    for ($p = 0; $p < 1; $p++) {
        $im = imagecreatefrompng('http://sso.ouchn.cn/ashx/CheckCode.ashx');
        imagepng($im, 'tmp' . $p . '.png');
        $img = "tmp" . $p . ".png";//验证码地址
        $v = new valite();
        // $val=file_get_contents($img);
        // file_put_contents(SAE_TMP_PATH.'1.jpg',$val);//写入SAE临时文件
        $v->setImage($img);//传输该图片
        $v->getHec();
        $res = $v->run();//结果/*这个验证码的识别率在95%以上，如果对于有字母的需要为其扩充特征串类，对于有复杂变化的还需要进行其他处理*/
        echo $res . PHP_EOL;
        fwrite(STDOUT, "请输入正确的验证码: \n");
        $code = trim(fgets(STDIN));
        if(!$code){
            continue;
        }
        $run_arr = str_split($res);
        $input_arr = str_split($code);
        $confirm_arr = [];
        foreach ($run_arr as $i => $item) {
            if ($item != $input_arr[$i]) {
                $field = 'c' . ($i + 1);
                $confirm_arr[$v->$field] = $input_arr[$i];
            }
        }
        if (!empty($confirm_arr)) {
            $keys = $v->getKeys();
            $keys = array_merge($keys, $confirm_arr);
            $v->setKeys($keys);
            $v->saveKeys();
        }
    }
}
//xunlian();
