<?php

namespace App\Infrastructure\Persistence\Payment;

use Core\Domain\Exception\RecordNotFoundException;
use App\Domain\Payment\Payment_detailsRepository;
use App\Domain\Payment\Payment_details;
use Core\Infrastructure\Persistence\DMLPersistence;
use App\Models\Payment\Payment_detailsModel;

class SQLPayment_detailsRepository implements Payment_detailsRepository
{
    use DMLPersistence;

    /** @var AppModel */
    protected $model;

    public function __construct()
    {
        $this->model = new Payment_detailsModel();
    }
    public function setEntity($d)
    {
        return new Payment_details($d);
    }

    function globalJoin(): void {
        $this->model->select([
            'payment_details.*',
            "CASE
                WHEN payment_details.payment_status = 1 THEN 'Paid'
                WHEN payment_status = 2 THEN 'Pending'
                ELSE 'Failed'
            END as payment_status_name",
            "CASE
                WHEN payment_details.payment_mode_fk_id = 1 THEN 'Online'
                WHEN payment_mode_fk_id = 2 THEN 'Cheque / DD'
                WHEN payment_mode_fk_id = 3 THEN 'Qr Code'
                ELSE 'Unknown'
            END as payment_mode_name",
            'payment_details.remark as message',
            "CONCAT(IFNULL(CONCAT('', mr.first_name, ' '), ''), mr.last_name) AS fullName, ev.eventName, ev.event_date, ev.venue"
        ])
        ->join('member_request as mr', 'mr.id = payment_details.mem_req_fk_id', 'left')
        ->join('event as ev', 'ev.id = payment_details.event_fk_id', 'left');
    }
    public function findAllPagination($ftbl, $isActive = true) {
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
    
        $this->model->select(['payment_details.*',"CASE WHEN payment_details.payment_status = 1 THEN 'Paid' WHEN payment_status = 2 THEN 'Pending' ELSE 'Failed' END as payment_status_name",
            "CASE WHEN payment_details.payment_mode_fk_id = 1 THEN 'Online' WHEN payment_mode_fk_id = 2 THEN 'Cheque / DD' WHEN payment_mode_fk_id = 3 THEN 'Qr Code' ELSE 'Unknown' END as payment_mode_name",
            'payment_details.remark as message',
            "DATE_FORMAT(created_at, '%Y-%m-%d') as payment_date"])
            ->where("DATE(payment_details.created_at) BETWEEN '$from_date' AND '$end_date'");
    
        return $this->model->asArray()->allowCallbacks(true)->findAll(); 
    }
}
