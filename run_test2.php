<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$activeThreshold = now()->subMinutes(20)->timestamp;
$sessionDriver = (string) config('session.driver', 'file');
$sessionTable = (string) config('session.table', 'sessions');
$sessionTrackingEnabled = $sessionDriver === 'database' && \Illuminate\Support\Facades\Schema::hasTable($sessionTable);

$sessionRows = $sessionTrackingEnabled
    ? \Illuminate\Support\Facades\DB::table($sessionTable . ' as sessions')
        ->join('users as users', 'users.id', '=', 'sessions.user_id')
        ->whereNotNull('sessions.user_id')
        ->select([
            'users.id as user_id',
            'users.name',
            'users.email',
            'users.role',
            'sessions.ip_address',
            'sessions.user_agent',
            'sessions.last_activity',
        ])
        ->orderByDesc('sessions.last_activity')
        ->get()
    : collect();

$connectedUsers = $sessionRows
    ->where('last_activity', '>=', $activeThreshold)
    ->groupBy('user_id')
    ->map(function ($sessions) {
        $latestSession = $sessions->sortByDesc('last_activity')->first();

        return (object) [
            'user_id' => $latestSession->user_id,
            'name' => $latestSession->name,
            'email' => $latestSession->email,
            'role' => $latestSession->role,
            'ip_address' => $latestSession->ip_address,
            'user_agent' => $latestSession->user_agent,
            'last_activity' => (int) $latestSession->last_activity,
            'last_seen_human' => \Illuminate\Support\Carbon::createFromTimestamp((int) $latestSession->last_activity)->diffForHumans(),
            'sessions_count' => $sessions->count(),
        ];
    });

$users = \App\Models\User::query()
    ->latest()
    ->get()
    ->map(function (\App\Models\User $user) use ($connectedUsers, $sessionTrackingEnabled) {
        $session = $connectedUsers->get($user->id);
        $user->setAttribute('is_connected', (bool) $session);
        return $user;
    });

foreach ($users as $user) {
    echo "ID: {$user->id} | Name: {$user->name} | Connected: " . ($user->is_connected ? 'YES' : 'NO') . "\n";
}
