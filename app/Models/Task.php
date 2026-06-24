<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'assigned_to',
        'status',
        'priority',
        'due_date',
        'due_time',
        'approval_status',
        'time_revision_request',
        'time_revision_status',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'task_collaborators')
            ->withPivot('invitation_status')
            ->withTimestamps();
    }

    public function history()
    {
        return $this->hasMany(TaskHistory::class);
    }

    public function files()
    {
        return $this->hasMany(TaskFile::class);
    }

    public function timeRevisionRequests()
    {
        return $this->hasMany(TimeRevisionRequest::class);
    }
}
