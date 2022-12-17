<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SuperAdminChangePassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'superadmin:changepassword {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Change Super Admin's password.";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $password = $this->option('password');
        if($password) {
            User::where('user_type', User::SUPER_ADMIN)
                ->update([
                    'password' => Hash::make($password)
                ]);
            return $this->info("Super Admin's Password changed.");
        }
        return $this->error('No password inputted. Operation failed.');
    }
}
