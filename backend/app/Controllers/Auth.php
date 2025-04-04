<?php

namespace Core\Controllers;

use Config\Services;
use Core\Domain\Exception\RecordNotFoundException;
use Core\Domain\User\UserLoginRepository;
use Core\Domain\User\UserRepository;
use Core\Infrastructure\Persistence\Role\SQLUser_roleRepository;
// use Core\Libraries\Otp;
use Core\Models\Logs\LogsModel;

class Auth extends BaseController
{

    private $repository;
    private $role_repository;
    private $login_repository;
    private $logModel;
    private $sponsorRepo;
    private $otpLib;
    public function __construct()
    {
        helper('Core\Helpers\Utility');
        $this->initializeFunction();
        // $this->otpLib           = new Otp();
        $this->repository       = Services::UserRepository();
        $this->login_repository = Services::UserLoginRepository();
        $this->role_repository  = new SQLUser_roleRepository();
        $this->logModel         = new LogsModel();
    }

    public function index()
    {
        $this->login();
    }

    public function login($type = 'WEB')
    {

        if (strtolower($this->reqMethod) == 'post') {
            $data     = $this->getDataFromUrl('json');
            $password = isset($data['password']) ? $data['password'] : null ?? $_POST['password'];
            $username = isset($data['username']) ? $data['username'] : null ?? $_POST['username'];
            if (!$password || !$username) {
                return $this->message(500, null, 'Username and Password are Required');
            }
            try {
                $userData = $this->login_repository->loginCheck($username, md5($password));
            } catch (RecordNotFoundException $e) {
                return $this->message(404, $e->getMessage());
            }
            if (!empty($userData)) {
                if (!empty($_POST['password'])) {
                    session_start();
                    $_SESSION["username"] = $username;
                    header("Location: login.php");
                    exit();
                }
                $userData = $this->genAuthData($userData, $type);
                return $this->message(200, $userData);
            } else {
                return $this->message(400, null, 'Bad Credentials');
            }
            return $this->message(400, null, 'Invalid User');
        } else {
            return $this->message(400, null, 'Method Not Allowed');
        }
    }

    public function genAuthData($userData, $type = 'WEB', $appName = 'ADMIN_APP_WEB')
    {
        $authConfig = new \Config\AppConstant();
        $appName    = $type == 'WEB' ? 'ADMIN_APP_WEB' : ($type == 'SPONOR_APP' ? 'MOBILE_APP_SPONSOR' : 'MOBILE_APP_STAFF');
        $roleData   = $this->role_repository->findPermissionByUser($userData['user_id']);
        $tokenData  = ['user_id' => $userData['user_id'], 'user_name' => $userData['user_name'], 'app_name' => $appName, 'mobile_no' => $userData['mobile_no'], 'email_id' => $userData['email_id'], 'login_device' => $type, 'loggin_time' => date("Y-m-d H:i:s")];


        $userData['role'] = $roleData;
        //later implenet by ip for more security
        $userData['token'] = $this->createToken($tokenData);
        return $userData;
    }

    public function loginVerifyOtp()
    {
        $data      = $this->getDataFromUrl('json');
        $mobile_no = $data['mobile_no'] ?? '';
        $otp       = $data['otp'] ?? '';
        if (!$mobile_no) {
            return $this->message(400, null, 'Mobile Number Required');
        }
        if (!$otp) {
            return $this->message(400, null, 'OTP Required');
        }
        //otp verify
        // $res = $this->otpLib->verifyOtp($mobile_no, $otp);
        // if ($res['result'] != true) {
        //     return $this->message(400, $res, $res['response']);
        // }
        //remove country code
        $mobile_no = trimMobileNumber($mobile_no, true);
        //add country code
        $mobile_no = '91' . $mobile_no;
        // checkuser exsits
        $user = $this->login_repository->findAllByWhere(['mobile_no' => $mobile_no])[0] ?? [];
        if (empty($user)) {
            return $this->message(400, null, 'User Account not Exsits');
        }
        $userData = $this->genAuthData($user, 'MOBILE', 'MOBILE_APP_STAFF');
        // update last login
        $this->login_repository->update(['mobile_no' => $mobile_no], ['last_login_date' => date('Y-m-d H:i:s')]);
        return $this->message(200, $userData, 'Success');
    }

    public function changePassword()
    {
        $userId       = $this->userData->user_id ? $this->userData->user_id : 1;
        $data         = $this->getDataFromUrl('json');
        $password     = isset($data['password']) ? $data['password'] : null;
        $old_password = isset($data['old_password']) ? $data['old_password'] : null;
        $user_id      = isset($data['user_id']) ? $data['user_id'] : null;
        if (!$user_id || !$password) {
            return $this->message(400, null, 'Auth Id Missing, please try again');
        }
        $userData = $this->login_repository->findAllByWhere(['user_id' => $user_id, 'password' => md5($old_password)]);

        if (empty($userData)) {
            return $this->message(400, null, 'Old Password Mismatched');
        }
        $res = $this->login_repository->updateById($user_id, ['password' => md5($password), 'reset_password' => 0, 'last_reset_password' => date('Y-m-d H:i:s')]);
        $this->logModel->addLog($res ? 1 : 0, 'Change Password', $userId, $user_id, 0, 0);
        return $this->message($res ? 200 : 400, $data, 'unable to process your request');
    }

    public function updateStats()
    {
        // ussage update added
        $data[' = $this-'] > getDataFromUrl('json');
        $mobile_no = trimMobileNumber($data['mobile_no'] ?? '', true);
        $mobile_no = '91' . $mobile_no ?? '';
        $this->UtilityModel->executeQuery('update user_login set last_active_session =?,app_version=? where mobile_no =? ', [date('Y-m-d H:i:s'), $data['app_version'] ?? '', $mobile_no]);
        return $this->message(200, $data, 'Success');
    }

    public function genToken()
    {
        // token generation
        $data       = $this->getDataFromUrl('json');
        $authConfig = new \Config\AppConstant();

        if (!isset($data['authorized_key']) || empty($data['authorized_key'])) {
            return $this->message(400, $data, 'authorized Key Missing');
        }

        if ($authConfig->TOKEN_REMOTE_ACCESS === $data['authorized_key']) {
            $req                         = \Config\Services::request();
            $tokenData['ip_address']     = $req->getIPAddress();
            $tokenData['access_type']    = 'CLIENT_SERVER';
            $tokenData['app_name']       = 'API';
            $tokenData['authorized_key'] = $data['authorized_key'];
            $userData['token']           = $this->createToken($tokenData);
            return $this->message(200, $userData, 'Token Generated');
        } else {
            return $this->message(400, $data, 'Invalid Key');
        }
    }

    public function forgotPassword($id)
    {
        return $this->respond([$id]);
    }

    public function delete($id)
    {
        return $this->respond($id);
    }

    public function webLogin()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $userData = $this->login_repository->loginCheck($username, md5($password));
        if ($userData) {
            $_SESSION["username"] = $username;
            header("Location: pages/login.php");
            exit();
        } else {
            header("Location: pages/login.php?error=1");
            exit();
        }
    }
}
