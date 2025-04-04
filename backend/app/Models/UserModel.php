<?php
namespace Core\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table          = 'user';
    protected $primaryKey     = 'user_id';
    protected $returnType     = 'Core\Domain\User\User';
    protected $useSoftDeletes = false;
    protected $allowedFields  = ['user_id', 'first_name', 'last_name', 'avatar', 'gender', 'date_of_birth'];
    protected $useTimestamps  = true;
    // protected $beforeUpdate   = ['last_login_date'];

    // protected function last_login_date($data)
    // {
    //     $format = 'Y-m-d H:i:s';
    //     if (isset($data['data']['user_id'])) {
    //         $timezone = isset($this->timezone) ? $this->timezone : app_timezone();
    //         $this->last_login_date->setTimezone($timezone);
    //         $data['data']['user_id']= $format === true ? $this->last_login_date : $this->last_login_date->format($format);
    //     }

    // }
}
