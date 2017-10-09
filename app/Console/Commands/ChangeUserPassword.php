<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ChangeUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wask:change-user-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Console Command to change a user\'s password.';

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
        $userId = $this->ask('Enter ID of the user you want to change the password');

        if(!$User = User::find($userId))
        {
            $this->error("User not found");
        }
        else
        {
            $ans = $this->ask("User found with name: {$User->name}. Proceed? (yes|no)");

            if($ans=='yes')
            {
                $newPassword = $this->ask('Enter new password string');

                $User->password = Hash::make($newPassword, ['rounds' => 12]);

                if(!$User->save()) {
                    $this->error("Failed to update user password. Aborted.");
                }
                else
                {
                    $this->info("Password updated successfully.");
                }
            }
            else
            {
                $this->info("Aborted.");
            }
        }
    }
}
