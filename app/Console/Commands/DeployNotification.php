<?php

namespace App\Console\Commands;

use App\Facades\Slack;
use Illuminate\Console\Command;

class DeployNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployment:notify {status=started}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Slack deployment notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (app()->isProduction()) {
            $status = $this->argument('status');

            if ($status == 'started') {
                $message = "deployment $status";
            } else {
                $git_message = exec("git log -1 --pretty=format:'%s (%h)'");
                $message = "deployment $status: $git_message";
            }

            Slack::send('#deployments', "<mintpad-web> $message");
        }

        return Command::SUCCESS;
    }
}
