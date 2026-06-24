<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $statusFilter = $request->query('status');
        $search = $request->query('search');
        $query = User::where('role', 'user')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($statusFilter && in_array($statusFilter, ['active', 'inactive'])) {
            $query->where('status', $statusFilter);
        }

        $users = $query->paginate(15);

        return view('users.index', compact('users', 'statusFilter'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:manager,user',
            'status' => 'required|in:active,inactive',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'status' => $data['status'],
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $totalTasks = \App\Models\Task::where('assigned_to', $user->id)->count();
        $completedTasks = \App\Models\Task::where('assigned_to', $user->id)->where('status', 'completed')->count();
        $pendingTasks = \App\Models\Task::where('assigned_to', $user->id)->where('status', '!=', 'completed')->count();

        $recentTasks = \App\Models\Task::where('assigned_to', $user->id)
            ->with(['creator', 'assignedUser'])
            ->latest()
            ->take(10)
            ->get();

        return view('users.show', compact('user', 'totalTasks', 'completedTasks', 'pendingTasks', 'recentTasks'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:manager,user',
            'status' => 'required|in:active,inactive',
        ]);

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function activate(User $user)
    {
        $this->authorize('activate', $user);

        $user->update(['status' => 'active']);

        return redirect()->route('users.index')->with('success', 'User activated successfully.');
    }

    public function deactivate(User $user)
    {
        $this->authorize('deactivate', $user);

        $user->update(['status' => 'inactive']);

        return redirect()->route('users.index')->with('success', 'User deactivated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
