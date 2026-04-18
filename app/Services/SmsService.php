<?php

namespace App\Services;

require_once base_path('app/Services/sms/autoload.php');

use NotifyLk\Api\SmsApi;

class SmsService
{
    protected $api_instance;

    public function __construct()
    {
        $this->api_instance = new SmsApi();
    }

    public function sendSMS($user_id, $api_key, $message, $to, $sender_id)
    {
        try {
            $response = $this->api_instance->sendSMS(
                $user_id,
                $api_key,
                $message,
                $to,
                $sender_id
            );
            return $response;
        } catch (\Exception $e) {
            return 'Exception when calling SmsApi->sendSMS: ' . $e->getMessage();
        }
    }
}
