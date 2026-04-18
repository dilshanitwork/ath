<?php

namespace App\Http\Controllers;

use App\Services\SmsService;
use Illuminate\Support\Carbon;

class SmsController extends Controller
{
    /**
     * Send an SMS to a mobile number with message and contact details.
     */
    public function sendSmsToMobile($mobile, $message, $contact_fname = '', $contact_lname = '', $contact_email = '', $contact_address = '', $contact_group = 0, $type = 'unicode')
    {
        // Remove spaces
        $mobile = str_replace(' ', '', $mobile);

        // Remove first 0 if present
        if (substr($mobile, 0, 1) === '0') {
            $mobile = substr($mobile, 1);
        }

        // Add 94 at the beginning
        $mobile = '94' . $mobile;

        // Ensure the total length is 11
        if (strlen($mobile) !== 11) {
            // Handle invalid mobile number (log, skip, etc.)
            return ['status' => false, 'message' => 'Invalid mobile number'];
        }

        // You may choose to move these to .env or config
        $user_id = '29640'; // Replace with your API User ID
        // $api_key = "wIVSo12v14uVoQ62GwMx"; // Replace with your API Key
        $sender_id = 'KAVINDU PAY'; // Replace with your sender ID

        $smsService = new SmsService();

        $response = $smsService->sendSMS($user_id, $api_key, $message, $mobile, $sender_id, $contact_fname, $contact_lname, $contact_email, $contact_address, $contact_group, $type);

        return $response;
    }

    /**
     * Example usage: direct test
     */
}
