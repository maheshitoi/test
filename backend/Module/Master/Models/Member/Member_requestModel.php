<?php

namespace App\Models\Member;

use CodeIgniter\Model;

class Member_requestModel extends Model
{   
    private $appConstant;
    private $imageColum;
    public function __construct()
    {
        helper('Core\Helpers\File');
        $this->appConstant = new \Config\AppConstant();
        $this->imageColum = array('profile_img' => $this->appConstant->memberprofileImgPath);
    }
    protected $table      = 'member_request';
    protected $primaryKey = 'id';
    protected $returnType = 'App\Domain\Member\Member_request';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['first_name', 'last_name', 'email_id','mobile_no', 'address', 'country', 'state', 'city', 'pincode', 'field', 'reg_no', 'dob', 'college_name', 'status', 'profile_img','mail_on'];
    protected $useTimestamps = true;
    protected $beforeInsert = ['beforeSave'];
    protected $beforeUpdate = ['beforeSave'];
    protected $afterFind = ['addImageRealPath'];
    protected $allowCallbacks = true;

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
}
