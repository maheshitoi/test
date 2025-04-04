<?php

namespace Core\Libraries;

use Razorpay\Api\Api;

class RazorpayLibrary
{
    protected $razorpay;
    protected $appConstant;

    public function __construct()
    {
        $this->appConstant = new \Config\AppConstant();
        $keyId = $this->appConstant->KEY_ID;
        $keySecret = $this->appConstant->KEY_SECRET;

        $this->razorpay = new Api($keyId, $keySecret);
    }

    public function createOrder($orderData)
    {
        return $this->razorpay->order->create($orderData);
    }

    public function verifyPayment($razorpayOrderId, $razorpayPaymentId, $razorpaySignature)
    {
        $signature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $this->appConstant->KEY_SECRET);

        return $signature === $razorpaySignature;
    }
}
