<?php

namespace App\Domain\Payment;

use CodeIgniter\Entity;

class Payment_details extends Entity
{
    protected $attributes = [
        'member_fk_id' => null, 'transaction_id' => null, 'amount' => null, 'mem_req_fk_id' => null, 'payment_status' => null, 'remark' => null, 'payment_mode_fk_id'=>null, 'transaction_ref' => null, 'transaction_date' => null, 'document' => null,'razorpayment_id'=> null,
        'razorpayorder_id' => null,
    ];
}
