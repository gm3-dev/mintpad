<?php

namespace App\Console\Commands;

use App\Facades\Moneybird;
use App\Models\User;
use Illuminate\Console\Command;

class MoneybirdCreateContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moneybird:contacts:create-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create all Moneybird contacts that are missing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::where('moneybird_id', null)->get();
        foreach ($users as $user) {
            $this->createContact($user);
        }

        $this->info('Finished task!');
    }

    protected function createContact(User $user)
    {
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
