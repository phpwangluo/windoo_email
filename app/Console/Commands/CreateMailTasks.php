<?php

namespace App\Console\Commands;

use App\Jobs\CreateToMailTasks;
use Illuminate\Console\Command;

class CreateMailTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gp_email:createtasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'gp系统生成邮件发送任务';

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
        $send_mail = new CreateToMailTasks('');
        $job = $send_mail->onConnection('database')->onQueue('gp_email_create');
        $job_code = dispatch($job);//分发任务到队列
        return $job_code;
    }
}
