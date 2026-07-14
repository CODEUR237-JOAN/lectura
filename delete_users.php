<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::where('email', 'not like', '%@gmail.com')->get();
$count = 0;
foreach ($users as $user) {
    try {
        // Supprimer d'abord les sessions (pas de contrainte cascade automatique)
        \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $user->id)->delete();
        $user->delete();
        $count++;
    } catch (\Exception $e) {
        echo "Erreur avec " . $user->email . ": " . $e->getMessage() . "\n";
    }
}
echo "Supprimé $count utilisateurs.\n";
