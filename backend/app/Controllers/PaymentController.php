<?php

namespace Core\Controllers;

use App\Domain\Payment\Payment_details;
use App\Domain\Member\Member_request;
use App\Infrastructure\Persistence\Member\SQLMember_requestRepository;
use App\Infrastructure\Persistence\Member\SQLMemberRepository;
use App\Infrastructure\Persistence\Payment\SQLPayment_detailsRepository;
use App\Libraries\Email;
use App\Libraries\whatsApp;
use App\Libraries\countrycode;
use CodeIgniter\HTTP\Message;
use Core\Libraries\PDFGenerator;
use Core\Libraries\RazorpayLibrary;

class PaymentController extends BaseController
{
    protected $razorpayLibrary;
    protected $appConstant;
    protected $paymentRepo;
    protected $mem_reqRepo;
    protected $memRepo;
    protected $pdfGenerate;
    protected $email;
    protected $whatsApp;

    public function __construct()
    {
        helper('Core\Helpers\Task');
        $this->appConstant     = new \Config\AppConstant();
        $this->razorpayLibrary = new RazorpayLibrary();
        $this->paymentRepo     = new SQLPayment_detailsRepository();
        $this->mem_reqRepo     = new SQLMember_requestRepository();
        $this->memRepo         = new SQLMemberRepository();
        $this->email           = new Email();
        $this->whatsApp        = new whatsApp();
        $this->pdfGenerate     = new PDFGenerator();
    }

    public function verifyPayment()
    {
        $req = $this->request->getPost();
        $razorpayPaymentId = $req['razorpay_payment_id'];
        $razorpayOrderId = $req['razorpay_order_id'];
        $razorpaySignature = $req['razorpay_signature'];
        if ($this->razorpayLibrary->verifyPayment($razorpayOrderId, $razorpayPaymentId, $razorpaySignature)) {
            $this->paymentRepo->update(['razorpayorder_id' => $razorpayOrderId], ['payment_status' => 1, 'razorpayment_id' => $razorpayPaymentId]);
            $this->paymentEmail($razorpayOrderId);
            return view('include/header')
                . view('pages/payment/payment-success.php');
        } else {
            return view('include/header')
                . view('pages/payment/payment-failed.php');
        }
    }

    public function paymentEmail($razorpayOrderId)
    {
        addTask('member_request/email/' . $razorpayOrderId, 'payment Successfull Email', 1);
    }

}
