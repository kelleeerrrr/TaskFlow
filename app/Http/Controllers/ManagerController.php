<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (! $user->isSuperAdmin()) {
            abort(403);
        }

        $managers = User::where('role', 'manager')
            ->withCount(['tasks' => function($query) {
                $query->where('status', 'completed');
            }])
            ->get()
            ->map(function($manager) {
                $totalTasks = Task::where('assigned_to', $manager->id)->count();
                $completedTasks = Task::where('assigned_to', $manager->id)->where('status', 'completed')->count();
                $pendingTasks = Task::where('assigned_to', $manager->id)->where('status', 'pending')->count();
                $overdueTasks = Task::where('assigned_to', $manager->id)
                    ->where('due_date', '<', now())
                    ->where('status', '!=', 'completed')
                    ->count();
                
                return [
                    'id' => $manager->id,
                    'name' => $manager->name,
                    'email' => $manager->email,
                    'team_size' => User::where('role', 'user')->count(),
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'pending_tasks' => $pendingTasks,
                    'overdue_tasks' => $overdueTasks,
                    'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
                    'status' => $manager->status,
                    'created_at' => $manager->created_at,
                ];
            });

        return view('managers.index', compact('managers'));
    }

    public function show(string $id)
    {
        $user = auth()->user();

        if (! $user->isSuperAdmin()) {
            abort(403);
        }

        $manager = User::where('role', 'manager')->findOrFail($id);

        $teamMembers = User::where('role', 'user')->get()->map(function($user) {
            $totalTasks = Task::where('assigned_to', $user->id)->count();
            $completedTasks = Task::where('assigned_to', $user->id)->where('status', 'completed')->count();
            $pendingTasks = Task::where('assigned_to', $user->id)->where('status', 'pending')->count();
            $overdueTasks = Task::where('assigned_to', $user->id)
                ->where('due_date', '<', now())
                ->where('status', '!=', 'completed')
                ->count();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'overdue_tasks' => $overdueTasks,
                'status' => $user->status,
            ];
        });

        $managerStats = [
            'total_users' => User::where('role', 'user')->count(),
            'completed_tasks' => Task::where('assigned_to', $manager->id)->where('status', 'completed')->count(),
            'pending_tasks' => Task::where('assigned_to', $manager->id)->where('status', 'pending')->count(),
            'overdue_tasks' => Task::where('assigned_to', $manager->id)
                ->where('due_date', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
        ];

        return view('managers.show', compact('manager', 'teamMembers', 'managerStats'));
    }
}
