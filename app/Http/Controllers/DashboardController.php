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
        // Summary Cards
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_managers' => User::where('role', 'manager')->count(),
            'active_users' => User::where('status', 'active')->where('role', 'user')->count(),
            'inactive_users' => User::where('status', 'inactive')->where('role', 'user')->count(),
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'overdue_tasks' => Task::where('due_date', '<', now())->where('status', '!=', 'completed')->count(),
        ];

        // User Activity Analytics
        $userGrowthTrend = User::selectRaw("DATE(created_at) as date, COUNT(*) as count")
            ->where('role', 'user')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $activeUsersTrend = User::selectRaw("DATE(created_at) as date, COUNT(*) as count")
            ->where('status', 'active')
            ->where('role', 'user')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Task Analytics
        $taskDistribution = [
            'completed' => Task::where('status', 'completed')->count(),
            'ongoing' => Task::where('status', 'ongoing')->count(),
            'pending' => Task::where('status', 'pending')->orWhere('approval_status', 'pending')->count(),
            'overdue' => Task::where('due_date', '<', now())->where('status', '!=', 'completed')->count(),
        ];

        // Manager Overview
        $managers = User::where('role', 'manager')->withCount(['tasks' => function($query) {
            $query->where('status', 'completed');
        }])->get()->map(function($manager) {
            $totalTasks = Task::where('assigned_to', $manager->id)->count();
            $completedTasks = Task::where('assigned_to', $manager->id)->where('status', 'completed')->count();
            $teamSize = User::where('role', 'user')->count(); // Simplified team size
            
            return [
                'name' => $manager->name,
                'email' => $manager->email,
                'team_size' => $teamSize,
                'total_tasks' => $totalTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
                'status' => $manager->status,
            ];
        });

        // Recent Activity
        $recentActivities = \App\Models\TaskHistory::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($activity) {
                return [
                    'user' => $activity->user ? $activity->user->name : 'System',
                    'action' => $activity->action,
                    'date' => $activity->created_at->format('M d, Y'),
                    'time' => $activity->created_at->format('g:i A'),
                ];
            });

        return view('dashboard.super-admin', compact(
            'user',
            'stats',
            'userGrowthTrend',
            'activeUsersTrend',
            'taskDistribution',
            'managers',
            'recentActivities'
        ));
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
