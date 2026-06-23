<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Task;

$tasks = Task::all();
echo "Count=" . $tasks->count() . "\n";
foreach ($tasks as $task) {
    echo "ID={$task->id} title={$task->title} created_by={$task->created_by} assigned_to={$task->assigned_to} status={$task->status} approval={$task->approval_status}\n";
}
