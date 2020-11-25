<?php

namespace App\Console\Commands;

use App\Jobs\ReceiveToReplyMail;
use App\Jobs\SendToStartMail;
use Illuminate\Console\Command;

class ReceiveReplyMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gp_email:receivemails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GP系统定时拉取回复邮件';

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
        $send_mail = new ReceiveToReplyMail('');
        $job = $send_mail->onConnection('database')->onQueue('gp_email_reply');
        dispatch($job);//分发任务到队列
        dispatch($job);//分发任务到队列
        return 1;
    }
}
