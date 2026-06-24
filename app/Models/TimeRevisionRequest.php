<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeRevisionRequest extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'requested_due_date',
        'requested_due_time',
        'reason',
        'status',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
