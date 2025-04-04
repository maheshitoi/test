<?php

namespace Core\Controllers;

use Config\Services;
use Core\Domain\Exception\RecordNotFoundException;
use Core\Domain\User\UserRepository;
use Core\Libraries\ExportExcel;
use Core\Models\Logs\LogsModel;
use Core\Models\Utility\UtilityModel;
use Vtiful\Kernel\Excel;

class Utility extends BaseController
{

	private $repository;
	private $utilityRepo;
	private $LogModel;
	private $appConfig;
	public function __construct()
	{
		helper('Core\Helpers\UserAgent');
		$this->initializeFunction();
		$this->utilityRepo = new UtilityModel();
		$this->LogModel = new LogsModel();
		$this->appConfig = new \Config\AppConstant();
		$this->repository = Services::UserRepository();
	}
	public function getCountryPhone()
	{
		$json_url = BASEURL . '/public/countries.json';
		$json = file_get_contents($json_url);
		$data = json_decode($json, true);
		return $this->message(200, $data);
	}
	public function isEmailUnique()
	{

		if ($this->reqMethod == 'post') {
			$data = $this->getDataFromUrl('json');
			$email = $data['email'];
			if ($email) {
				try {
					$data = $this->repository->findUserByUserName($email);
					if (!empty($data)) {
						$data = false;
					} else {
						$data = true;
					}
				} catch (RecordNotFoundException $e) {

					return $this->message(404, $e->getMessage());
				}
				return $this->message(200, $data);
			} else {
				return $this->message(400, null, 'Bad Request');
			}
		} else {
			return $this->message(400, null, 'Method Not Allowed');
		}
	}

	public function getRole()
	{
		$data = $this->utilityRepo->getRole();
		return $this->message(200, $data);
	}

	public function getById($tbl, $id)
	{
		if (strtolower($this->reqMethod) == 'get') {
			$where = [$tbl . '.id' => $id];
			$data = $this->utilityRepo->getDataById($tbl, $where);
			if (isset($data->id) && $tbl == 'form_purpose') {
				$d = json_decode($data->form_schema);
				$data->form_schema = $d;
			}
			return $this->message(200, $data);
		} else {
			return $this->message(400, null, 'Method Not Allowed');
		}
	}

	public function saveData($table)
	{
		if (strtolower($this->reqMethod) == 'post') {
			$req_data = $this->getDataFromUrl('json');
			$where = array();
			switch ($table) {
				case 'department':
					$where['ad_id'] = $req_data['ad_id'] ?? '';
					$where['dName'] = $req_data['dName'] ?? '';
					break;
				case 'form_purpose':
					$rq = json_encode($req_data['form_schema']);
					$req_data['form_schema'] = $rq;
					break;
			}
			if (!empty($where)) {
				$data_p = $this->utilityRepo->getDataById($table, $where);
				if (!empty($data_p)) {
					if (!isset($req_data['id']) || $data_p->id != $req_data['id']) {
						return $this->message(400, 'Data Already Exisits');
					}
				}
			}
			//map Domain
			$filteredData = $this->mapColumn($table, $req_data);
			if (checkValue($req_data, 'id')) {
				$data = $this->utilityRepo->updateData($table, $filteredData, ['id' => $req_data['id']]);
			} else {
				$data = $this->utilityRepo->addNewData($table, $filteredData);
			}
			return $this->message(200, $data);
		} else {
			return $this->message(400, null, 'Method Not Allowed');
		}
	}
	protected function mapColumn($tName, $reqData)
	{
		$res = $this->utilityRepo->showTableColum($tName);
		$filteredData = [];
		foreach ($res as $key) {
			$field = $key['Field'];
			if (isset($reqData[$field])) {
				$filteredData[$field] = $reqData[$field];
			}
		}
		return $filteredData;
	}

	public function exportData($tName = '')
	{
		$req_data = $this->getDataFromUrl('json');
		return $this->utilityRepo->exportExcel($tName, $req_data);
	}

	public function getData($join, $table, $c1 = array(), $limit = 0)
	{
		if ($table == 'country_code') {
			return $this->getCountryPhone();
		}
		$cond = array();
		if ($c1) {
			$cond = json_decode(utf8_decode(urldecode($c1)));
		}
		$ftbl = false;
		if ($limit !== 0) {
			$ftbl = json_decode(utf8_decode(urldecode($limit)));
		}
		$where = array();
		$tbl = $table;
		$tbl = ($tbl == 'city' || $tbl == 'panchayat') ? 'city' : $tbl;
		switch ($tbl) {
			case 'district':
				array_push($where, 'district.state_id');
				break;
			case 'state':
				array_push($where, 'state.country_id');
				break;
			case 'asset_category':
				array_push($where, 'asset_category.asset_type_id');
				break;
			case 'asset_sub_category':
				array_push($where, 'asset_sub_category.asset_category_id');
				break;

			case 'city':
				array_push($where, $table . '.sub_district_id');
				break;
			case 'ward':
				array_push($where, $table . '.city_id');
				break;
			case 'village':
				array_push($where, $table . '.panchayat_id');
				break;
			case 'subdistrict':
				array_push($where, $table . '.district_id');
				break;
			case 'department':
				array_push($where, 'department.ad_id');
				break;
			case 'designation':
				array_push($where, 'designation.branch_id');
				break;

			case 'zone':
				array_push($where, 'zone.region_id');
				break;
			case 'email_template':
				array_push($where, 'module_id');
				break;
			case 'church':
				array_push($where, 'f.zone');
				break;
				/*payroll */
			case 'account_scheme':
				array_push($where, 'account_scheme.account_category_id');
				break;
			case 'payroll_group':
				array_push($where, 'payroll_group.trust_fk_id');
				break;
			case 'salary_category':
				array_push($where, 'payroll_type_fk_id');
				break;
		}

		if (strtolower($this->reqMethod) == 'get') {
			if (count($cond)) {
				$slice = array_slice($where, 0, count($cond));
				$where = [];
				foreach ($cond as $k => $v) {
					$where[$slice[$k]] = $v;
				}
			} else {
				$where = array();
			}
			$key = array_search('all', $cond);
			if ($key < -1) {
				if ($table != 'field' && $table != 'church') {
					$where[$table . '.status'] = 1;
				}
			} else if ($key >= 0) {
				if (array_key_exists($key, $cond)) {
					unset($cond[$key]);
				}
			}
			$data = $this->utilityRepo->getData($table, $where, $join, $ftbl);
			return $this->message(200, $data);
		}
	}
	public function search($tbl, $terms)
	{
		$col = [];
		switch ($tbl) {
			case 'staff':
				$col = ['name', 'email_id', 'mobile_no', 'staff_emp_id'];
				break;
		}
		return $this->message(200, $this->utilityRepo->tableSearch($tbl, $col, $terms));
	}

	public function save_role_permission($id)
	{
		if (strtolower($this->reqMethod) == 'post') {
			$req_data = $this->getDataFromUrl('json');
			if (isset($req_data['role']) && $id) {
				$data = $this->utilityRepo->updateRolePermission($req_data['role'], $id);
				return $this->message(200, $data);
			} else {
				return $this->message(401, null, 'data required');
			}
		} else {
			return $this->message(400, null, 'Method Not Allowed');
		}
	}

	public function savePromotional()
	{
		if (strtolower($this->reqMethod) == 'post') {
			$req_data = $this->getDataFromUrl('json');
			if (!checkValue($req_data, 'promotionalName')) {
				return $this->message(400, null, 'Promotional Name required');
			}
			if (!checkValue($req_data, 'phone')) {
				return $this->message(400, null, 'Phone Number required');
			}

			if (checkValue($req_data, 'promotional_id')) {
				$proData = $this->utilityRepo->getDataById('promotional_office', ['promotional_id' => $req_data['promotional_id']]);
				if (empty($proData)) {
					return $this->message(400, null, 'Promotional Data Not found please check your promotional id');
				}
			}

			if (isset($req_data['promotional_id']) && !empty($req_data['promotional_id'])) {

				$res = $this->utilityRepo->updateData('promotional_office', $req_data, ['promotional_id' => $req_data['promotional_id']]);

				return $this->message($res ? 200 : 400, $req_data, $res ? 'Successfully Saved' : 'Oops Something went to wrong');
			} else {
				$req_data['promotional_id'] = generateKey('PROMOTIONAL_OFFICE');
				$res = $this->utilityRepo->addNewData('promotional_office', $req_data);
				return $this->message($res ? 200 : 400, $req_data, $res ? 'Successfully Saved' : 'Oops Something went to wrong');
			}
		} else {
			return $this->message(401, null, 'Invalid Call');
		}
	}

	public function getAllPermission($role)
	{
		$cond = explode(',', $role);
		$data = $this->utilityRepo->getAllPermission($cond);
		return $this->message(200, $data);
	}

	public function importData()
	{
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		$arySt = [
			"uttara" => 20, "jammu" => 22, "hariyana" => 21, "mehalaya" => 23, "arunachal" => 24, "manipur" => 25, "nagaland" => 26, "tripura" => 27, "mizoram" => 28, "delhi" => 29, "goa" => 30, "andaman" => 31, "ladakh" => 32, "diu" => 33, 'asam' => 15, "chhattisgarh" => 16, "gujarat" => 18, "himachal" => 17, "odisha" => 12, "punjab" => 19, "rajasthan" => 13, "west_bengal" => 14,
			"chandigar" => 34, "lakshdweep" => 35, "skkim" => 36
		];

		foreach ($arySt as $arKey => $arValue) {
			$json_url = BASEURL . '/public/' . $arKey . '.json';
			$json = file_get_contents($json_url);
			$data = json_decode($json, true);
			$vArray = array();
			$dArray = array();
			$sArray = array();
			$pArray = array();
			$cArray = array();
			$wArray = array();
			$dataMap = array('country_id' => 1, 'state_id' => $arValue);
			$i = 0;
			foreach ($data as $k => $v) {
				foreach ($v as $key => $value) {
					print_r($value);
					if (!isset($dArray[$value['Dt_Code']])) {
						$districtData = $dataMap;
						$districtData['districtName'] = $value['Dt_Name'];
						$res = $this->utilityRepo->addNewData('district', $districtData);
						$dArray[$value['Dt_Code']] = $res;
					}

					if (!isset($sArray[$value['Sbdt_Code']])) {
						$sub_districtData = $dataMap;
						$sub_districtData['district_id'] = $dArray[$value['Dt_Code']];
						$sub_districtData['subDistrictName'] = $value['Sbdt_Name'];
						$res = $this->utilityRepo->addNewData('subdistrict', $sub_districtData);
						$sArray[$value['Sbdt_Code']] = $res;
					}
					if (isset($value['Pnchyt_Code'])) {
						$pCode = $value['Sbdt_Code'] . '' . $value['Pnchyt_Code'];
						if (!isset($pArray[$pCode])) {
							$panData = $dataMap;
							$panData['district_id'] = $dArray[$value['Dt_Code']];
							$panData['sub_district_id'] = $sArray[$value['Sbdt_Code']];
							$panData['pName'] = $value['Pnchyt_Name'];
							$res = $this->utilityRepo->addNewData('panchayat', $panData);
							$pArray[$pCode] = $res;
						}
					}
					if (isset($value['V_Code'])) {
						if (!isset($vArray[$value['V_Code']])) {
							$pCode = $value['Sbdt_Code'] . '' . $value['Pnchyt_Code'];
							$villageData = $dataMap;
							$villageData['district_id'] = $dArray[$value['Dt_Code']];
							$villageData['sub_district_id'] = $sArray[$value['Sbdt_Code']];
							$villageData['panchayat_id'] = $pArray[$pCode];
							$villageData['vName'] = $value['V_Name'];
							$res = $this->utilityRepo->addNewData('village', $villageData);
							$vArray[$value['V_Code']] = $res;
						}
					}
					if (isset($value['Town_City_Code'])) {
						if (!isset($cArray[$value['Town_City_Code']])) {
							$cityData = $dataMap;
							$cityData['district_id'] = $dArray[$value['Dt_Code']];
							$cityData['sub_district_id'] = $sArray[$value['Sbdt_Code']];
							$cityData['cityName'] = $value['Town_City_Name'];
							$res = $this->utilityRepo->addNewData('city', $cityData);
							$cArray[$value['Town_City_Code']] = $res;
						}
					}

					if (isset($value['Ward_No'])) {
						$wardData = $dataMap;
						$wardData['district_id'] = $dArray[$value['Dt_Code']];
						$wardData['sub_district_id'] = $sArray[$value['Sbdt_Code']];
						$wardData['city_id'] = $cArray[$value['Town_City_Code']];
						$wardData['wardName'] = $value['Ward_Name'];
						$res = $this->utilityRepo->addNewData('ward', $wardData);
						//$vArray[$value['V_Code']]       = $res;
					}
					echo $i++ . " data inserted <br>";
				}
			}
		}
		echo 'imported';
	}

	public function genModule($table, $moduleName, $appName = 'core')
	{
		$tblU = ucwords($table);
		$modU = ucwords($moduleName);
		$db = new UtilityModel($appName == 'core' ? 'master' : $appName);
		$res = $db->showTableColum($table);
		$appNamespace = ucfirst(strtolower($appName));
		$basePath = $appName == 'core' ? APPPATH : ROOTPATH . '/Module/' . $appNamespace;
		$controllerFilePath = $basePath . "/Controllers/" . $modU . '/' . $tblU . "Controller.php";
		$entityFilePath = $basePath . "/Domain/" . $modU . '/' . $tblU . ".php";
		$entityRepoFilePath = $basePath . "/Domain/" . $modU . '/' . $tblU . "Repository.php";
		$modelFilePath = $basePath . "/Models/" . $modU . '/' . $tblU . "Model.php";
		$modalRepoFilePath = $basePath . "/Infrastructure/Persistence/" . $modU . '/SQL' . $tblU . "Repository.php";
		$entitiyText = '';
		$modalText = '';
		$eliminate = array("created_at", "updated_at", "deleted_at", "id");
		foreach ($res as $key => $value) {
			if (in_array($value['Field'], $eliminate)) {
				continue;
			}
			$entitiyText .= "'" . $value['Field'] . "'=> null,";
			$modalText .= "'" . $value['Field'] . "',";
		}

		$txt = '<?php
namespace "' . $appNamespace . '"\Domain\"' . $modU . '";

use CodeIgniter\Entity;
    class "' . $tblU . '" extends Entity
    {
         protected $attributes = [ "' . $entitiyText . '"
         ];
    }
?>';

		$mtxt = '<?php
namespace "' . $appNamespace . '"\Models\"' . $modU . '";
use CodeIgniter\Model;

    class "' . $tblU . '"Model extends Model
    {
        protected $table      = `"' . $table . '"`;
    protected $primaryKey = `id`;
    protected $returnType = `"' . $appNamespace . '"\Domain\"' . $modU . '"\"' . $tblU . '"`;
    protected $useSoftDeletes = true;
         protected $allowedFields = [ "' . $modalText . '"];
         protected $useTimestamps = true;
    }
?>';

		$repotxt = '<?php
namespace "' . $appNamespace . '"\Domain\"' . $modU . '";

use Core\Domain\DMLRepository;

interface "' . $tblU . '"Repository extends DMLRepository
{

}?>';
		$repoModal = '<?php
namespace "' . $appNamespace . '"\Infrastructure\Persistence\"' . $modU . '";
use Core\Domain\Exception\RecordNotFoundException;
use "' . $appNamespace . '"\Domain\"' . $modU . '"\"' . $tblU . '"Repository;
use "' . $appNamespace . '"\Domain\"' . $modU . '"\"' . $tblU . '";
use Core\Infrastructure\Persistence\DMLPersistence;
use "' . $appNamespace . '"\Models\"' . $modU . '"\"' . $tblU . '"Model;

class SQL"' . $tblU . '"Repository implements "' . $tblU . '"Repository
{
    use DMLPersistence;

    /** @var AppModel */
    protected $model;

    public function __construct()
    {
        $this->model = new "' . $tblU . '"Model();
    }
    public function setEntity($d)
    {
        return new "' . $tblU . '"($d);
    }

}';

		$controllerTxt = '<?php
namespace "' . $appNamespace . '"\Controllers\"' . $modU . '";
use Core\Controllers\BaseController;
use Core\Controllers\DMLController;
use "' . $appNamespace . '"\Infrastructure\Persistence\"' . $modU . '"\SQL"' . $tblU . '"Repository;

class "' . $tblU . '"Controller extends BaseController
{
    use DMLController;
    private $repository;
    private $userAgentHepler;
    public function __construct()
    {
        $this->initializeFunction();
        $this->repository = new SQL"' . $tblU . '"Repository();
    }
}';
		fileWrite($entityRepoFilePath, $repotxt, ['"']);
		fileWrite($controllerFilePath, $controllerTxt, ['"']);
		fileWrite($modalRepoFilePath, $repoModal, ['"']);
		fileWrite($entityFilePath, $txt, ['"']);
		fileWrite($modelFilePath, $mtxt, ['"', '`'], ['', "'"]);

		foreach ($res as $key => $value) {
			echo '<pre>';
			echo "'" . $value['Field'] . "'=> null,";
			echo '</pre>';
		}
	}

	public function sendEmail()
	{
		$data = $this->getDataFromUrl('json');
	}

	public function generateId($name)
	{
		if (strtolower($this->reqMethod) == 'get') {
			$data = $this->utilityRepo->generateId($name);
			return $this->message(200, $data);
		} else {
			return $this->message(400, null, 'Method Not Allowed');
		}
	}

	public function exportExcel()
	{
		//$this->load->helper('download');

		$filename = "website_data_" . date('Ymd') . ".xls";

		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$flag = false;
		$data = $this->utilityRepo->getData('zone', []);
		foreach ($data as $row) {
			if (!$flag) {
				// display field/column names as first row
				echo implode("\t", array_keys($row)) . "\r\n";
				$flag = true;
			}
			array_walk($row, __NAMESPACE__ . '\cleanData');
			echo implode("\t", array_values($row)) . "\r\n";
		}
		flush();
		readfile($filename);
		exit;
	}

	public function sendOTP($mobile_no, $type)
	{
		$otp = $this->otpLib->generate($mobile_no, $type);
		return $this->message($otp['result'] == true ? 200 : 400, $otp, $otp['response'] ?? 'OTP Generated');
	}
	public function resendOTP($mobile_no, $type)
	{
		$otp = $this->otpLib->reSend($mobile_no, $type);
		return $this->message($otp['result'] == true ? 200 : 400, $otp, $otp['response']);
	}
	public function getAppVersion()
	{
		$rows = $this->utilityRepo->executeQuery('Select * from settings where description =? or description =?', ['APP_VERSION', 'BLOCK_LIST']) ?? [];
		$data = [];
		foreach ($rows as $key => $v) {
			if ($v['description'] == 'BLOCK_LIST') {
				$b = explode(',', $v['value'] ?? '');
				$data['blockList'] = [];
				foreach ($b as $k => $v) {
					$mobile_no = trimMobileNumber($v, true);
					$mobile_no = '91' . $mobile_no;
					array_push($data['blockList'], $mobile_no);
				}
			} else if ($v['description'] == 'APP_VERSION') {
				if (!empty($v['value'])) {
					$data['appVersion'] = json_decode($v['value'], true);
				}
			}
		}
		return $this->message($data == true ? 200 : 400, $data, 'Success');
	}

	public function cleanData(&$str)
	{
		$str = preg_replace("/\t/", "\\t", $str);
		$str = preg_replace("/\r?\n/", "\\n", $str);
	}

	public function uploadTempFile()
	{
		$data = array();
		if (strtolower($this->reqMethod) == 'post') {
			$imagefile = $this->request->getFiles();
			if ($imagefile) {
				if (validatFileExtension($imagefile)) {
					if (!validateSize($imagefile)) {
						return $this->message(400, null, 'File size exceed..');
					}
				} else {
					return $this->message(400, null, 'File type Not Allowed..');
				}
			}
			$tempPath = getTempPath();
			//foreach ($imagefile['files'] as $img) {
			$fileName = uploadFile($imagefile['files'], $tempPath);
			$data['file_name'] = $fileName;
			$data['file_path'] = getFilePath($tempPath, $fileName);
			//}
			return $this->message(200, $data);
		} else {
			return $this->message(400, null, 'Method Not Allowed');
		}
	}

	public function mapExcelData()
	{
		$excel = new ExportExcel();
		$data = $excel->mapExcelData();
		return $this->message(200, $data, 'success');
	}
}
