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
        $this->model->select("member_request.*,");
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
