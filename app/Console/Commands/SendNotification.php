<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\TelegramNotification;

class SendNotification extends Command
{
    use TelegramNotification;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send a notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->sendTaskdatelineTrackingToTelegram();

        $this->SendMailContract();
    }
    //  php artisan send:notification
}
