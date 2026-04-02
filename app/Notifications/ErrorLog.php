<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class ErrorLog extends Notification
{
    /**
     * @param \Exception $e
     */
    public static function sendTelegram(\Exception $e)
    {
        if (env('IS_ERROR_SEND_TELEGRAM', 0) && Request::ip()) {
            $text = ""
                . "<b>Dear value developer \n </b>"
                . "E-Approve Error Now...\n"
                . "<b>* Error message: \n</b>"
                .  $e->getMessage()."​​​​ \n\n"
                . "<b>* IP Address:</b>".Request::ip()." \n"
                . "<b>* Route Name:</b>".Request::url()." \n"
                . "<b>* User Agent:</b>".Request::server('HTTP_USER_AGENT')." \n"
                . "<b>* Auth:</b>".auth()->user()->username." \n"
                . "<b>* App Name:</b>".Request::$app->environment()." \n"
                . "<b>* Current User:</b>".get_current_user()."\n"
                . "<b>* Param:</b>\n"
                . json_encode(Request::all())."\n"
                . "\nPlease find log file for detail\n"
                . "\u{1F602}\u{1F602}\u{1F602} \n"
                . "Thank you"
            ;

            Telegram::sendMessage([
                'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
                'parse_mode' => 'HTML',
                'text' => $text
            ]);
        }
//        else {
//            self::sendMail($e);
//        }
    }
}
