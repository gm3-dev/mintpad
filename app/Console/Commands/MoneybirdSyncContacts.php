<?php

namespace App\Console\Commands;

use App\Facades\Moneybird;
use App\Models\User;
use Illuminate\Console\Command;

class MoneybirdSyncContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moneybird:contacts:sync-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all Moneybird contacts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::where('moneybird_id', '!=', null)->get();
        foreach ($users as $user) {
            $this->updateContact($user);
        }

        $this->info('Finished task!');
    }

    protected function updateContact(User $user)
    {
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
