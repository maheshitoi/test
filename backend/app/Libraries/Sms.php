<?php
namespace Core\Libraries;

use Core\Models\Utility\UtilityModel;

class Sms
{
    private $response;
    private $UtilityModel;
    public function __construct()
    {
        helper('Core\Helpers\Utility');
        $this->UtilityModel = new UtilityModel();
        $this->response     = \Config\Services::response();
    }
    public function send($id, $data, $is_send = false)
    {
        $template          = $this->UtilityModel->getDataById('sms_templates', ['id' => $id]);
        $template->message = parameterReplace($template->parameter, $data, $template->template_content ?? '');
        if ($is_send && (int) $template->allow_to_send) {
            return $this->sendSMS($template->message, $data['mobile_no'], $template->sender_id ?? '', $template->message_type);
        }
        return (array) $template;
    }

    public function sendSMS($message, $mobile_no, $sendID = 'IAOIIN', $type = 2)
    {
        $client    = \Config\Services::curlrequest();
        $mobile_no = trimMobileNumber($mobile_no);
        $message = str_replace(' ', '%20', $message);
        $url     = sprintf('https://bulksmsplans.com/api/send_sms?api_id=APIL6gcrjoC134209&api_password=vDKaRWEo&sms_encoding=text&template_id', $message, $mobile_no, $type, $sendID);
        $response = $client->request('GET', $url, ['headers' => [
            'Accept' => '*']]);
        $res    = $response->getBody();
        $result = false;
        if((int) trim($res)){
            $result = true;
        }
        return ['result' => $result, 'response' => trim($res)];
    }

}
