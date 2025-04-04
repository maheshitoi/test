<?php

namespace App\Domain\Member;

use CodeIgniter\Entity;

class Member_request extends Entity
{
    protected $attributes = [
        'first_name' => null, 'last_name' => null, 'email_id' => null, 'address' => null, 'country' => null, 'state' => null, 'city' => null, 'pincode' => null, 'field' => null, 'reg_no' => null, 'dob' => null, 'college_name' => null, 'profile_img' => null, 'status','mail_on',
    ];
}
