<?php

namespace Core\Domain\User;

use CodeIgniter\Entity;

class UserLogin extends Entity
{
    public $profile_id;
    public $user_name;
    protected $password;
    public $last_login_date;
    public $email_id;
    public $mobile_no;
    public $updated_at;
    public $created_at;
    public $deleted_at;
    public $reset_password;
    public $forgot_password;
    public $sponsor_fk_id;
    public $user_id;
    public $lname;
    public $fname;
    public $avatar;
    public $ref_fk_id;
    public $ref_module_id;
    public $member_fk_id;
    protected $attributes = ['user_name' => null, 'password' => null, 'last_login_date' => '', 'reset_password' => null, 'forgot_password' => null, 'user_id' => null, 'lname' => null, 'fname' => null, 'avatar' => null, 'mobile_no' => null, 'email_id' => null, 'member_fk_id' => null];

    public function setPassword(string $password)
    {
        $this->attributes['password'] = md5($password);
        return $this;
    }
    public function setUser_id(string $id)
    {
        $this->attributes['user_id'] = $id;
        return $this;
    }
    public function getUser_name()
    {
        return $this->attributes['user_name'];
    }
    public function getMobile_no()
    {
        return $this->attributes['mobile_no'];
    }

    public function password(string $password)
    {
        $this->password = $password;
        return $this;
    }

    public function last_login_date($format = 'Y-m-d H:i:s'): string
    {

        $timezone = isset($this->timezone) ? $this->timezone : app_timezone();
        $this->last_login_date->setTimezone($timezone);
        return $format === true ? $this->last_login_date : $this->last_login_date->format($format);
    }
}
