<?php

namespace App\Domain\Payment;

use CodeIgniter\Entity;

class Payment_details extends Entity
{
    protected $attributes = [
        'member_fk_id' => null, 'event_fk_id' => null, 'transaction_id' => null, 'amount' => null, 'payment_mode' => null, 'mem_req_fk_id' => null, 'payment_status', 'plan_id' => null, 'contact_details' => null, 'remark' => null,'document'=> null,
        'transaction_date' => null, 'transaction_ref' => null,'razorpayment_id'=>null,'razorpayorder_id'=>null,
    ];
}
