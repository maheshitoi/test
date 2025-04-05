<?php

namespace App\Infrastructure\Persistence\Member;

use Core\Domain\Exception\RecordNotFoundException;
use App\Domain\Member\MemberRepository;
use App\Domain\Member\Member;
use Core\Infrastructure\Persistence\DMLPersistence;
use App\Models\Member\MemberModel;

class SQLMemberRepository implements MemberRepository
{
    use DMLPersistence;

    /** @var AppModel */
    protected $model;

    public function __construct()
    {
        $this->model = new MemberModel();
    }
    public function setEntity($d)
    {
        return new Member($d);
    }

    public function search($terms, $whereField = [])
    {
        $this->model->builder()
            ->groupStart()
            ->like('member.member_id', $terms, 'both')
            ->orLike('member.name', $terms, 'both')
            ->groupEnd();
        $this->setWhere($whereField);
        return $this->model->asArray()->allowCallbacks(true)->findAll(10);
    }
    function globalJoin()
    {
        $this->model->select("member.*");
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

        $this->model->select("member.*, pd.transaction_id, pd.amount, CASE WHEN pd.payment_status = 1 THEN 'Paid' WHEN payment_status = 2 THEN 'Pending' ELSE 'Failed' END as payment_status_name, 
                pd.remark as message, CASE WHEN pd.payment_mode_fk_id = 1 THEN 'Online' WHEN payment_mode_fk_id = 2 THEN 'Cheque / DD' WHEN payment_mode_fk_id = 3 THEN 'Qr Code' ELSE 'Unknown' END as payment_mode_name,
                pd.transaction_ref, DATE_FORMAT(pd.created_at, '%Y-%m-%d') as payment_date, pd.document, pd.razorpayment_id, pd.razorpayorder_id,")
            ->where("DATE(member.created_at) BETWEEN '$from_date' AND '$end_date'")
            ->join('payment_details as pd', 'pd.member_fk_id = member.id', 'left');

        return $this->model->asArray()->allowCallbacks(true)->findAll();
    }

    public function findAllDataByMonth()
    {
        $data = $this->model->select(['DATE_FORMAT(member.created_at, "%Y") as Year','DATE_FORMAT(member.created_at, "%M") as Month','COUNT(member.id) as total_members'], false)
            ->groupBy('Year, Month')
            ->asArray()
            ->allowCallbacks(true)
            ->findAll();

        $months = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december',];

        $formattedResults = [];

        foreach ($data as $row) {
            $year = $row['Year'];
            $month = strtolower($row['Month']);
            $total_members = $row['total_members'];

            if (!isset($formattedResults[$year])) {
                $monthlyData = [];
                foreach ($months as $monthName) {
                    $monthlyData[$monthName] = 0;
                }

                $formattedResults[$year] = [
                    'year' => $year,
                    'monthly_data' => $monthlyData
                ];
            }

            $formattedResults[$year]['monthly_data'][$month] = $total_members;
        }

        return array_values($formattedResults);
    }
    // public function findAllByWhere($cond)
    // {
    //     $this->globalJoin();
    //     $this->model->select("member.*")
    //     ->where("member.email_id",$cond);
    //     return $this->model->asArray()->allowCallbacks(true)->findAll();
    // }
}
