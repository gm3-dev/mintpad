<?php

namespace App\Console\Commands;

use App\Facades\Moneybird;
use App\Models\User;
use Illuminate\Console\Command;

class MoneybirdSyncContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moneybird:contacts:sync {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Moneybird contact by user ID';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userID = $this->argument('user');
        $user = User::find($userID);

        if ($user->moneybird_id !== null) {
            $moneybird_id = Moneybird::updateContact($user);
            if ($moneybird_id !== false) {
                $this->info('Contact '.$user->id.' updated');
            } else {
                $this->info('Contact '.$user->id.' update error');
            }
        } else {
            $this->info('Contact '.$user->id.' does not exists');
        }
    }
}
