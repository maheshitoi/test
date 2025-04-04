<?php
use Core\Models\Utility\UtilityModel;
function generateRecordNumber($id)
{
    if ($id) {
        return "Rec-" . sprintf('%03s', $id);
    } else {
        return false;
    }

}

function removeUrlCharacter($text)
{
    $text = trim($text);
    return urldecode($text);
}

function decodeJson($d)
{
    return json_decode(urldecode($d));
}

function trimNumber($num)
{
    return preg_replace('/[^0-9]/', '', $num);
}

function trimPhoneNumber($num)
{
    return substr(preg_replace('/^\+?1|\|1|\D/', '', $num), -10);
}
function trimMobileNumber($phone, $removeCountry = false)
{
    $m = trimPhoneNumber($phone);
    return ltrim($m, 0);
}

function generateRandomNumber($digit, $from = false)
{
    // code...
    $to   = str_pad(9, $digit, "9", STR_PAD_LEFT);
    $from = $from || str_pad(1, $digit, "0", STR_PAD_RIGHT);
    return mt_rand($from, $to); //random 4 disgit
}

function generateKey($name)
{
    $model = new UtilityModel('master');
    return $model->generateId($name);
}

function mergeArrayKey($arrayKey, &$data, $mergeArray)
{

    foreach ($arrayKey as $key => $value) {
        if (isset($data[$value]) && !empty($data[$value])) {
            if (gettype($data[$value]) == 'array' && count($data[$value]) !== count($data[$value], COUNT_RECURSIVE)) {
                foreach ($data[$value] as $k => $s) {
                    if(isset($data[$value][$k]) &&  gettype($data[$value][$k]) != 'array'){
                        $data[$value][$k]=[];
                    }
                    $data[$value][$k] = array_merge($data[$value][$k], $mergeArray);
                }
            } else {
                $data[$value] = array_merge($data[$value], $mergeArray);
            }

        }
    }
    return $data;
}

function keyExsits($data, $k)
{
    $d = (array) $data;
    return isset($data[$k]);
}

function checkValue($data, $key)
{
    if (keyExsits($data, $key)) {
        return $data[$key];
    }
    return null;
}
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

function mapData($mapData, $data)
{
    foreach ($mapData as $key => $v) {
        $data[$key] = isset($data[$v]) ? $data[$v] : "";
    }
    return $data;
}

function parameterReplace($data, $temp)
{
    $matches=[];
    //get All the keys
    $txt = preg_match_all('/{(.*?)}/',$temp,$matches);
    if(isset($matches[0])){
        foreach ($matches[0] as $k => $v) {
            $str = checkValue($data,$matches[1][$k]) ?? '';
            $temp = str_replace($v, $str, $temp);
        }
    }
    return $temp;
}

function compress_html($html)
{
    $search = array(
        '/\>[^\S ]+/s', // remove whitespaces after tags
        '/[^\S ]+\</s', // remove whitespaces before tags
        '/(\s)+/s', // remove multiple consecutive spaces
    );

    $replace = array('>', '<', '\\1');

    return preg_replace($search, $replace, $html);
}

function generateNumber($n)
{
    $generator = "1357902468";
    $result    = "";

    for ($i = 1; $i <= $n; $i++) {
        $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }
    return $result;
}

function generateOTP($n)
{
    $num = generateNumber($n);
    if ($num != '5907') {
        return $num;
    } else {
        return generateNumber($n);
    }
}

function string_between_two_string($str, $tagname)
{
    $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
    preg_match_all($pattern, $str, $matches);
    return $matches[0][0] ?? '';
}
function replace_all_text_between($str, $tagname, $replacement)
{
    $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
    $res     = preg_replace($pattern, $replacement, $str);
    return $res;
}

function findReplaceWithData($str, $tagname, $data, $dateCol = [], $imgCol = [])
{
    $textData = string_between_two_string($str, $tagname, $dateCol);
    $result   = '';
    if (is_array($data)) {
        foreach ($data as $key => $v) {
            if (is_array($v)) {
                //$allKeys = array_keys($v);
                // check date
                if (is_array($dateCol) && count($dateCol)) {
                    $v = changeDateFormat($dateCol, $v);
                }
                // check replace image
                if (is_array($imgCol) && count($imgCol)) {
                    $v = replaceImage($imgCol, $v);
                }
                $result .= parameterReplace($v, $textData);
            }
        }
    }
    $str = replace_all_text_between($str, $tagname, $result);
    return $str;
}

function modelJsonHandler($key, &$data, $type = 'ENCODE')
{
    if (!empty($data['data'])) {
        foreach ($key as $key => $keyV) {
            if (isset($data['data'][$keyV])) {
                $data['data'][$keyV] = $type == 'ENCODE' ? json_encode($data['data'][$keyV], JSON_UNESCAPED_UNICODE) : json_decode($data['data'][$keyV]);
            } else {
                foreach ($data['data'] as $k => $v) {
                    if (isset($data['data'][$k][$keyV]) && !empty($data['data'][$k][$keyV])) {
                        $data['data'][$k][$keyV] = $type == 'ENCODE' ? json_encode($data['data'][$k][$keyV], JSON_UNESCAPED_UNICODE) : json_decode($data['data'][$k][$keyV]);
                    }

                }
            }
        }

    }
    return $data;
}

function generatePassword($password)
{
    $password = md5($password);
    return $password;
}