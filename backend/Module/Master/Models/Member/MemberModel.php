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
        $this->imageColum = array(
            'profile_img' => $this->appConstant->memberProfileImgPath,
            'certification_doc' => $this->appConstant->certificationDocPath
        );
        
    }
    protected $table      = 'member';
    protected $primaryKey = 'id';
    protected $returnType = 'App\Domain\Member\Member';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['mem_req_fk_id', 'member_id', 'name', 'father_name', 'email_id', 'mobile_no', 'permanent_address', 'present_address', 'office_designation', 'aadhar_no', 'certification_no', 'dob', 'certification_doc', 'remarks', 'approved_by', 'profile_img', 'status'];
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
