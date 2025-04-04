<?php

namespace App\Controllers\Payment;

use App\Domain\Member\Member_request;
use Core\Controllers\BaseController;
use Core\Controllers\DMLController;
use App\Infrastructure\Persistence\Payment\SQLPayment_detailsRepository;

class Payment_detailsController extends BaseController
{
    use DMLController;
    private $paymentRepo;
    private $userAgentHepler;
    public function __construct()
    {
        $this->initializeFunction();
        $this->paymentRepo = new SQLPayment_detailsRepository();
    }
    public function getList($tblLazy, $active = true)
    {
        $ftbl= [];
        $isActive = ($active === 'false') ? false : true;
        if ($tblLazy) {
            $ftbl = json_decode(utf8_decode(urldecode($tblLazy)));
        }
        $data = $this->paymentRepo->findAllPagination($ftbl, $isActive);
        return $this->message(200, $data, 'Success');
    }

    public function get($id = false)
    {
        $id = $id ? $id : 0;
        $data = [];
        if ($id === 0) {
            $data = $this->paymentRepo->findAll();
        } else {
            $data = $this->paymentRepo->findById($id);
        }
        return $this->message(200, $data);
    }
    public function updatePayment()
    {
        $data = $this->getDataFromUrl('json');
        $payment = [
            'payment_mode_fk_id' => $data['payment_mode_fk_id'],
            'payment_status'   => $data['payment_status'],
        ];
        $res= $this->paymentRepo->updateById($data['id'],$payment);
        return $this->message(200, $res,'updated');
    }
}

