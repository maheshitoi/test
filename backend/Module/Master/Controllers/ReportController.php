<?php

namespace App\Controllers;

use App\Infrastructure\Persistence\Member\SQLMember_requestRepository;
use App\Infrastructure\Persistence\Payment\SQLPayment_detailsRepository;
use App\Infrastructure\Persistence\Member\SQLMemberRepository;
use Config\Services;
use Core\Controllers\BaseController;
use Core\Controllers\DMLController;
use Core\Libraries\ExportExcel;
use Core\Models\Utility\UtilityModel;

class ReportController extends BaseController
{
	use DMLController;
	private $login_repository;
	private $utility_repo;
	private $excelLib;
	private $memberRequestRepo;
	private $paymentdetailsRepo;
	private $memberRepo;
	public function __construct()
	{
		$this->initializeFunction();
		helper('Core\Helpers\File');
		helper('Core\Helpers\Utility');
		$this->login_repository = Services::UserLoginRepository();
		$this->utility_repo = new UtilityModel();
		$this->excelLib = new ExportExcel();
		$this->memberRequestRepo = new SQLMember_requestRepository();
		$this->paymentdetailsRepo = new SQLPayment_detailsRepository();
		$this->memberRepo = new SQLMemberRepository();
	}

	public function index() {}

	public function genReport($module)
	{
		$data = $this->getDataFromUrl('json');
		$cond = checkValue($data, 'condition');
		if (checkValue($data, 'is_active_only')) {
			$ob = (object) ['colName' => strtolower($module) . '.deleted_at is Null ', 'value' => null, 'operation' => 'AND'];
			array_push($cond, $ob);
		}

		$dataResult = [];
		switch (strtolower($module)) {
			case 'member_request':
				if (!empty($data['from_date']) && !empty($data['end_date'])) {
					$from_date = $data['from_date'];
					$end_date = $data['end_date'];
					$result = $this->memberRequestRepo->findWhereDateRange($from_date, $end_date);
				} else {
					$result = $this->memberRequestRepo->findAll();
				}
				foreach ($result as $k => &$v) {
					$dE['basic'] = $v;
					if (!empty($dE)) {
						array_push($dataResult, $dE);
					}
				}
				break;
			case 'payment_details':
				if (!empty($data['from_date']) && !empty($data['end_date'])) {
					$from_date = $data['from_date'];
					$end_date = $data['end_date'];
					$result = $this->paymentdetailsRepo->findWhereDateRange($from_date, $end_date);
				} else {
					$result = $this->paymentdetailsRepo->findAll();
				}
				foreach ($result as $k => &$v) {
					$dE['basic'] = $v;
					if (!empty($dE)) {
						array_push($dataResult, $dE);
					}
				}
				break;
			case 'member':
				if (!empty($data['from_date']) && !empty($data['end_date'])) {
					$from_date = $data['from_date'];
					$end_date = $data['end_date'];
					$result = $this->memberRepo->findWhereDateRange($from_date, $end_date);
				} else {
					$result = $this->memberRepo->findAll();
				}
				foreach ($result as $k => &$v) {
					$dE['basic'] = $v;
					if (!empty($dE)) {
						array_push($dataResult, $dE);
					}
				}
				break;
		}
		return $this->excelLib->export($dataResult, $data);
	}
}
