<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Task;

$u = User::first();
if (! $u) {
    echo "No users found. Please seed users first.\n";
    exit(1);
}

$t = Task::create([
    'title' => 'Test task from script',
    'description' => 'Created by automated test script',
    'created_by' => $u->id,
    'assigned_to' => $u->id,
    'status' => 'new',
    'priority' => 'medium',
    'due_date' => date('Y-m-d', strtotime('+7 days')),
    'due_time' => date('H:i:s', strtotime('+7 days')),
    'approval_status' => 'approved',
]);

if ($t) {
    echo "CREATED: {$t->id} - {$t->title}\n";
    exit(0);
}

echo "Failed to create task.\n";
exit(1);
