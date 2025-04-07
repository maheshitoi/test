<?php

namespace Core\Models;

use CodeIgniter\Model;

class UserLoginModel extends Model
{
    private $appConstant;
    private $imageColum;
    public function __construct()
    {
        helper('Core\Helpers\File');
        $this->appConstant = new \Config\AppConstant();
        $this->imageColum = array('profile_img' => $this->appConstant->memberProfileImgPath);
    }
    protected $primaryKey     = 'user_id';
    protected $table          = 'user_login';
    protected $returnType     = 'Core\Domain\User\UserLogin';
    protected $useSoftDeletes = true;
    protected $allowedFields  = ['user_name', 'password', 'email_id', 'forgot_password', 'reset_password', 'mobile_no', 'last_login_date', 'profile_id', 'lname', 'fname', 'avatar', 'deleted_at', 'app_version', 'temp_block', 'block_reason', 'last_active_session', 'ref_fk_id', 'member_fk_id', 'profile_img'];
    protected $useTimestamps  = true;
    protected $beforeInsert = ['beforeSave'];
    protected $beforeUpdate = ['beforeSave'];
    protected $afterFind = ['addImageRealPath', 'phone_number_format', 'removePasswordField'];
    protected $allowCallbacks = true;

    public function phone_number_format(array $data)
    {
        $data['data'] = extractPhoneDbFormat($data['data']);
        return $data;
    }

    public function beforeSave(array $data)
    {
        $data['data'] = modelFileHandler($data['data'], $this->imageColum);
        return $data;
    }
    protected function addImageRealPath(array $data)
    {
        $data['data'] = addImageRealPath($data['data'], $this->imageColum);
        return $data;
    }
    protected function removePasswordField(array $data)
    {
        //$result=$data['data'];
        if (isset($data['data']) && $data['data']) {
            if (isset($data['data']['password'])) {
                unset($data['data']['password']);
            } else {
                foreach ($data['data'] as &$item) {
                    if (isset($item['password'])) {
                        unset($item['password']);
                    }
                }
            }
        }
        return $data;
    }
}
