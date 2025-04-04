<?php

namespace Core\Controllers;

use App\Domain\Exception\RecordNotFoundException;
use App\Domain\Member\Member;
use Core\Controllers\DMLController;
use App\Domain\Member\Member_request;
use App\Domain\Payment\Payment_details;
use App\Infrastructure\Persistence\Member\SQLMember_requestRepository;
use App\Infrastructure\Persistence\Member\SQLMemberRepository;
use App\Infrastructure\Persistence\Payment\SQLPayment_detailsRepository;
use App\Models\Member\Member_requestModel;
use Config\Services;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\IncomingRequest;
use Core\Libraries\RazorpayLibrary;
use Razorpay\Api\Api;
use Core\Libraries\EmailConetentGenerator;
use App\Libraries\Email;
use Core\Libraries\PDFGenerator;

class Become_a_member extends BaseController
{
    use DMLController;
    protected $validator;
    private $repository;
    private $userAgentHepler;
    private $paymentRepo;
    private $memberRepo;
    private $login_repository;
    protected $razorpayLibrary;
    private $pdfGenerate;
    protected $appConstant;
    private $email;
    public function __construct()
    {
        helper(['form']);
        helper('Core\Helpers\Utility');
        helper('Core\Helpers\Task');
        $this->initializeFunction();
        $this->repository       = new SQLMember_requestRepository();
        $this->paymentRepo      = new SQLPayment_detailsRepository();
        $this->memberRepo       = new SQLMemberRepository();
        $this->login_repository = Services::UserLoginRepository();
        $this->appConstant      = new \Config\AppConstant();
        $this->razorpayLibrary  = new RazorpayLibrary();
        $this->email            = new Email;
        $this->pdfGenerate = new PDFGenerator();
    }
    public function index()
    {
        $data = [];
        return view('include/header')
            . view('pages/become-a-member', $data)
            . view('include/footer');
    }
    
    public function member_req()
    {
    
        $req = $this->request->getPost();
        $res = $this->repository->insert(new Member_request($req));
        $price = 200;

        $orderData = [
            'amount'          => $price * 100, // amount in paise
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ];

        $razorpayOrder = $this->razorpayLibrary->createOrder($orderData);
        $razorpayOrderId = $razorpayOrder['id'];
        $_SESSION['email'] = $req['email_id'];
        $_SESSION['price'] = $price;
        $_SESSION['razorpay_order_id'] = $razorpayOrderId;

        $data = [
            "key"         => $this->appConstant->KEY_ID,
            "amount"      => $price * 100,
            "name"        => "IAOI",
            "description" => "dummy",
            "image"       => "https://s29.postimg.org/r6dj1g85z/daft_punk.jpg",
            "prefill"     => [
                "name"  => $req['name'],
                "email" => $req['email_id'],
                "mobile_no" => $req['mobile_no'],
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
            'mem_req_fk_id'    => $res,
            'payment_mode_fk_id' => 1,
            'amount'             => $price,
            'transaction_id'   => generateKey('PAYMENT'),
            'razorpayorder_id' => $razorpayOrderId
        ];
        
        $data['payment_fk_id'] = $this->paymentRepo->insert(new Payment_details($payment));
            return view('pages/payment', ['data' => $data, 'json' => $json]);
    }

    public function webLogin()
    {

        if (strtolower($this->reqMethod) == 'post') {
            $password = isset($_POST['password']) ? $_POST['password'] : null;
            $username = isset($_POST['username']) ? $_POST['username'] : null;
            if (!$password || !$username) {
            }
            try {
                $userData = $this->login_repository->loginCheck($username, md5($password));
                if ($userData) {
                    $memData = $this->memberRepo->findAllByWhere(['member.id' => $userData['member_fk_id']])[0] ?? [];
                }
            } catch (RecordNotFoundException $e) {
                return $this->message(404, $e->getMessage());
            }
            if (!empty($userData)) {
                if (!empty($_POST['password'])) {

                    $id = $userData['user_id'];
                    $_SESSION["username"] = $username;
                    $_SESSION["profile_img_path"] = $memData['profile_img_path'] ?? '';
                    $_SESSION["user_id"] = $id;
                    $_SESSION["valid"] = true;
                    return view('include/header', ['username' => $username, 'user_id' => $id])
                        . view('pages/home', ['data' => $userData])
                        . view('include/footer');
                }
            } else {
                $data['validation'] = "BAD Credentials";
                return view('include/header')
                    . view('pages/login', ['users' => $data])
                    . view('include/footer');
            }

            return $this->message(400, null, 'Invalid User');
        } else {
            return $this->message(400, null, 'Method Not Allowed');
        }
    }

    public function help_req()
    {
        $req = $this->request->getPost();
        $emailSent = $this->email->mapWithContent('4', $req, true, 'noreplay@iaoi.in', '');

        if ($emailSent) {
            return view('include/header')
                . view('pages/contact')
                . view('include/footer');
        } else {
            return $this->message(400, $req, 'Failed');
        }
    }

    public function resend_password()
    {
        $req = $this->request->getPost();
        $emailId = $req['email'];
        $password = rand(99, 9999);

        if (checkValue($req, 'email')) {
            $loginData = $this->login_repository->update(['email_id' => $emailId], ['password' => md5($password)], ['reset_password' => 1]);
            if (empty($loginData)) {
                return $this->message(400, null, 'Member Not Found');
            }
        }
        $emailData = [
            'email_id' => $req['email'],
            'password' => $password,
        ];
        $this->email->mapWithContent('2', $emailData, true, $req['email']);
        return view('include/header')
            . view('pages/login')
            . view('include/footer');
    }
}
