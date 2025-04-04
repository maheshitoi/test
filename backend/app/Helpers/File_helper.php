<?php
use Config\Services;
function isValidExtension($ext)
{
    $except = "png|gif|jpg|jpeg|pdf|docx|doc";
    if (preg_match('/(' . $except . ')/', $ext)) {
        return true;
    } else {
        return false;
    }

}

function isImage($ext)
{
    $except = "png|gif|jpg|jpeg";
    if (preg_match('/(' . $except . ')/', $ext)) {
        return true;
    } else {
        return false;
    }

}

function isDoc($ext)
{
    $except = "pdf|docx|doc";
    if (preg_match('/(' . $except . ')/', $ext)) {
        return true;
    } else {
        return false;
    }

}

function validatFileExtension($file, $allowedType = false)
{
    # code...
    foreach ($file['files'] as $img) {
        $ext = $img->getExtension();
        if (!$allowedType) {
            if (!isValidExtension($ext)) {
                return false;
            }

        } else if ($allowedType == 'IMAGE') {
            if (!isImage($ext)) {
                return false;
            }
        } else if ($allowedType == 'DOC') {
            if (!isDoc($ext)) {
                return false;
            }
        }
    }

    return true;
}

function uploadFile($img, $folderName = false)
{
    # code...
    if (!$img) {
        return false;
    }

    $newName = $img->getRandomName();
    if ($folderName) {
        $folder = $folderName;
    } else {
        $folder = 'uploads/';
    }

    //$newName;
    $security = Services::security();
    $fileName = $newName;
    $security->sanitizeFilename($newName);

    if ($img->isValid() && !$img->hasMoved()) {
        if ($img->move($folder, $newName)) {
            return $fileName;
        } else {
            return false;
        }

    }
}

function validateSize($file)
{

    foreach ($file['files'] as $img) {
        $size = $img->getSizeByUnit('mb');

        if ($size > 1) {
            return false;
        }

    }
    return true;
}

function getTempPath()
{
    $appConstant = new \Config\AppConstant();
    return $appConstant->TEM_PATH;
}
function pathExits($path)
{
    // $path = ROOTPATH.$path;
    // echo $path;
    if (!file_exists($path)) {
        // $old_umask = umask(000);
        echo $path;
        mkdir($path, 0777, true);
        chmod($path, 0777);
        // umask($old_umask);
        // if (mkdir($path, 777, true)) {
        //     chmod($path, 0777);
        // }

    }
}

function moveFile($from, $destination, $fileName)
{
    pathExits($from);
    pathExits($destination);
    $path;
    $to;
    if (endsWith($from, '/')) {
        $path = $from . $fileName;
    } else {
        $path = $from . '/' . $fileName;
    }

    if (endsWith($destination, '/')) {
        $to = $destination . $fileName;
    } else {
        $to = $destination . '/' . $fileName;
    }
    if (@rename($path, $to)) {
        chmod($to, 0777);
        @unlink($path);
        return true;
    } else {
        return false;
    }
}
function fileWrite($filePath, $data, $rFrom = false, $rTo = '')
{
    if ($rFrom !== false) {
        $data = str_replace($rFrom, $rTo, $data);
    }
    $folderPath = preg_replace('#\/[^/]*$#', '', $filePath);
    pathExits($folderPath);
    $file = fopen($filePath, "w") or die("Unable to open file!");
    fwrite($file, $data);
    fclose($file);
    chmod($filePath, 0777);
    // code...
}

function deleteImg($from, $fileName)
{
    if (!endsWith($from, '/')) {
        $from = $from . '/';
    }
    if ($fileName) {
        $path = $from . $fileName;
        @unlink($path);
    }
}
// Return the full path to this file
function getFilePath($path, $fileName)
{
    if (!endsWith($path, '/')) {
        $path = $path . '/';
    }
    return BASEURL . $path . $fileName;
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    return $length > 0 ? substr($haystack, -$length) === $needle : true;
}

function modelMoveFile($data, $va)
{
    $temp = getTempPath();
    foreach ($va as $key => $value) {
        if (isset($data[$key]) && !empty($data[$key])) {
            $epl        = '[~@!OLDIMAGE!@~]';
            $expData    = explode($epl, $data[$key]);
            $data[$key] = $expData[0];
            moveFile($temp, $value, $data[$key]);
            if (isset($expData[1])) {
                deleteImg($value, $expData[1]);
            }
        }
    }
    return $data;
}

function modelFileHandler(&$data, $va)
{
    if (count($data) !== count($data, COUNT_RECURSIVE)) {
        foreach ($data as &$item) {
            $item = modelMoveFile($item, $va);
        }
    } else {
        $data = modelMoveFile($data, $va);
    }
    return $data;
}

function addImageRealPath(&$data, $va)
{
    if (empty($data)) {
        return $data;
    }
    if (count($data) !== count($data, COUNT_RECURSIVE)) {
        foreach ($data as &$item) {
            $item = mapFilePath($item, $va);
        }
    } else {
        $data = mapFilePath($data, $va);
    }
    return $data;
}

function mapFilePath($data, $va)
{
    foreach ($va as $key => $value) {
        if (isset($data[$key])) {
            $data[$key . '_path'] = getFilePath($value, $data[$key]);
        }
    }
    return $data;
}
function extractPhoneDbFormat(&$data)
{
    $json_url  = 'public/countries.json';
    $json      = @file_get_contents($json_url);
    $Phonedata = json_decode($json, true);
    if (empty($data)) {
        return $data;
    }
    if (count($data) !== count($data, COUNT_RECURSIVE)) {
        foreach ($data as &$item) {
            $item = extractPhoneFormat($item, $Phonedata);
        }
    } else {
        $data = extractPhoneFormat($data, $Phonedata);
    }
    return $data;
}

function extractPhoneFormat($data, $phoneDb)
{
    if (is_array($phoneDb)) {
        foreach ($phoneDb as $key => $v) {
            if (isset($data['mobile_no']) && !empty($data['mobile_no'])) {
                $code = $v['phone'];
                $len  = strlen($v['phone']);
                if (substr($data['mobile_no'], 0, $len) == $code) {
                    $data['mobile_no']    = substr($data['mobile_no'], $len);
                    $data['country_code'] = $code;
                    break;
                }
            }
        }
    }
    return $data;
}
function fileExists($filePath)
{
    return is_file($filePath) && file_exists($filePath);
}

function getRealPath($path)
{
    return  $path;
}

function converBaseImage($url)
{
    if (!@getimagesize($url)) {
        return BASEURL.'asset/default_image.jpg';
    }
    return $url;
    $d = base64_encode(@file_get_contents($url));
    return $d ? '@' . $d : '';
}

function replaceImage($imgCol, $data,$appendText='_path')
{
    foreach ($imgCol as $k => $v) {
        if (isset($data[$v])) {
            $data[$v] = converBaseImage($data[$v . $appendText] ?? '');
        }
    }
    return $data;
}
function staffImageRealPath($data){
     $conf = new \Config\AppConstant();
     $imgP = array('profile_img' => $conf->staffProfileUploadPath);
     $data['data'] = addImageRealPath($data['data'], $imgP);
    return $data;
}
