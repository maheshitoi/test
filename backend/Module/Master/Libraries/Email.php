<?php

namespace App\Libraries;

use Core\Libraries\PDFMaker;
use Core\Models\Utility\UtilityModel;
use Mail\Infrastructure\Persistence\Email\SQLEmail_listRepository;

class Email {

	static $email;
	private $repository;
	private $userData;
	public function __construct() {
		self::$email = \Config\Services::email();
		// $this->repository = new SQLEmail_listRepository();
		$this->userData = \AUTHORIZATION::getTokenData();
	}

	public function send($data) {
		$to = isset($data['to']) ? $data['to'] : null;
		$cc = isset($data['cc']) ? $data['cc'] : null;
		$bcc = isset($data['bcc']) ? $data['bcc'] : null;
		$subject = isset($data['subject']) ? $data['subject'] : null;
		$mailMessage = isset($data['body']) ? $data['body'] : null;
		$data['name'] = $data['emailName'] ?? $data['name'] ?? '';
		$data['email_to'] = $to;
		sleep(1);
		$data['status'] = 2;
		$userId = $this->userData->user_id ?? 1;
		$fname = $this->userData->fname ?? 'Admin';
		if (!checkValue($data, 'id')) {
			$data['created_by'] = $userId;
			$data['created_byName'] = $fname;
		} else {
			$data['resent_by'] = $userId;
			$data['resent_byName'] = $fname;
			$data['resent_on'] = date('Y-m-d H:i:s');
		}
		$email = \Config\Services::email();
		$email->clear(true);
		$email->setFrom(MAIL_FROM_USER, MAIL_FROM_USER_NAME);
		$email->setTo(ENVIRONMENT != 'production' ? TEST_EMAIL : $to);
		
		if (!empty($cc) && ENVIRONMENT == 'production') {
			$email->setCC('');
		}
		if (ENVIRONMENT == 'development') {
			$email->setCC('agootechnology@gmail.com');
		}
		if (!empty($bcc) && ENVIRONMENT == 'production') {
			$email->setBCC($bcc);
		}
	
		$email->setSubject($subject);
		$email->setMessage($mailMessage);
		
		if (!empty($data['attachment'])) {
			$attachment = $data['attachment'];
		$email->attach($attachment);
		}
		
		$result = $email->send(true) ? 1 : 3; // success / failed
		
		// print_r(self::$email->printDebugger());
		return $result;
	}
	// map with Content
	public function mapWithContent($template, $combineData, $send = false, $email_id = '', $attachment = []) {
		// print_r($attachment);
		// return;
		$templateId = is_scalar($template) ? ((int) $template ? $template : 0) : 0;
		$combinedResult = (array) $combineData;
		
		if ($templateId) {
			$utilityRepo = new UtilityModel();
			$template = $utilityRepo->getDataById('email_template', array('id' => $templateId));

		}

		$template->body = parameterReplace($combinedResult, $template->body);
		$template->subject = parameterReplace($combinedResult, $template->subject);
		if ($template->pdf_content) {
			$template->pdf_content = parameterReplace($combinedResult, $template->pdf_content);
		}
		$template->attachment = $attachment;
		$template->to = $combinedResult['to'] ?? $template->to ?? '';
		if (!empty($email_id)) {
			$template->to = $email_id;
		}
		$template->cc = $combinedResult['cc'] ?? '';
		$template->bcc = $combinedResult['bcc'] ?? $template->bcc ?? '';
		if ($send) {
			$template->combineData = $combinedResult;
			if (isset($template->id)) {
				unset($template->id);
			}
			return $this->send((array) $template);
		}
		$template->combineData = $combinedResult;
		return true;
	}
}
