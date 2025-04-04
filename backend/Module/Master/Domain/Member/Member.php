<?php

namespace App\Domain\Member;

use CodeIgniter\Entity;

class Member extends Entity
{
    protected $attributes = [
        'member_id', 'first_name' => null, 'last_name' => null,'last_sent_mail_on' => null, 'email_id' => null, 'address' => null, 'country' => null, 'state' => null, 'city' => null, 'pincode' => null, 'field' => null, 'reg_no' => null, 'dob' => null, 'college_name' => null, 'mem_req_fk_id' => null, 'approved_by' => null, 'mobile_no' => null, 'remarks'=>null, 'is_speaker', 'profile_img' => null,
    ];
}
