<?php

namespace Core\Libraries;

use App\Infrastructure\Persistence\Alliance\SQLAlliance_partnerRepository;
use App\Infrastructure\Persistence\Events\SQLEvent_locationRepository;
use Core\Libraries\PDFGenerator;
use Core\Models\Utility\UtilityModel;
use App\Libraries\Email;
use Config\Services;

class EmailConetentGenerator
{
	private $pdf;
	static $response;
	private $UtilityModel;
	private $login_repository;
	private $eventLocationRepo;
	private $allianceRepo;
	public function __construct()
	{
		$this->UtilityModel = new UtilityModel();
		$this->login_repository = Services::UserLoginRepository();
        $this->eventLocationRepo=new SQLEvent_locationRepository();
        $this->allianceRepo=new SQLAlliance_partnerRepository();
	}
	public function genContentSentEmail($id, $data, $attachment = [], $sendEmail = true)
	{
		$c = $this->generateEmail($id, $data);
		$email = new Email();
		//pdf header replace
		$template = (array) $c['template'];
		
		return $email->mapWithConent($c['template'], $c['combineData'], $sendEmail, '', $attachment);
	}

	public function generateEmail($id, $data)
	{
		//$data        = $this->getDataFromUrl('json');
		$template = $this->UtilityModel->getDataById('email_template', array('id' => $id));
		
		$combineData = array();
		if (!empty($template)) {
			switch ((int) $template->module_id) {
				case 1:
					$combineData = $this->getLocationData($data['event_location_code']);
                    $combineData['email_id']=$combineData['user_name'];				
					break;
				case 2:
					$combineData = $this->allianceData($data['alliance_code']);
                    $combineData['email_id']=$combineData['user_name'];				
					break;
			}
	
			$template->to = $combineData['email_id'] ?? ($template->to ?? '') ?? '';
			$combineData['date'] = date('d M Y');
		}
		$template->emailName =  $combineData['first_name'] ?? '';
		return ["combineData" => $combineData, 'module_id' => $template->module_id, "template" => $template]; // to , cc body, pdf_content
	}

	private function getLocationData($event_location_code)
	{
		if (!empty($event_location_code)) {
			$data = $this->eventLocationRepo->findAllByWhere(['event_location_code' => $event_location_code])[0] ?? [];
		}
		return $data;
	}
	private function allianceData($alliance_code)
	{
		if (!empty($alliance_code)) {
			$data = $this->allianceRepo->findAllByWhere(['alliance_code' => $alliance_code])[0] ?? [];
		}
		return $data;
	}



	private function addressMap($adr)
	{
		if (empty($adr) || (is_array($adr) && count($adr) == 0)) {
			return '';
		}
		if (!isset($adr['street'])) {
			return '';
		}
		$addrArray = [$adr['street'] ?? '', $adr['address'] ?? '', $adr['districtName'] ? '<br>' . ($adr['districtName'] ?? '') : '', ($adr['stateName'] ?? '') ? '<br>' . $adr['stateName'] : '', ($adr['countryName'] ?? ''), $adr['pincode'] ? '- ' . $adr['pincode'] : ''];
		return implode(',', array_filter($addrArray, 'strlen'));
	}
}
