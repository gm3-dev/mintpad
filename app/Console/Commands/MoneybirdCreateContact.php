<?php

namespace App\Console\Commands;

use App\Facades\Moneybird;
use App\Models\User;
use Illuminate\Console\Command;

class MoneybirdCreateContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moneybird:contacts:create {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Moneybird contact by user ID if it\'s missing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userID = $this->argument('user');
        $user = User::find($userID);

        if ($user->moneybird_id === null) {
            $moneybird_id = Moneybird::createContact($user);
            if ($moneybird_id !== false) {
                $user->moneybird_id = $moneybird_id;
                $user->save();

                $this->info('Contact '.$user->id.' created');
            } else {
                $this->info('Contact '.$user->id.' creation error');
            }
        } else {
            $this->info('Contact '.$user->id.' already exists');
        }
    }
}
