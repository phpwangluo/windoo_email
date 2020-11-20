<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\SendToStartMail;

class SendStarterEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'starter:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'gp系统客户发送邮件';

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
     * @return int
     */
    public function handle()
    {
        //业务逻辑
        $send_mail = new SendToStartMail('');
        $job = $send_mail->onConnection('database')->onQueue('emails');
        dispatch($job);//分发任务到队列
    }
}
