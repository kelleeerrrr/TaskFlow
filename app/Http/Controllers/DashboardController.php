<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskCollaborator;
use App\Models\TimeRevisionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard($user);
        } elseif ($user->isManager()) {
            return $this->managerDashboard($user);
        } else {
            return $this->userDashboard($user);
        }
    }

    private function superAdminDashboard($user)
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'total_tasks' => Task::count(),
        ];

        $tasksPerMonth = Task::selectRaw("to_char(created_at, 'YYYY-MM') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $userRegistrations = User::selectRaw("to_char(created_at, 'YYYY-MM') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard.super-admin', compact('user', 'stats', 'tasksPerMonth', 'userRegistrations'));
    }

    private function managerDashboard($user)
    {
        $stats = [
            'total_tasks' => Task::count(),
            'pending_tasks' => Task::where('approval_status', 'pending')->count(),
            'rejected_tasks' => Task::where('status', 'rejected')->count(),
            'new_tasks' => Task::where('status', 'new')->where('approval_status', 'approved')->count(),
            'ongoing_tasks' => Task::where('status', 'ongoing')->where('approval_status', 'approved')->count(),
            'completed_tasks' => Task::where('status', 'completed')->where('approval_status', 'approved')->count(),
            'total_users' => User::count(),
        ];

        $taskCompletionRate = [
            'completed' => Task::where('status', 'completed')->where('approval_status', 'approved')->count(),
            'total' => Task::count(),
        ];

        $tasksByStatus = [
            'new' => Task::where('status', 'new')->where('approval_status', 'approved')->count(),
            'pending' => Task::where('approval_status', 'pending')->count(),
            'ongoing' => Task::where('status', 'ongoing')->where('approval_status', 'approved')->count(),
            'completed' => Task::where('status', 'completed')->where('approval_status', 'approved')->count(),
            'rejected' => Task::where('status', 'rejected')->count(),
        ];

        $actionCenter = [
            'collaboration_requests_pending' => TaskCollaborator::where('invitation_status', 'pending')->count(),
            'time_revision_requests_pending' => TimeRevisionRequest::where('status', 'pending')->count(),
            'tasks_due_today' => Task::whereDate('due_date', Carbon::today())
                ->where('approval_status', 'approved')
                ->where('status', '!=', 'completed')
                ->count(),
            'overdue_tasks' => Task::where('due_date', '<', Carbon::today())
                ->where('approval_status', 'approved')
                ->where('status', '!=', 'completed')
                ->count(),
        ];

        return view('dashboard.manager', compact('user', 'stats', 'taskCompletionRate', 'tasksByStatus', 'actionCenter'));
    }

    private function userDashboard($user)
    {
        $taskQuery = Task::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
                ->orWhere('assigned_to', $user->id)
                ->orWhereHas('collaborators', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->where('invitation_status', 'accepted');
                });
        });

        $myTasks = (clone $taskQuery)->count();
        $pendingApprovalTasks = Task::where('created_by', $user->id)->where('approval_status', 'pending')->count();
        $newTasks = (clone $taskQuery)->where('status', 'new')->where('approval_status', 'approved')->count();
        $ongoingTasks = (clone $taskQuery)->where('status', 'ongoing')->where('approval_status', 'approved')->count();
        $rejectedTasks = (clone $taskQuery)->where('status', 'rejected')->count();
        $completedTasks = (clone $taskQuery)->where('status', 'completed')->where('approval_status', 'approved')->count();

        $recentTasks = (clone $taskQuery)
            ->with('assignedUser')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'my_tasks' => $myTasks,
            'pending_approval' => $pendingApprovalTasks,
            'new_tasks' => $newTasks,
            'ongoing_tasks' => $ongoingTasks,
            'completed_tasks' => $completedTasks,
            'rejected_tasks' => $rejectedTasks,
        ];

        return view('dashboard.user', compact('user', 'stats', 'recentTasks'));
    }
}
