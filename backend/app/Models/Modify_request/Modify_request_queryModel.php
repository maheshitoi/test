<?php
namespace Core\Models\Modify_request;
use CodeIgniter\Model;

    class Modify_request_queryModel extends Model
    {
        protected $table      = 'modify_request_query';
    protected $primaryKey = 'id';
    protected $returnType = 'Core\Domain\Modify_request\Modify_request_query';
    protected $useSoftDeletes = true;
         protected $allowedFields = [ 'query_remark','created_by','modify_request_fk_id',];
         protected $useTimestamps = true;
    }
?>