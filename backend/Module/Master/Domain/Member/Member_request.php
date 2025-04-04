<?php

namespace App\Domain\Member;

use CodeIgniter\Entity;

class Member_request extends Entity
{
    protected $attributes = [
        'name' => null, 'father_name' => null, 'email_id' => null, 'mobile_no' => null, 'permanent_address' => null, 'present_address' => null, 'current_office_address' => null, 'last_office_address' => null, 'aadhar_no' => null, 'certification_no' => null, 'dob' => null, 'certification_doc' => null,'profile_img' => null,'mail_on',
    ];
}
