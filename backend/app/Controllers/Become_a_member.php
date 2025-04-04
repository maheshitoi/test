<?php

namespace Core\Controllers;

use App\Domain\Exception\RecordNotFoundException;
use App\Domain\Member\Member;
use Core\Domain\Certification\Certification;
use Core\Controllers\DMLController;
use App\Domain\Member\Member_request;
use App\Domain\Payment\Payment_details;
use App\Infrastructure\Persistence\Events\SQLEventRepository;
use App\Infrastructure\Persistence\Member\SQLMember_requestRepository;
use App\Infrastructure\Persistence\Member\SQLMemberRepository;
use App\Infrastructure\Persistence\Payment\SQLPayment_detailsRepository;
use Core\Infrastructure\Persistence\Certification\SQLCertificationRepository;
use App\Models\Member\Member_requestModel;
use Core\Models\Certification\CertificationModel;
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
    private $certificationRepo;
    private $login_repository;
    protected $razorpayLibrary;
    private $pdfGenerate;
    protected $appConstant;
    private $email;
    private $eventRepo;
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
        $this->eventRepo        = new SQLEventRepository();
        $this->certificationRepo = new SQLCertificationRepository();
    }
    public function index()
    {
        $data = [];
        return view('include/header')
            . view('pages/become-a-member', $data)
            . view('include/footer');
    }
    public function certificate()
    {
        $data = [];
        return view('include/header')
            . view('pages/certificate', $data)
            . view('include/footer');
    }
    public function profile()
    {
        $id = $this->sessionData->get('user_id');
        // $member_fk_id = $this->sessionData->get('member_fk_id');
        $userData = $this->login_repository->findAllByWhere(['user_id' => $id])[0] ?? [];
        $memData = $this->memberRepo->findAllByWhere(['member.id' => $userData['member_fk_id']]);
        return view('include/header') . view('pages/profile', ['userData' => $memData]) . view('include/footer');
    }
    public function generateCertificate() {
        $req = json_decode($this->request->getBody(), true);
        $certificate_name = $req['name'] . '_certificate';
        $pdf = $this->pdfGenerate->genByTemp('certificate', $req, 'f', $certificate_name);
        $view_pdf = FCPATH.$pdf;
        return $this->response->setJSON(['success' => true, 'data' => $view_pdf]);
    }
    public function mypayments()
    {
        $id = $this->sessionData->get('user_id');
        $paymentData = $this->paymentRepo->findAllByWhere(['member_fk_id' => $id]);
        return view('include/header') . view('pages/my_payments', ['paymentData' => $paymentData]) . view('include/footer');
    }
    public function member_req()
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'dob' => 'required',
            'email_id' => [
                'rules' => 'required|valid_email|is_unique[member_request.email_id]',
                'errors' => [
                    'is_unique' => 'The Email ID you entered already exists.',
                ],
            ],
            'field' => 'required',
            'reg_no' => 'required',
            'college_name' => 'required',
            'mobile_no' => 'required',
            'address' => 'required',
            'city' => 'required',
            'pincode' => 'required',
            'state' => 'required',
            'country' => 'required',
            'payment_mode' => 'required',
        ];

        $validation = \Config\Services::validation();

        if (!$this->validate($rules)) {
            $errors = $validation->getErrors();
            return view('include/header') . view('pages/become-a-member', ['errors' => $errors]) . view('include/footer');
        }
        $req = $this->request->getPost();
        if (isset($req['payment_mode'])) {
            switch ($req['payment_mode']) {
                case '1':
                    return $this->processOnlinePayment($req);
                case '2':
                    return $this->processChequePayment($req);
                case '3':
                    return $this->processQrPayment($req);
            }
        }

        return redirect()->to(BASEURL . 'become-a-member');
    }


    protected function processOnlinePayment($req)
    {
        $res = $this->repository->insert(new Member_request($req));
        $price = 5900;

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
                "name"  => $req['first_name'] . ' ' . $req['last_name'],
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
            'payment_mode_fk_id' => $req['payment_mode'],
            'amount'             => $price,
            'transaction_id'   => generateKey('PAYMENT'),
            'razorpayorder_id' => $razorpayOrderId
        ];

        $data['payment_fk_id'] = $this->paymentRepo->insert(new Payment_details($payment));
        if (empty($data)) {
            $member =  $this->repository->deleteOfId(['id' => $res]);
        } else {
            return view('pages/payment', ['data' => $data, 'json' => $json]);
        }
    }

    protected function processChequePayment($req)
    {
        $res = $this->repository->insert(new Member_request($req));
        $payment = [
            'mem_req_fk_id'    => $res,
            'payment_mode_fk_id' => $req['payment_mode'],
            'remark'           => $req['remark'],
            'transaction_ref'  => $req['DD/Check_no'] ?? '',
            'transaction_date' => $req['transaction_date'] ?? date('Y-m-d'),
            'document'         => $req['imagefile'] ?? "",
            'transaction_id'   => generateKey('PAYMENT'),
        ];

        $data['payment_fk_id'] = $this->paymentRepo->insert(new Payment_details($payment));
        $emailData = [
            'memberData' => $req,
            'paymentData' => $payment,
        ];
        $this->email->mapWithContent('1', $req, true, 'noreplay@iaoi.in', '');
        return redirect()->to(BASEURL . 'registration_success');
    }

    protected function processQrPayment($req)
    {
        $res = $this->repository->insert(new Member_request($req));
        $payment = [
            'mem_req_fk_id'    => $res,
            'payment_mode_fk_id' => $req['payment_mode'],
            'remark'           => $req['remarks'],
            'document'         => $req['screenshotfile'] ?? "",
            'transaction_id'   => generateKey('PAYMENT'),
        ];

        $data['payment_fk_id'] = $this->paymentRepo->insert(new Payment_details($payment));

        $this->email->mapWithContent('1', $req, true, 'noreplay@iaoi.in', '');
        return redirect()->to(BASEURL . 'registration_success');
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

                    $userData['eventData'] = $this->eventRepo->findAllByWhere(['status' => 2]) ?? [];
                    $memData = $this->memberRepo->findAllByWhere(['member.id' => $userData['member_fk_id']])[0] ?? [];
                    $certificationData = $this->certificationRepo->findAllByWhere(['Certification.member_fk_id' => $userData['member_fk_id']]);
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

    public function profile_edit($edit = false)
    {
        if ($edit) {
            $id = $this->sessionData->get('user_id');
            // $member_fk_id = $this->sessionData->get('member_fk_id');
            $userData = $this->login_repository->findAllByWhere(['user_id' => $id])[0] ?? [];
            $memData = $this->memberRepo->findAllByWhere(['member.id' => $userData['member_fk_id']]);
            return view('include/header')
                . view('pages/profile_edit', ['userData' => $memData])
                . view('include/footer');
        }
        $memData = $this->request->getPost();
        if (checkValue($memData, 'email_id')) {
            $memberReqData = $this->memberRepo->findAllByWhere(['email_id' => $memData['email_id']])[0] ?? [];
            $memData['id'] = $memberReqData['id'];
            if (empty($memberReqData)) {
                return $this->message(400, null, 'Member Not Found');
            }

            $res = $this->memberRepo->save(new Member($memData));
            $memberReqData = $this->memberRepo->findAllByWhere(['email_id' => $memData['email_id']]);

            return view('include/header')
                . view('pages/profile', ['userData' => $memberReqData])
                . view('include/footer');
        }
    }

    public function getAllmembername()
    {
        $reqData = $this->memberRepo->findAll();
        return view('include/header')
            . view('pages/members', ['reqData' => $reqData])
            . view('include/footer');
    }

    public function publication()
    {
        return view('include/header')
            . view('pages/publication')
            . view('include/footer');
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
