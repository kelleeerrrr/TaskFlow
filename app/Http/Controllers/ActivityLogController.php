<?php

namespace App\Http\Controllers;

use App\Models\TaskHistory;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (! $user->isSuperAdmin()) {
            abort(403);
        }

        $userFilter = $request->query('user');
        $dateFilter = $request->query('date');
        $activityFilter = $request->query('activity');

        $query = TaskHistory::with('user');

        if ($userFilter) {
            $query->where('user_id', $userFilter);
        }

        if ($dateFilter) {
            $query->whereDate('created_at', $dateFilter);
        }

        if ($activityFilter) {
            $query->where('action', 'like', "%{$activityFilter}%");
        }

        $activities = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $users = User::where('role', '!=', 'super_admin')->get();

        return view('activity-logs.index', compact('activities', 'users'));
    }
}
