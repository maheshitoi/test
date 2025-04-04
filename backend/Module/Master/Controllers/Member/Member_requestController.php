<?php

namespace App\Controllers\Member;

use App\Domain\Member\Member_request;
use Core\Controllers\BaseController;
use Core\Controllers\DMLController;
use App\Infrastructure\Persistence\Member\SQLMember_requestRepository;
use App\Domain\Payment\Payment_details;
use App\Infrastructure\Persistence\Payment\SQLPayment_detailsRepository;
use Core\Libraries\EmailConetentGenerator;
use App\Libraries\Email;
use Core\Libraries\PDFGenerator;

class Member_requestController extends BaseController
{
    use DMLController;
    private $repository;
    private $userAgentHepler;
    private $paymentRepo;
    private $email;
    private $pdfGenerate;
    public function __construct()
    {
        $this->initializeFunction();
        $this->repository = new SQLMember_requestRepository();
        $this->paymentRepo = new SQLPayment_detailsRepository();
        $this->pdfGenerate = new PDFGenerator();
        $this->email            = new Email;
    }
    public function memberSave()
    {
        $req = $this->getDataFromUrl('json');
        $req['status'] = 2;
        if (!checkValue($req, 'mobile_no')) {
            return $this->message(400, null, 'Mobile Number is required');
        }
        $res = $this->repository->insert(new Member_request($req));
        $payment = [
            'mem_req_fk_id'    => $res,
            'payment_mode_fk_id' => $req['payment_mode'],
            'transaction_ref'  => $req['DD/Check_no'] ?? '',
            'transaction_date' => $req['transaction_date'] ?? date('Y-m-d'),
            'document'         => $req['imagefile'] ?? "",
            'transaction_id'   => generateKey('PAYMENT'),
        ];

        $this->paymentRepo->insert(new Payment_details($payment));
        //member req inserted then only need to trigger  email
        $pdfs = [];

        $pdfs = $this->pdfGenerate->genByTemp('member_invoice', $res);
        $this->email->mapWithContent(1, $req, true, 'noreplay@iaoi.in',$pdfs);
        $this->email->mapWithContent(6, $req, true, $req['email_id'],$pdfs);
        return $this->message(200, $req, 'Successfully added');
    }
    public function getList($tblLazy, $active = true)
    {
        $isActive = ($active === 'false') ? false : true;
        if ($tblLazy) {
            $ftbl = json_decode((urldecode($tblLazy)));
        }
        $data = $this->repository->findAllPagination($ftbl, $isActive);
        return $this->message(200, $data, 'Success');
    }
    public function get($id = false)
    {
        $id = $id ? $id : 0;
        $data = [];
        if ($id === 0) {
            $data = $this->repository->findAll();
        } else {
            $data = $this->repository->findById($id);
            $data['payment']=$this->paymentRepo->findAllByWhere(['mem_req_fk_id'=>$data['id']])[0]??[];
        }
        return $this->message(200, $data);
    }
    public function isEmailUnique($email)
    {
        $data = $this->repository->findAllByWhere(['email_id' => $email]);
        return $this->message(200, $data);
    }
    public function memberRequestMailSend($razorpayorder_id)
    {
        $paymentData = $this->paymentRepo->findAllByWhere(['razorpayorder_id'=>$razorpayorder_id])[0]??[];
        if(!empty($paymentData)){
            $data = $this->repository->findById($paymentData['mem_req_fk_id']);
            $pdfs = $this->pdfGenerate->genByTemp('member_invoice', $data);
            $this->repository->updateById($data['id'],['mail_on'=>date('Y-m-d')]);
            $this->email->mapWithContent(6, $data, true, $data['email_id'], $pdfs);
            return $this->message(200, $pdfs,'Mail Sent Successfully');
        }else{
            log_message('debug','razorpayorder_id Not Found');
        }
    }
}
