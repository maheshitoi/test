 <?php
function createEdom($value = '')
{

}

function callApi($url, $method = "GET", $data = '')
{
    // $url = 'http://edomsgems-env.eba-qdaamcq5.ap-south-
    // 1.elasticbeanstalk.com/'.$url;
    //if(!$token){
    $token = file_get_contents('token_edom.json');
    if ($token) {
        $token = json_decode($token);
        if ($token->access_token) {
            $token = $token->access_token;
        }
    }
    //}

    $client = \Config\Services::curlrequest([
        'baseURI' => 'http://edomsgems-env.eba-qdaamcq5.ap-south-1.elasticbeanstalk.com',
    ]);
    $response = $client->request($method, $url, [
        'http_errors' => false,
        'debug'       => 'curl_log.txt',
        'headers'     => [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ],
    ]);

    $code = $response->getStatusCode();
    if ($code == 401) {
        createTokenEdom();
        return false;
    }
    return json_decode($response->getBody());
}

function createTokenEdom()
{
    $client = \Config\Services::curlrequest([
        'baseURI' => 'http://edomsgems-env.eba-qdaamcq5.ap-south-1.elasticbeanstalk.com',
    ]);
    $response = $client->request('POST', '/token', [
        'http_errors' => false,
        'debug'       => 'curl_log.txt',
        'form_params' => [
            'username'   => 'admin@gmail.com',
            'password'   => 'admin@123',
            'grant_type' => 'password',
        ],
        'headers'     => [
            'Accept' => 'application/json',
        ],
    ]);
    $result = $response->getBody();
    // if (strpos($response->getHeader('content-type'), 'application/json') !== false) {
    //$body = json_decode($result);
    //}
    //print_r($body);
    $filePath = 'token_edom.json';
    // $folderPath = preg_replace('#\/[^/]*$#', '', $filePath);
    // pathExits($folderPath);
    $file = fopen($filePath, "w") or die("Unable to open file!");
    fwrite($file, $result);
    fclose($file);
    @chmod($filePath, 0777);
    return $result;
}
?>