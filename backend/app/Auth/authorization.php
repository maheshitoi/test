<?php
use CodeIgniter\HTTP\RequestInterface;

require_once APPPATH . 'Auth/jwt.php';

class AUTHORIZATION
{

    static $request;
    static $response;

    public function __construct(RequestInterface $request)
    {
        self::$request  = \Config\Services::request();
        self::$response = \Config\Services::response();
    }

    public static function validateTimestamp($token)
    {
        //foreach ($token as $key => $val) {
        //  if (property_exists(__CLASS__, $key)) {
        //    $this->$key = $val;
        //}
        if ($token && (time() < $token->expire)) {
            return $token;
        }
        return false;
        //}
    }

    public static function validateToken($token)
    {
        $authConfig = new \Config\AppConstant();
        if ($authConfig->TOKEN_REMOTE_ACCESS === $token) {
            return true;
        }
        $token   = self::decodeToken($token);
        $uId     = $token->user_id ?? '';
        $aId     = $token->authorized_key ?? '';
        $eData   = $token->environment ?? '';
        $expData = $token->expire;
        $appName = $token->app_name ?? '';
        //both empty
        if (empty($aId) && empty($uId)) {
            return false;
        }
        $authConfig = new \Config\AppConstant();
        if (!empty($expData) && $eData == ENVIRONMENT && in_array($appName, $authConfig->app_name)) {
            return self::validateTimestamp($token);
        } else {
            return false;
        }
    }

    public static function decodeToken($token)
    {
        $authConfig = new \Config\AppConstant();
        return JWT::decode($token, $authConfig->jwt_key);
    }

    public static function generateToken($data)
    {

        $authConfig = new \Config\AppConstant();
        return JWT::encode($data, $authConfig->jwt_key);
    }
    public static function getTokenData(){
        $request = \Config\Services::request();
        $headers = $request->getHeaders();
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            list($jwt) = sscanf($headers['Authorization'], 'Authorization: Bearer %s');
            return self::decodeToken($jwt);
        } else {
            return false;
        }
    }

    public static function checkAuthorized()
    {
        $request = \Config\Services::request();
        $headers = $request->getHeaders();
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            list($jwt) = sscanf($headers['Authorization'], 'Authorization: Bearer %s');
            return self::validateToken($jwt);
        } else {
            return false;
        }

    }

}
