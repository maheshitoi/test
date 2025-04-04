<?php

namespace App\Domain\Member;

use CodeIgniter\Entity;

class Member extends Entity
{
    protected $attributes = [
        'mem_req_fk_id', 'member_id', 'name' => null, 'father_name' => null,'email_id' => null, 'mobile_no' => null, 'permanent_address' => null, 'present_address' => null, 'current_office_address' => null, 'last_office_address' => null, 'aadhar_no' => null, 'certification_no' => null, 'dob' => null, 'certification_doc' => null, 'remarks' => null,'approved_by'=>null, 'profile_img' => null, 'status' => null,
    ];
}
