<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Services\SmsService;

Artisan::command('sms:send-daily', function () {
    $tomorrow = Carbon::today()->addDay()->toDateString();

    $mobile = '';
    $message = '';

    $bills = DB::table('bills')->join('customers', 'bills.customer_id', '=', 'customers.id')->whereDate('bills.next_bill', $tomorrow)->select('customers.mobile', 'customers.name', 'bills.bill_number', 'bills.next_bill')->get();

    // You may choose to move these to .env or config
    $user_id = '29640'; // Replace with your API User ID
    // $api_key = "wIVSo12v14uVoQ62GwMx"; // Replace with your API Key
    $sender_id = 'KAVINDU PAY'; // Replace with your sender ID

    foreach ($bills as $bill) {
        // Format mobile number
        $mobile = str_replace(' ', '', $bill->mobile);
        if (substr($mobile, 0, 1) === '0') {
            $mobile = substr($mobile, 1);
        }
        $mobile = '94' . $mobile;

        if (strlen($mobile) !== 11) {
            Log::warning("Invalid mobile number for customer {$bill->name}: {$bill->mobile}");
            continue;
        }

        $message = 'Hi ' . $bill->name . '!, Reminder: Payment for Bill ' . $bill->bill_number . ' is due on ' . $bill->next_bill . '. Please pay on time to avoid late fees. - Tyre Management System';

        $smsService = new SmsService();

        $response = $smsService->sendSMS($user_id, $api_key, $message, $mobile, $sender_id);
    }
})
    ->describe('Send SMS daily at 23:30')
    ->dailyAt('23:30') // schedule frequency: daily at 15:00
    ->withoutOverlapping() // prevent overlap
    ->onOneServer() // single-server lock
    ->runInBackground(); // don’t block the scheduler
