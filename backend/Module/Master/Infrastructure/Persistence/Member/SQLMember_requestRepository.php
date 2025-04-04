<?php

namespace App\Infrastructure\Persistence\Member;

use Core\Domain\Exception\RecordNotFoundException;
use App\Domain\Member\Member_requestRepository;
use App\Domain\Member\Member_request;
use Core\Infrastructure\Persistence\DMLPersistence;
use App\Models\Member\Member_requestModel;

class SQLMember_requestRepository implements Member_requestRepository
{
    use DMLPersistence;

    /** @var AppModel */
    protected $model;

    public function __construct()
    {
        $this->model = new Member_requestModel();
    }
    public function setEntity($d)
    {
        return new Member_request($d);
    }
    function globalJoin()
    {
        $this->model->select("member_request.*,CONCAT(IFNULL(CONCAT('',member_request.first_name,' '),''),member_request.last_name) AS fullName, pd.transaction_id, pd.amount, pd.transaction_ref, pd.payment_mode_fk_id, pd.transaction_date, pd.razorpayment_id, pd.razorpayorder_id, DATE_FORMAT(pd.created_at, '%Y-%m-%d') as payment_date,pd.payment_status
            CASE WHEN pd.payment_status = 1 THEN 'Paid' WHEN payment_status = 2 THEN 'Pending' ELSE 'Failed' END as payment_status_name")
            ->join('payment_details as pd', 'pd.mem_req_fk_id = member_request.id', 'left');
    }
    public function findAllPagination($ftbl, $isActive = true)
    {
        $this->globalJoin();
        if (!$isActive) {
            $this->model->onlyDeleted();
        }
        if (isset($ftbl->queryParams)) {
            foreach ($ftbl->queryParams as $k => $v) {
                $v->matchMode = 'havingLike';
            }
        }
        return $this->paginationQuery($ftbl, $this->defaultMapCol ?? []);
    }
    public function findWhereDateRange($from_date, $end_date)
    {
        $from_date = date('Y-m-d', strtotime($from_date));
        $end_date = date('Y-m-d', strtotime($end_date));
    
        $this->model->select("member_request.*")
            ->where("DATE(member_request.created_at) BETWEEN '$from_date' AND '$end_date'");
        return $this->model->asArray()->allowCallbacks(true)->findAll(); 
    }
    
}
