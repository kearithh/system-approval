<?php

namespace App\Console\Commands;

use App\PartialCode;
use Illuminate\Console\Command;

class ApprovalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'approval:command {fx?} {--tb?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $fx = $this->argument('fx');
//        $tb = $this->argument('tb');
        $temp = new PartialCode();
        $temp->$fx();
    }
}
