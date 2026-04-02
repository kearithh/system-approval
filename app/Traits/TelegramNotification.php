<?php

namespace App\Traits;

use Constants;
use Carbon\Carbon;
use App\Mail\SendMailContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Model\ContractMagement\Contracts;
use App\Model\ContractMagement\NotificationChannel;
use App\Model\ContractMagement\TaskDatelineTracking;

trait TelegramNotification
{
    private $apiBotTelegramToken = "5850722563:AAE55lmKBy04clmCPSMZ8s3Vo3ATJyfVU5s";
    public function sendMessageToTelegram($userTelegramChatId, $text)
    {
        $data = [
            'chat_id' => $userTelegramChatId,
            'text'    => $text,
        ];
        $url = "https://api.telegram.org/bot".$this->apiBotTelegramToken."/sendMessage?" .http_build_query($data);

        try {
            file_get_contents($this->curl_get_contents($url));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    private function curl_get_contents($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public function sendMessageToTelegramV2($userTelegramChatId, $text){
        $data = [
            'chat_id' => $userTelegramChatId,
            'text'    => $text,
        ];
        $url = "https://api.telegram.org/bot".$this->apiBotTelegramToken."/sendMessage?" .http_build_query($data);

        try {
            file_get_contents($url);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function SendMailContract()
    {
        try {
            $contracts       = Contracts::get();

            foreach($contracts as $contract){

                $obj            = json_decode($contract->data);
                $due_date       = date('Y-m-d', strtotime(@$obj->due_date));
                $startDate      = Carbon::parse(date('Y-m-d'));
                $endDate        = Carbon::parse($due_date);
                $date_diff      = $startDate->diffInDays($endDate);

                $head            = 'ជម្រាបជូន លោកគ្រូ/អ្នកគ្រូ ជាទីរាប់អាន!';
                $title           = "នេះជាការជូនដំណឹងអំពីកាលបរិច្ឆេទផុតកំណត់នៃកិច្ចសន្យាផ្សេងៗដែល បានកត់ត្រាទុកក្នុងប្រព័ន្ធ E-Approval";
                $desc            = "នៅសល់ ".$date_diff."​ថ្ងៃទៀតនឹងមានកិច្ចសន្យាដែលត្រូវផុតកំណត់ អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត។";
                $url             = request()->root().'/contract';
                $type            = "Notification";
                $name            = "Contract Management";
                $telegramMessage = "$head\n\n$title\n\n$desc\n\nតំណភ្ជាប់: $url\n\nសូមអរគុណ!";

                switch($obj->contract_type) {

                    case Constants::PROPERTY_RENTAL:
                        if($date_diff == Constants::CONTRACT_TYPE['PROPERTY_RENTAL']){
                            $users = NotificationChannel::whereNotNull('data->is_channel_email')
                                        ->whereIn('data->contract_management_role',[Constants::CONTRACT_MANAGEMEMNT_ADMIN, Constants::CONTRACT_MANAGEMEMNT_ALL])
                                        ->get();

                            $emails = [];
                            foreach ($users as $key => $value) {

                                $obj      = json_decode($value->data);
                                $emails[] = $obj->is_channel_email;

                                if($obj->is_channel_telegram){
                                    $this->sendMessageToTelegramV2(@$obj->is_channel_telegram, $telegramMessage);
                                }
                            }
                            //Mail::send(new SendMailContract($emails, $head, $title, $desc, $type, $url, $name));
                        }
                        break;

                    case Constants::STAFF_CONTRACT:
                        if($date_diff == Constants::CONTRACT_TYPE['STAFF_CONTRACT']){
                            $users = NotificationChannel::whereNotNull('data->is_channel_email')
                                        ->whereIn('data->contract_management_role',[Constants::CONTRACT_MANAGEMEMNT_HR, Constants::CONTRACT_MANAGEMEMNT_ALL])
                                        ->get();

                            $emails = [];
                            foreach ($users as $key => $value) {

                                $obj      = json_decode($value->data);
                                $emails[] = $obj->is_channel_email;

                                if($obj->is_channel_telegram){
                                    $this->sendMessageToTelegramV2(@$obj->is_channel_telegram, $telegramMessage);
                                }
                            }
                            //Mail::send(new SendMailContract($emails, $head, $title, $desc, $type, $url, $name));
                        }
                        break;

                    default:
                    if(in_array($date_diff,[Constants::CONTRACT_TYPE['SOFTWARE_LICENSE_RENEWAL'],Constants::CONTRACT_TYPE['VENDOR_AGREEMENT']])){

                        $users = NotificationChannel::whereNotNull('data->is_channel_email')
                                    ->whereIn('data->contract_management_role',[Constants::CONTRACT_MANAGEMEMNT_IT, Constants::CONTRACT_MANAGEMEMNT_ALL])
                                    ->get();

                        $emails = [];
                        foreach ($users as $key => $value) {

                            $obj      = json_decode($value->data);
                            $emails[] = $obj->is_channel_email;

                            if($obj->is_channel_telegram){
                                $this->sendMessageToTelegramV2(@$obj->is_channel_telegram, $telegramMessage);
                            }
                        }
                        //Mail::send(new SendMailContract($emails, $head, $title, $desc, $type, $url, $name));
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json(["Eerror" => $e]);
        }

    }
    public function sendTaskdatelineTrackingToTelegram(){
        try {
            $task       = TaskDatelineTracking::get();
            foreach($task as $tasks){

                $obj            = json_decode($tasks->data);
                $due_date       = date('Y-m-d', strtotime(@$obj->due_date));
                $startDate      = Carbon::parse(date('Y-m-d'));
                $endDate        = Carbon::parse($due_date);
                $date_diff      = $startDate->diffInDays($endDate);
                $head            = 'ជម្រាបជូន លោកគ្រូ/អ្នកគ្រូ ជាទីរាប់អាន!';
                // $title           = "នេះជាការជូនដំណឹងអំពីកាលបរិច្ឆេទផុតកំណត់នៃ Task ផ្សេងៗដែល បានកត់ត្រាទុកក្នុងប្រព័ន្ធ E-Approval";
                $desc            = "នៅសល់ ".$date_diff."​ថ្ងៃទៀតការងារខាងក្រោមនឹងដល់ Deadline:";
                $task            =  "Task: My"." ".$obj->description." "."task";
                $dateline        = "Deadline:".$due_date;
                $url             = request()->root().'/task-dateline-tracking';
                $telegramMessage = "$head\n\n$desc\n\n$task\n\n$dateline\n\nតំណភ្ជាប់: $url\n\nសូមអរគុណ!";

                if ($date_diff == Constants::TASKDATELINE && $obj->is_id_telegram) {
                    $this->sendMessageToTelegramV2(@$obj->is_id_telegram, $telegramMessage);
                }
            }
        } catch (\Exception $e) {
            return response()->json(["Eerror" => $e]);
        }
    }
}
