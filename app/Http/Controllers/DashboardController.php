<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

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

        $tasksPerMonth = Task::selectRaw('strftime("%Y-%m", created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $userRegistrations = User::selectRaw('strftime("%Y-%m", created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard.super-admin', compact('user', 'stats', 'tasksPerMonth', 'userRegistrations'));
    }

    private function managerDashboard($user)
    {
        $stats = [
            'total_tasks' => Task::count(),
            'new_tasks' => Task::where('status', 'new')->count(),
            'ongoing_tasks' => Task::where('status', 'ongoing')->count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'total_users' => User::count(),
        ];

        $taskCompletionRate = [
            'completed' => Task::where('status', 'completed')->count(),
            'total' => Task::count(),
        ];

        $tasksByStatus = [
            'new' => Task::where('status', 'new')->count(),
            'ongoing' => Task::where('status', 'ongoing')->count(),
            'completed' => Task::where('status', 'completed')->count(),
        ];

        return view('dashboard.manager', compact('user', 'stats', 'taskCompletionRate', 'tasksByStatus'));
    }

    private function userDashboard($user)
    {
        $myTasks = Task::where('created_by', $user->id)->count();
        $newTasks = Task::where('created_by', $user->id)->where('status', 'new')->count();
        $ongoingTasks = Task::where('created_by', $user->id)->where('status', 'ongoing')->count();
        $completedTasks = Task::where('created_by', $user->id)->where('status', 'completed')->count();

        $recentTasks = Task::with('assignedUser')
            ->where('created_by', $user->id)
            ->orWhere('assigned_to', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'my_tasks' => $myTasks,
            'new_tasks' => $newTasks,
            'ongoing_tasks' => $ongoingTasks,
            'completed_tasks' => $completedTasks,
        ];

        return view('dashboard.user', compact('user', 'stats', 'recentTasks'));
    }
}
