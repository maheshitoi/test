<?php

namespace Core\Controllers;

use App\Domain\Payment\Payment_details;
use App\Domain\Member\Member_request;
use App\Infrastructure\Persistence\Member\SQLMember_requestRepository;
use App\Infrastructure\Persistence\Member\SQLMemberRepository;
use App\Infrastructure\Persistence\Events\SQLEventRepository;
use Core\Infrastructure\Persistence\Certification\SQLCertificationRepository;
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
    protected $eventRepo;
    protected $cerRepo;
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
        $this->eventRepo       = new SQLEventRepository();
        $this->cerRepo         = new SQLCertificationRepository();
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
                . view('pages/payment/payment-success.php')
                . view('include/footer');
        } else {
            return view('include/header')
                . view('pages/payment/payment-failed.php')
                . view('include/footer');
        }
    }

    public function paymentEmail($razorpayOrderId)
    {
        addTask('member_request/email/' . $razorpayOrderId, 'payment Successfull Email', 1);
    }
    public function registration_form()
    {
        $eventData = $this->eventRepo->findById(5);
        return
            view('pages/conference_registration', ['eventData' => $eventData]);
    }

    public function payment_registration()
    {
        $req = $this->request->getPost();
        $payload = json_decode($req['payload'], true);
        if (in_array($req['plan_id'], [1])) {
            $memData = $this->memRepo->findAllByWhere(['member_id' => $payload['member_id']])[0] ?? [];
            $existsdata = $this->paymentRepo->findAllByWhere(['member_fk_id' => $memData['id'], 'event_fk_id' => $req['event_fk_id'], 'plan_id' => $req['plan_id'], 'razorpayment_id !=' => null])[0] ?? '';
            if ($existsdata) {
                return
                    view('pages/payment/registration_failed.php', ['existsdata' => $existsdata]);
            }
        } elseif (in_array($req['plan_id'], [4])) {
            $memData = $this->memRepo->findAllByWhere(['member_id' => $payload['member_id']])[0] ?? [];
            $existsdata = $this->paymentRepo->findAllByWhere(['member_fk_id' => $memData['id'], 'event_fk_id' => $req['event_fk_id'], 'plan_id' => $req['plan_id']])[0] ?? '';
            if ($existsdata) {
                return
                    view('pages/payment/registration_failed.php', ['existsdata' => $existsdata]);
            } else {
                $payment = [
                    'member_fk_id'       => $memData['id'] ?? '',
                    'event_fk_id'        => $req['event_fk_id'] ?? '',
                    'plan_id'            => $req['plan_id'] ?? '',
                    'contact_details'    => $req['payload'],
                    'transaction_id'     => generateKey('PAYMENT'),
                    'amount'             => $req['amount'],
                    'payment_status'     => 1

                ];
                $this->paymentRepo->insert(new Payment_details($payment));
                $payload = json_decode($req["payload"], true);
                $emailData = array_merge($req, $payload);
                $this->whatsApp->send(1, $emailData, true);
                $this->email->mapWithContent(7, $emailData, true, $payload['email_id']);
                $this->email->mapWithContent(8, $emailData, true, 'noreplay@iaoi.in');
                return
                    view('pages/payment/registration_payment_success.php', ['data' => $req]);
            }
        } elseif (in_array($req['plan_id'], [3])) {
            $res = $this->mem_reqRepo->insert(new Member_request($payload));
        }
        $price = $req['amount'];
        $orderData = [
            'amount'          => $price * 100, // amount in paise
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ];

        $razorpayOrder = $this->razorpayLibrary->createOrder($orderData);
        $razorpayOrderId = $razorpayOrder['id'];
        $_SESSION['email'] = $payload['email_id'];
        $_SESSION['price'] = $price;
        $_SESSION['razorpay_order_id'] = $razorpayOrderId;

        $data = [
            "key"         => $this->appConstant->KEY_ID,
            "amount"      => $price * 100,
            "name"        => "IAOI",
            "description" => "dummy",
            "image"       => "https://s29.postimg.org/r6dj1g85z/daft_punk.jpg",
            "prefill"     => [
                "name"  => $payload['first_name'],
                "email" => $payload['email_id'],
                "mobile_no" => $payload['mobile_no'],
            ],
            "notes"       => [
                "address"           => "Hello World",
                "merchant_order_id" => "12312321",
            ],
            "theme"       => [
                "color" => "#F37254"
            ],
            "order_id"    => $razorpayOrderId,
        ];

        $json = json_encode($data);

        $payment = [
            'member_fk_id'       => $memData['id'] ?? '',
            'event_fk_id'        => $req['event_fk_id'] ?? '',
            'plan_id'            => $req['plan_id'] ?? '',
            'contact_details'    => $req['payload'],
            'transaction_id'     => generateKey('PAYMENT'),
            'amount'             => $req['amount'],
            'razorpayorder_id'   => $razorpayOrderId
        ];
        $data['payment_fk_id'] = $this->paymentRepo->insert(new Payment_details($payment));
        return view('pages/registration_payment', ['data' => $data, 'json' => $json]);
    }
    public function verify_registration_Payment()
    {
        $req = $this->request->getPost();
        $razorpayPaymentId = $req['razorpay_payment_id'];
        $razorpayOrderId = $req['razorpay_order_id'];
        $razorpaySignature = $req['razorpay_signature'];
        if ($this->razorpayLibrary->verifyPayment($razorpayOrderId, $razorpayPaymentId, $razorpaySignature)) {
            $this->paymentRepo->update(['razorpayorder_id' => $razorpayOrderId], ['payment_status' => 1, 'razorpayment_id' => $razorpayPaymentId]);
            $paymentData = $this->paymentRepo->findAllByWhere(['razorpayment_id' => $req['razorpay_payment_id']])[0] ?? [];
            $contactDetails = json_decode($paymentData["contact_details"], true);

            // Merge contact details with the main array and remove the original JSON string
            $data = array_merge($paymentData, $contactDetails);
            unset($data["contact_details"]);
            $this->whatsApp->send(1, $data, +917806816116, true);
            return;
            $this->email->mapWithContent(7, $data, true, $data['email_id']);
            $this->email->mapWithContent(8, $data, true, 'noreplay@iaoi.in');
            return
                view('pages/payment/registration_payment_success.php', ['data' => $data]);
        } else {
            return
                view('pages/payment/registration_payment_failed.php');
        }
    }
}
