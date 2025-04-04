<?php
namespace Core\Models\Modify_request;

use CodeIgniter\Model;

class Modify_requestModel extends Model
{
    protected $table          = 'modify_request';
    protected $primaryKey     = 'id';
    protected $returnType     = 'Core\Domain\Modify_request\Modify_request';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;
    protected $allowedFields  = ['action_id', 'request_data', 'created_by', 'module_id', 'ref_id', 'status', 'action_by', 'action_date', 'zone', 'region', 'department', 'institution', 'description', 'created_at', 'ad_office', 'trust', 'home', 'promotional_office', 'sponsorship_module', 'child_type','remarks'
    ];
    public $beforeUpdate = ['beforeSave', 'encodeData'];
    public $beforeInsert = ['beforeSave', 'encodeData'];
    public $afterFind    = ['decodeData'];
    //status => 1 approved 2 for pending 3 review 4 => rejected
    public function beforeSave(array $data)
    {
        if (isset($data['data'])) {
            $data['data']['action_date'] = date('Y-m-d H:i:s');
        }
        return $data;
    }
    //child type 1 mk child 2 for home child
    protected function decodeData(array $data)
    {
        return modelJsonHandler(['request_data'], $data, 'DECODE');
    }
    protected function encodeData(array $data)
    {
        return modelJsonHandler(['request_data'], $data);
    }
}
