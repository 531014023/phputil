<?php
/**
 * Created by PhpStorm.
 * User: dy
 * Date: 2018/7/19
 * Time: 16:07
 */

namespace badwords;

/**
 * 敏感词检测
 * Class WordBan
 * @package badwords
 */
class WordBan
{
    public static function createLines()
    {
        $file_name = ROOT_PATH . 'extend/badwords/pub_banned_words.txt.out.txt';
        $file_name_php = ROOT_PATH . 'extend/badwords/words.php';
        if(file_exists($file_name_php))
        {
            return include $file_name_php;
        }
        $lines = file($file_name);
        $newLines = [];
        foreach ($lines as $rawword) {
            $newLines[] = trim($rawword);
        }
        $arr = var_export($newLines,true);
        $content = "<?php\nreturn ".$arr.";\n";
        file_put_contents($file_name_php,$content);
        return $newLines;
    }

    public static function find($text = null)
    {
        debug('word_start');
        $sensitive_words = self::createLines();
        $result = [];
        foreach ($sensitive_words as $v)
        {
            if(strpos($text,$v) !== false)
            {
                $result[] = $v;
            }
        }
        $result = array_unique($result);
        debug('word_end');
        return $result;
    }

    public static function replace(&$text = null,$replace_txt = '*')
    {
        debug('word_start');
        $sensitive_words = self::createLines();
        foreach ($sensitive_words as $v)
        {
            if(strpos($text,$v) !== false)
            {
                $str_replace_txt = $replace_txt;
                $len = mb_strlen($v);
                for ($i=0;$i<$len;$i++)
                {
                    $str_replace_txt .= $replace_txt;
                }
                $text = str_replace($v,$str_replace_txt,$text);
            }
        }
        debug('word_end');
    }
}