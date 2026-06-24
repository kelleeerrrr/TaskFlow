<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        $taskSummary = [
            'total' => Task::count(),
            'new' => Task::where('status', 'new')->count(),
            'ongoing' => Task::where('status', 'ongoing')->count(),
            'completed' => Task::where('status', 'completed')->count(),
            'late' => Task::where('status', 'late')->count(),
        ];

        $userPerformance = User::where('role', 'user')
            ->withCount(['tasks as total_tasks', 'tasks as completed_tasks' => function($q) {
                $q->where('status', 'completed');
            }, 'tasks as ongoing_tasks' => function($q) {
                $q->where('status', 'ongoing');
            }, 'tasks as late_tasks' => function($q) {
                $q->where('status', 'late');
            }])
            ->get()
            ->map(function($user) {
                $user->completion_rate = $user->total_tasks > 0
                    ? round(($user->completed_tasks / $user->total_tasks) * 100, 1)
                    : 0;
                return $user;
            });

        $teamProductivity = [
            'users_meeting_deadlines' => User::where('role', 'user')->whereHas('tasks', function($q) {
                $q->where('status', '!=', 'late');
            })->count(),
            'overdue_tasks' => Task::where('status', 'late')->count(),
            'team_completion_percentage' => $taskSummary['total'] > 0
                ? round(($taskSummary['completed'] / $taskSummary['total']) * 100, 1)
                : 0,
        ];

        return view('reports.index', compact('taskSummary', 'userPerformance', 'teamProductivity'));
    }

    public function detectLate()
    {
        Task::where('status', '!=', 'completed')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'late']);

        return redirect()->route('reports.index')->with('success', 'Late tasks have been detected and updated.');
    }

    public function productivity()
    {
        $tasksPerMonth = Task::selectRaw('strftime("%Y-%m", created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $taskCompletionRate = [
            'completed' => Task::where('status', 'completed')->count(),
            'total' => Task::count(),
        ];

        $tasksByStatus = [
            'new' => Task::where('status', 'new')->count(),
            'ongoing' => Task::where('status', 'ongoing')->count(),
            'completed' => Task::where('status', 'completed')->count(),
        ];

        $tasksByApproval = [
            'pending' => Task::where('approval_status', 'pending')->count(),
            'approved' => Task::where('approval_status', 'approved')->count(),
            'rejected' => Task::where('approval_status', 'rejected')->count(),
        ];

        $overdueTasks = Task::where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        return view('reports.productivity', compact(
            'tasksPerMonth',
            'taskCompletionRate',
            'tasksByStatus',
            'tasksByApproval',
            'overdueTasks'
        ));
    }

    public function perUserReport(Request $request)
    {
        $userId = $request->query('user_id');
        $users = User::where('role', 'user')->get();

        if ($userId) {
            $user = User::findOrFail($userId);

            if ($user->role !== 'user') {
                $userStats = [
                    'total_tasks' => 0,
                    'completed_tasks' => 0,
                    'ongoing_tasks' => 0,
                    'overdue_tasks' => 0,
                ];
            } else {
                $userStats = [
                    'total_tasks' => Task::where('created_by', $user->id)->count(),
                    'completed_tasks' => Task::where('created_by', $user->id)->where('status', 'completed')->count(),
                    'ongoing_tasks' => Task::where('created_by', $user->id)->where('status', 'ongoing')->count(),
                    'overdue_tasks' => Task::where('created_by', $user->id)
                        ->where('due_date', '<', now())
                        ->where('status', '!=', 'completed')
                        ->count(),
                ];
            }
        } else {
            $user = null;
            $userStats = null;
        }

        return view('reports.per-user', compact('users', 'user', 'userStats'));
    }

    public function userRegistrations()
    {
        $usersPerMonth = User::selectRaw('strftime("%Y-%m", created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $usersByRole = [
            'super_admin' => User::where('role', 'super_admin')->count(),
            'manager' => User::where('role', 'manager')->count(),
            'user' => User::where('role', 'user')->count(),
        ];

        $usersByStatus = [
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
        ];

        return view('reports.user-registrations', compact(
            'usersPerMonth',
            'usersByRole',
            'usersByStatus'
        ));
    }
}
