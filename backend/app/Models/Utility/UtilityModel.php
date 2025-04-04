<?php

namespace Core\Models\Utility;

use App\Infrastructure\Persistence\Account\SQLAccountRepository;
use App\Infrastructure\Persistence\Field\SQLFieldRepository;
use CodeIgniter\Model;
use Core\Libraries\ExportExcel;

class UtilityModel extends Model
{
    private $staffRepo;
    protected $db;
    public function __construct($profile = '')
    {
        parent::__construct();
        if ($profile) {
            $this->db = \Config\Database::connect($profile);
        } else {
            $this->db = \Config\Database::connect();
        }
        $this->appConstant = new \Config\AppConstant();
    }
    public function get_db()
    {
        return $this->db;
    }
    public function addNewData($tName, $data)
    {
        if ($tName == 'trust') {
            $data = modelFileHandler($data, ['logo_img' => $this->appConstant->trustLogoUploadPath, 'water_mark_img' => $this->appConstant->trustWaterMarkUploadPath]);
            //master Account Creation

        }
        $this->db->table($tName)->insert($data);
        $id = $this->db->insertID();
        if ($tName == 'payroll_group') {
            $accRepo = new SQLAccountRepository();
            $accRepo->addCheckAccount(['accountName' => $data['payroll_groupName'], 'ref_fk_id' => $id, 'status' => 1, 'category' => 1], 7);
        }
        return $id;
    }

    public function deleteData($tName, $cond)
    {
        return $this->db->table($tName)->where($cond)->delete();
    }

    public function replaceData($tName, $data)
    {
        return $this->db->table($tName)->replace($data);
    }
    public function mapColTbl($tName, $d)
    {
        $col = $this->showTableColum($tName);
        $mapData = [];
        foreach ($col as $key => $v) {
            $field = $v['Field'];
            if (isset($d[$field]) && !in_array($field, ['created_at', 'updated_at', 'deleted_at'])) {
                $mapData[$field] = $d[$field];
            }
        }
        return $mapData;
    }

    public function updateData($tName, $data, $where)
    {
        if ($tName == 'trust') {
            $data = modelFileHandler($data, ['logo_img' => $this->appConstant->trustLogoUploadPath, 'water_mark_img' => $this->appConstant->trustWaterMarkUploadPath]);
        }
        if ($tName == 'payroll_group') {
            $accRepo = new SQLAccountRepository();
            $accRepo->updateName($data['id'], 7, $data['payroll_groupName']);
        }
        return $this->db->table($tName)->update($data, $where);
    }
    public function getDataById($tbl, $cond, $returnType = 'Object')
    {
        $builder = $this->db->table($tbl)->select('*');
        if ($tbl == 'home') {
            $builder->select('home.status as status,home.id as id,h.home_typeName,z.zoneName,r.regionName,home.created_at as created_at,home.updated_at as updated_at,home.deleted_at as deleted_at,f.fieldName,u.fname as created_byName,uM.fname as last_modify_byName,home.mobile_no as mobile_no,home.email_id as email_id');
            $builder->join('home_type as h', 'h.id = home.type', 'inner');
            $builder->join('zone as z', 'z.id = zone', 'left')
                ->join('field as f', 'f.id = field', 'left')
                ->join('user_login as u', 'u.user_id = created_by', 'left')
                ->join('user_login as uM', 'uM.user_id = last_modify_by', 'left')
                ->join('region as r', 'r.id = z.region_id', 'left');
        }
        if ($returnType == 'array') {
            $data = $builder->where($cond)->get()->getResultArray();
        } else {
            $data = $builder->where($cond)->get()->getRow();
        }
        if ($tbl == 'trust') {
            $data = (array) $data;
            $data = addImageRealPath($data, ['logo_img' => $this->appConstant->trustLogoUploadPath, 'water_mark_img' => $this->appConstant->trustWaterMarkUploadPath]);
        }
        return $data;
    }

    private function mapQuery($tName, $builder)
    {
        switch ($tName) {
            case 'country':
                $builder->select('country.*,CONCAT("+",countryPhoneCode," ",countryName) as country_code_with_name', false);
                break;
            case 'district':
                $builder->select($tName . '.id as id, ' . $tName . '.status as status');
                $builder->join('country', 'country.id = country_id', 'inner');
                $builder->join('state', 'state.id = state_id', 'inner');
                break;

            case 'subdistrict':
                $builder->select($tName . '.id as id, ' . $tName . '.status as status');
                $builder->join('country', 'country.id = country_id', 'inner');
                $builder->join('state', 'state.id = state_id', 'inner');
                $builder->join('district', 'district.id = district_id', 'inner');

                break;
            case 'state':
                $builder->select('state.id as id,state.status as status,state.created_at as created_at');
                $builder->join('country as c', 'c.id = country_id', 'inner');
                break;

            case 'village':
                $builder->select($tName . '.id as id, ' . $tName . '.status as status');
                $builder->join('panchayat as p', 'p.id = panchayat_id', 'inner');
                $builder->join('subdistrict as s', 's.id = p.sub_district_id', 'inner');
                $builder->join('district as d', 'd.id = s.district_id', 'inner');
                $builder->join('state', 'state.id = d.state_id', 'inner');
                break;
            case 'ward':
                $builder->select($tName . '.id as id, ' . $tName . '.status as status');
                $builder->join('city as c', 'c.id = city_id', 'inner');
                $builder->join('subdistrict as s', 's.id = c.sub_district_id', 'inner');
                $builder->join('district as d', 'd.id = c.district_id', 'inner');
                $builder->join('state', 'state.id = d.state_id', 'inner');
                break;

            case 'panchayat':
                $builder->select($tName . '.id as id, ' . $tName . '.status as status');
                $builder->join('state', 'state.id = state_id', 'inner');
                $builder->join('district', 'district.id = district_id', 'inner');
                $builder->join('subdistrict', 'subdistrict.id = sub_district_id', 'inner');
                break;

            case 'city':
                $builder->select($tName . '.id as id, ' . $tName . '.status as status');
                $builder->join('state', 'state.id = state_id', 'inner');
                $builder->join('district', 'district.id = district_id', 'inner');
                $builder->join('subdistrict', 'subdistrict.id = sub_district_id', 'inner');
                break;

            case 'field':
                $builder->select($tName . '.id as id, ' . $tName . '.created_at as created_at,COALESCE(fieldName,vName,wardName) as fieldName')
                    ->join('village', 'village.id = village', 'left')
                    ->join('ward', 'ward.id = ward', 'left');
                break;
            case 'zone':
                $builder->select('zone.created_at as created_at,zone.id as id, zone.status as status, zone.contact_name as contact_name,zone.email_id as email_id,zone.mobile_no  as mobile_no,zone.lat as lat,zone.lng as lng,ze_short_code');
                $builder->join('region', 'region.id = region_id', 'inner');
                $orderByCol = 'zoneName';
                break;
            case 'department':
                $builder->select('department.id as id, department.status as status,department.contact_name as contact_name,department.created_at as created_at,region_id,re_short_code,r.regionName');
                $builder->join('ad_office', 'ad_office.id = ad_id', 'inner')
                    ->join('region as r', 'r.id = region_id', 'left');
                $orderByCol = 'dName';
                break;
            case 'designation':
                $builder->select('designation.id as id, designation.status as status,b.branchName,designation.created_at as created_at');
                $builder->join('branch as b', 'b.id = branch_id', 'inner');
                $orderByCol = 'deName';
                break;
            case 'module':
                $builder->select('module_action.id as moduleActionId,module.moduleName,action_name.actionName');
                $builder->join('module_action', 'module_action.module_id = module.id', 'inner');
                $builder->join('action_name', 'action_name.id = module_action.action_id', 'inner');

                break;
            case 'home':
                $builder->select('home.status as status,home.id as id,h.home_typeName,z.zoneName,r.regionName,f.fieldName,home.created_at as created_at,home.deleted_at as deleted_at,home.updated_at as updated_at,home.mobile_no as mobile_no,home.email_id as email_id');
                $builder->join('home_type as h', 'h.id = home.type', 'inner');
                $builder->join('zone as z', 'z.id = zone', 'left')
                    ->join('field as f', 'f.id = field', 'left')
                    ->join('region as r', 'r.id = z.region_id', 'left');
                break;
            case 'role_permission':
                $cond = array();
                $builder->join('role', 'role.id = role_permission.role_id', 'inner');
                $builder->join('module_action', 'module_action.module_id = role_permission.moduleActionId', 'inner');
                $builder->join('module', 'module.id = module_action.module_id', 'inner');
                $builder->join('action_name', 'action_name.id = module_action.action_id', 'inner');
                break;
            case 'church':
                $builder->select('church.status as status,church.id as id,church_name,church_id,z.zoneName,f.zone as zone,church.field')
                    ->join('field as f', 'f.id = field', 'left')
                    ->join('zone as z', 'z.id = f.zone', 'left');
                break;
            case 'account_scheme':
                $builder->select('account_scheme.status as status,account_scheme.id as id,account_categoryName')
                    ->join('account_category as c', 'c.id = account_category_id', 'left');
                break;
            case 'leave_type':
                $builder->select($tName . '.id as id, ' . $tName . '.status as status,CASE wHEN allowable_gender =1 THEN "Male Only" WHEN allowable_gender =2 THEN "Female Only" ELSE "Both" END as allowable_genderName,IF(is_paid_leave =1,"Yes" , "NO") as is_paid_leave_status,IF(allow_admin_only =1,"Yes" , "NO") as allow_admin_only_status,IF(allow_half_day =1,"Yes" , "NO") as allow_half_day_status,IF(is_staff_allow =1,"Yes" , "NO") as is_staff_allow_status', false);
                break;
            case 'payroll_group':
                $builder->select('payroll_group.status as status,payroll_group.id as id,trustName,payroll_groupName')
                    ->join(MASTER_DB_NAME . '.trust as t', 't.id = trust_fk_id', 'inner');
                break;
            case 'asset_category':
                $builder->select('asset_category.status as status,asset_category.id as id,t.asset_typeName')
                    ->join(MASTER_DB_NAME . '.asset_type as t', 't.id = asset_type_id', 'left');
                break;
            case 'asset_sub_category':
                $builder->select('asset_sub_category.status as status,asset_sub_category.id as id,c.asset_categoryName')
                    ->join(MASTER_DB_NAME . '.asset_category as c', 'c.id = asset_category_id', 'left');
                break;
            case 'salary_category':
                $builder->select('salary_category.*,salary_category.status as status,salary_category.id as id,t.payroll_typeName')
                    ->join('payroll_type as t', 't.id = payroll_type_fk_id', 'left');
                break;
        }
    }

    public function exportExcel($tName, $col, $cond = [])
    {
        $builder = $this->db->table($tName)->select('*');
        $this->mapQuery($tName, $builder);
        if (!empty($cond)) {
            $builder->where($cond);
        }
        $fieldRepo = new SQLFieldRepository();
        $dataResult = [];
        if ($tName == 'field') {
            $result = $fieldRepo->findDetail(true, '');
        } else {
            $builder->where($tName . '.status', 1);
            $result = $builder->get()->getResultArray();
        }
        foreach ($result as $k => &$v) {
            $dE['col'] = $v;
            array_push($dataResult, $dE);
        }
        $excelLib = new ExportExcel();
        return $excelLib->export($dataResult, $col);
    }

    public function getData($tName, $cond, $join = false, $ftbl = false)
    {
        $orderByCol;
        $builder = $this->db->table($tName)->select('*');
        if ($join) {
            $this->mapQuery($tName, $builder);
        }
        foreach ($cond as $key => $value) {
            if (is_array($value)) {
                $builder->whereIn($key, $value);
            } else {
                $builder->where($key, $value);
            }
        }
        if ($ftbl) {
            if (isset($ftbl->queryParams)) {
                if (count($ftbl->queryParams)) {
                    foreach ($ftbl->queryParams as $key => $v) {
                        if ($v->value) {
                            $builder->like($v->colName, $v->value);
                        }
                    }
                }
            }
            $result['totalRecord'] = $builder->countAllResults('', false);
            if (isset($ftbl->rows) && isset($ftbl->page)) {
                $builder->limit($ftbl->rows, ($ftbl->rows * $ftbl->page));
            }
            if (isset($ftbl->sort)) {
                if (count($ftbl->sort)) {
                    foreach ($ftbl->sort as $key => $v) {
                        $builder->orderBy($v->colName, $v->sortOrder);
                    }
                }
            }
            $result['data'] = $builder->get()->getResultArray();
            if ($tName == 'trust') {
                // $result = (array) $result['data'];
                $result['data'] = addImageRealPath($result['data'], ['logo_img' => $this->appConstant->trustLogoUploadPath]);
            }
            return $result;
        }
        $scol = ['ad_office' => 'adName', 'designation' => 'deName', 'department' => 'dName', 'panchayat' => 'pName', 'village' => 'vName', 'sponsorship_module' => 'name', 'staff' => 'name', 'promotional_office' => 'promotionalName', 'email_template' => 'event_name', 'title' => 'id', 'church' => 'church_name', 'settings' => 'description', 'payroll_head' => 'order_id'];
        foreach ($scol as $k => $v) {
            if ($tName == $k) {
                $orderByCol = $v;
            }
        }
        if ($tName == 'field') {
            $builder->where(['deleted_at is Null ' => null]);
        } else {
            $builder->where([$tName . '.status' => 1]);
        }
        $builder->orderBy($orderByCol ?? $tName . 'Name', 'asc');
        //echo $builder->getCompiledSelect();
        return $builder->get()->getResultArray();
    }

    public function updateRolePermission($data, $roleId)
    {
        $builder = $this->db->table('role_permission');
        $builder->delete(['role_id' => $roleId]);
        $result = $builder
            ->insertBatch($data);
        return $result;
    }

    public function getAllPermission($roleId)
    {
        return $this->db->table('role_permission')->select('*')
            ->join('role', 'role.id = role_permission.role_id', 'inner')
            ->join('module_action', 'module_action.module_id = role_permission.moduleActionId', 'inner')
            ->join('module', 'module.id = module_action.module_id', 'inner')
            ->join('action_name', 'action_name.id = module_action.action_id', 'inner')
            ->whereIn('role_id', $roleId)
            ->get()->getResultArray();
    }

    public function tableSearch($tbl, $col, $terms, $resultCol = '*')
    {
        $query = $this->db->table($tbl)->select($resultCol);
        $terms = strtolower($terms);
        if (is_array($col)) {
            $i = 0;
            foreach ($col as $v) {
                $v = "LOWER($v)";
                if ($i == 0) {
                    $query->like($v, $terms, 'both');
                } else {
                    $query->orLike($v, $terms, 'both');
                }
                $i++;
            }
        } else {
            $query->like($col, $terms, 'both');
        }
        // if ($deleted_type) {
        // $query->where('deleted_at is null ', null, false);
        // }
        return $query->limit(10)->get()->getResultArray();
    }
    public function countAll($tName, $active = true, $cond = [])
    {
        $builder = $this->db->table($tName)->select('id');
        if ($active) {
            if ($tName == 'event') {
                $builder->where($tName . '.deleted_at is null', null);
            } else {
                $builder->where($tName . '.status', 1);
            }
            if (count($cond)) {
                $builder->where($cond);
            }
        }
        return $builder->countAllResults('', false);
    }

    public function showTableColum($tbl)
    {
        $sql = "Show columns from ?";
        $excSql = $this->db->query("Show columns from " . $tbl);
        return $excSql->getResultArray();
    }
    public function getRole()
    {
        $query = $this->db->query('select * from role where isDeleted=0');
        return $query->getResultArray();
    }
    public function getOtp($mobile_no)
    {
        $query = $this->db->query('select * from otp where mobile_no=? order by created_at desc', [$mobile_no]);
        return $query->getResultArray();
    }
    public function executeQuery($query, $param, $page = null, $rows = 20)
    {
        $query = $this->db->query($query, $param);
        $data = $query->getResultArray();
        $query->freeResult();
        return $data;
    }

    public function executeNoResult($query, $param)
    {
        return $this->db->query($query, $param);
    }

    public function getAnyPreviousMonthDate($monthsBefore = null, $startDate = null)
    {
        $monthsBefore = $monthsBefore ?? 1; //php7
        $monthsBefore = abs($monthsBefore);
        $c = $startDate ?? date('Y-m-d');
        for ($i = 0; $i < $monthsBefore; $i++) {
            $c = date('Y-m-d', strtotime('last day of previous month ' . $c));
        }
        return $c;
    }

    public function updateHistory($id, $data, $date)
    {
        $sql = 'select id from sponsorship_history where sponsorship_id=? AND DATE_FORMAT(from_date,"%Y-%m") = DATE_FORMAT(?, "%Y-%m")';
        $query = $this->db->query($sql, [$id, $date]);
        $result = $query->getResultArray();
        $builder = $this->db->table('sponsorship_history');
        $expireDate = $this->getAnyPreviousMonthDate(1, $date);
        $fromDate = date("y-m-01", strtotime($date));
        $res = false;
        if (empty($result)) {
            $data['from_date'] = $fromDate;
            $res = $builder->set('expire_date', $expireDate)
                ->where('expire_date is Null', null, false)
                ->where('sponsorship_id', $id)
                ->update();
            $builder->insert($data);
        } else {
            $res = $builder->where('id', $result[0]['id'])
                ->update($data);
        }
        return $res;
    }

    public function generateId(string $des)
    {
        $description = (string) '"' . $des . '"';
        $query = $this->db->query('select * from master_config_id where property_name=' . $description . ' limit 1');
        $result = $query->getFirstRow();
        $prf = $result->prefix ?? '';
        $sep = $result->sep ?? '';
        $last_code = $result->last_key ? $result->last_key + 1 : 1;
        $padding = $result->padding ?? 0;
        $month = (int) $result->is_month_append ? date('m') : '';
        $year = (int) $result->is_year_append ? date('y') : '';
        $ext = $prf . $sep . $month . $year;
        $this->updateId($des, false);
        switch ($des) {
            case 'ADS_KEY':
                $last_code = sprintf("%03d", $last_code);
                break;
            case 'item_id':
                $iquery = $this->db->query('select COUNT(id)+1 as num from items');
                $last_code = $iquery->getResult()[0]->num;
                break;
        }
        $number = sprintf("%0" . $padding . "d", $last_code);
        $code = $ext . $number;
        return $code;
    }

    public function updateId($name, $hasRest = false)
    {
        $builder = $this->db->table('master_config_id');
        if ($hasRest) {
            $builder->set('month', date('m'), false);
            $builder->set('last_key', 0, false);
        } else {
            $builder->set('last_key', 'last_key+1', false);
        }
        $builder->where('property_name', $name);
        $builder->update();
    }

    function reportStats()
    {
    }
}
