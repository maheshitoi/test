<?php

namespace App\Models\Payment;

use CodeIgniter\Model;

class Payment_detailsModel extends Model
{
    private $appConstant;
    private $imageColum;
    public function __construct()
    {
        helper('Core\Helpers\File');
        $this->appConstant = new \Config\AppConstant();
        $this->imageColum = array('document' => $this->appConstant->paymentDocument);
    }
    protected $table      = 'payment_details';
    protected $primaryKey = 'id';
    protected $returnType = 'App\Domain\Payment\Payment_details';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['member_fk_id', 'event_fk_id', 'transaction_id', 'amount', 'payment_mode_fk_id', 'mem_req_fk_id', 'payment_status', 'plan_id', 'contact_details', 'remark', 'transaction_ref', 'transaction_date','document','razorpayment_id','razorpayorder_id'];
    protected $useTimestamps = true;
    protected $beforeInsert = ['beforeSave'];
    protected $beforeUpdate = ['beforeSave'];
    protected $afterFind = ['addImageRealPath'];
    protected $allowCallbacks = true;

    public function beforeSave(array $data)
    {
        $data['data'] = modelFileHandler($data['data'], $this->imageColum);
        return $data;
    }
    protected function addImageRealPath(array $data)
    {
        $data['data'] = addImageRealPath($data['data'], $this->imageColum);
        return $data;
    }
}
