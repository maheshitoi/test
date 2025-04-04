<?php

namespace App\Libraries;

use Core\Models\Utility\UtilityModel;

class whatsApp {
	private $response;
	private $utilityModel;
	public function __construct() {
		helper('Core\Helpers\Utility');
		$this->response = \Config\Services::response();
		$this->utilityModel = new UtilityModel();
	}
	public function send($id, $data, $mobile, $is_send = false)
    {
        $template = $this->utilityModel->getDataById('sms_templates', ['id' => $id]);
        $template->message = parameterReplace($data, $template->template_content ?? '');
        if ($is_send && (int) $template->allow_to_send) {
            $result = $this->whatsAppSend('TEXT', $mobile, $template->message);
            return $result;
        }
        return (array) $template->message;
    }
    public function whatsAppSend($type = 'TEXT', $mobile_no = '', $MorVariable = '', $file = '', $template_id = '')
    {
        
        $client = \Config\Services::curlrequest();
        if (ENVIRONMENT != 'testing') {
            $mobile_no = getenv("TEST.WHATSAPP");
        }
        
        sleep(1);
        $formData = array(
            "contact" => array(
                array()
            ),
            "appkey" => getenv("APPKEY"),
            "message" => $MorVariable,
            "authkey" => getenv("AUTHKEY"),
            "to" => getenv("TEST.WHATSAPP"),
            "template_id" => $template_id,
        );
            
            $url = getenv("URL");
            $req_url = filter_var($url, FILTER_SANITIZE_URL);
            $response = $client->post($req_url, [
                'headers' => [
                    'Accept' => '*',
                    'appkey' => getenv("APPKEY"),
                    'Content-Type' => 'application/json'
                ],
                'debug' => true,
                'form_params' => $formData,
            ]);
            
        $res = $response->getBody();
        $result = false;
        if (str_contains(trim(strtolower($res)), 'true')) {
            $result = true;
        }
        return ['result' => $result, 'response' => trim($res)];
    }
	public function whatsAppSendTemplate($mobile_no, $type, $data, $url = '') {
		$temp_id = '';
		$variable = [];
		switch ($type) {

		}
		return $this->whatsAppSend('TEMPLATE', $mobile_no, $variable, $url, $temp_id);
	}
}
