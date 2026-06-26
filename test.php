<?php
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

dump($connectedUsers);
$users = \App\Models\User::all();
foreach ($users as $user) {
    dump("User ID: " . $user->id . " -> connected? " . ($connectedUsers->get($user->id) ? 'YES' : 'NO'));
}
