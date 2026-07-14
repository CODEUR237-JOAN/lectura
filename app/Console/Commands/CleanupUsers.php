<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprimer les utilisateurs dont l\'email ne respecte pas le formalisme @gmail.com';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::where('email', 'not like', '%@gmail.com')->get();
        $count = 0;
        
        foreach ($users as $user) {
            try {
                // Delete sessions first to avoid foreign key issues on sessions table
                DB::table('sessions')->where('user_id', $user->id)->delete();
                $user->delete();
                $count++;
            } catch (\Exception $e) {
                $this->error("Erreur pour {$user->email} : " . $e->getMessage());
            }
        }
        
        $this->info("Nettoyage termine : {$count} utilisateurs supprimes.");
    }
}
