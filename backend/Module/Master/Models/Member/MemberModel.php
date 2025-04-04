<?php

namespace App\Models\Member;

use CodeIgniter\Model;

class MemberModel extends Model
{
    private $appConstant;
    private $imageColum;
    public function __construct()
    {
        helper('Core\Helpers\File');
        $this->appConstant = new \Config\AppConstant();
        $this->imageColum = array('profile_img' => $this->appConstant->memberprofileImgPath);
    }
    protected $table      = 'member';
    protected $primaryKey = 'id';
    protected $returnType = 'App\Domain\Member\Member';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['member_id', 'first_name', 'last_name', 'email_id', 'address', 'country', 'state', 'city', 'pincode', 'field', 'reg_no', 'dob', 'college_name', 'mem_req_fk_id', 'approved_by', 'mobile_no', 'remarks', 'is_speaker', 'approved_by', 'profile_img','last_sent_mail_on'];
    protected $useTimestamps = true;
    protected $beforeInsert = ['beforeSave'];
    protected $beforeUpdate = ['beforeSave'];
    protected $afterFind = ['addImageRealPath', 'phone_number_format'];
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
}
