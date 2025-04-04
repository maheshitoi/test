<?php

namespace Core\Libraries;

use Core\Models\Utility\UtilityModel;

class FCM
{
    static $response;
    private $UtilityModel;
    public function __construct()
    {
        self::$response = \Config\Services::response();
        $this->UtilityModel = new UtilityModel();
    }

    public function sendPushNotification($name, $data)
    {
        // $table = $this->UtilityModel->getDataById('send_fcm_notification', array('event_name' => $name));
        $notification_id = $data['deviceId'];
        $title = 'Time Pass';
        // $title = $table->title;
        $message = $data['message'];
        $id    = '123';
        $d_type = 'andriod';
        $accesstoken = 'AAAAMWqVIPU:APA91bFdflpH7FMNEB2oH3xABpLQ74JA0VMt51flZLErrg58ck8JfmsCdMZzOTt96rJLW0SF1SpMOpFindeTGEczbZ5D4bwK358-UUax8lHnxbI2n-t6-mS9SeLBZyzxaK2tIZRLxXFv';

        $URL = 'https://fcm.googleapis.com/fcm/send';
        if ($title == 'ORDER_REJECTED' || $title == 'ORDER_POSTING') {
            $payload = '{
	            "to" : "' . array($notification_id) . '",
	            "data" : {
	              "body" : "",
	              "title" : "' . $title . '",
	              "type" : "' . $d_type . '",
	              "id" : "' . $id . '",
	              "message" : "' . $message . '",
	            }
	          }';
            // print_r($post_data);die;

        } else {
            $fields = array(
                "registration_ids" => array($notification_id),
                "priority" => "normal",
                'notification' => array(
                    'title' => $title,
                    'body' => $message
                )
            );

            $payload = json_encode($fields);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: key=' . $accesstoken));
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}
