<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use App\User;
use Auth;
class SendMailContract extends Mailable
{
    use Queueable, SerializesModels;
    public $emails;
    public $head;
    public $title;
    public $name;
    public $type;
    public $url;
    public $desc;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emails, $head, $title, $desc, $type, $url, $name)
    {
        $this->emails = $emails;
        $this->head = $head;
        $this->title = $title;
        $this->name = $name;
        $this->type = $type;
        $this->url = $url;
        $this->desc = $desc;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(Request $request)
    {
        $cioMail = 'bunthach@sahakrinpheap.com.kh';
        // if(in_array($cioMail, $this->emails)) {
        //     $this->emails = [$cioMail];
        //     //$this->emails = [];
        // } else {
        //     $this->emails = [];
        //}

        if (env('APP_ENV') == 'local') {
            // $this->emails = [];
        }

        return $this->view('send_mail_contract', [
                            'head'=>$this->head,
                            'title'=>$this->title,
                            'name'=>$this->name,
                            'type'=>$this->type,
                            'url'=>$this->url,
                            'desc'=>$this->desc])
                    ->from(env('MAIL_USERNAME'), 'E-Approval')
                    ->to($this->emails)
                    ->bcc(['phollinhuy04@gmail.com'])
                    ->subject($this->name);

    }
}
