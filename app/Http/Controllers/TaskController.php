<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\TaskFile;
use App\Models\TaskHistory;
use App\Models\TaskNotification;
use App\Models\TimeRevisionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks with filtering and search capabilities.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->query('search');
            $statusFilter = $request->query('status');
            $assignedFilter = $request->query('assigned');
            $priorityFilter = $request->query('priority');
            $assignedToFilter = $request->query('assigned_to');
            $dueDateFilter = $request->query('due_date');

            // Start query with eager loading of relationships
            $query = Task::with(['creator', 'assignedUser', 'collaborators']);

            // Apply search filter on title and description
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply role-based visibility rules
            // Super admins and managers can see all tasks
            // Regular users can only see their own tasks or tasks they're collaborating on
            if ($user->isSuperAdmin() || $user->isManager()) {
                // Can see all tasks - no additional filtering needed
            } else {
                // Users can only see their own tasks or shared tasks
                $query->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id)
                      ->orWhereHas('collaborators', function ($query) use ($user) {
                          $query->where('user_id', $user->id)
                                ->where('invitation_status', 'accepted');
                      });
                });
            }

            // Filter by assigned to me (either directly assigned or as collaborator)
            if ($assignedFilter === 'me') {
                $query->where(function ($q) use ($user) {
                    $q->where('assigned_to', $user->id)
                      ->orWhereHas('collaborators', function ($query) use ($user) {
                          $query->where('user_id', $user->id)
                                ->where('invitation_status', 'accepted');
                      });
                });
            }

            // Filter by status (new, ongoing, completed, rejected, or pending approval)
            if ($statusFilter) {
                if (in_array($statusFilter, ['new', 'ongoing', 'completed', 'rejected'])) {
                    $query->where('status', $statusFilter);
                } elseif ($statusFilter === 'pending') {
                    $query->where('approval_status', 'pending');
                }
            }

            // Filter by priority
            if ($priorityFilter) {
                $query->where('priority', $priorityFilter);
            }

            // Filter by assigned user
            if ($assignedToFilter) {
                $query->where('assigned_to', $assignedToFilter);
            }

            // Filter by due date
            if ($dueDateFilter) {
                $query->whereDate('due_date', $dueDateFilter);
            }

            // Paginate results and append query parameters for pagination links
            $tasks = $query->latest()->paginate(15)->appends($request->query());

            return view('tasks.index', compact('tasks', 'statusFilter', 'assignedFilter'));
        } catch (\Exception $e) {
            // Log error and return with error message
            \Log::error('Error fetching tasks: ' . $e->getMessage());
            return back()->with('error', 'Failed to load tasks. Please try again.');
        }
    }

    /**
     * Display collaboration requests for managers and super admins.
     *
     * @return \Illuminate\View\View
     */
    public function collaborationRequests()
    {
        try {
            $user = auth()->user();

            // Only managers and super admins can view collaboration requests
            if (! ($user->isSuperAdmin() || $user->isManager())) {
                abort(Response::HTTP_FORBIDDEN);
            }

            // Fetch pending collaboration requests with related task and user data
            $requests = TaskCollaborator::with(['task.creator', 'user'])
                ->where('invitation_status', 'pending')
                ->latest()
                ->get();

            return view('collaborations.index', compact('requests'));
        } catch (\Exception $e) {
            \Log::error('Error fetching collaboration requests: ' . $e->getMessage());
            return back()->with('error', 'Failed to load collaboration requests. Please try again.');
        }
    }

    /**
     * Show the form for creating a new task.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created task in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Define validation rules
            $rules = [
                'title' => 'required|max:255',
                'description' => 'required',
                'due_date' => 'required|date',
                'due_time' => 'required',
                'priority' => 'nullable|in:low,medium,high',
                'attachment' => 'nullable|mimes:pdf|max:51200',
            ];

            // Additional rules for managers and super admins (can assign users and add collaborators)
            if ($request->user()->isManager() || $request->user()->isSuperAdmin()) {
                $rules['assigned_to'] = 'nullable|exists:users,id';
                $rules['collaborators'] = 'nullable|array';
                $rules['collaborators.*'] = 'exists:users,id';
            }

            $data = $request->validate($rules);
            $user = $request->user();

            $assignedUserId = $data['assigned_to'] ?? null;
            $collaboratorIds = $data['collaborators'] ?? [];

            // Auto-assign task to user if they are a regular user
            if ($user->isUser()) {
                $assignedUserId = $user->id;
            }

            // Create the task
            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'created_by' => $user->id,
                'assigned_to' => $assignedUserId,
                'status' => 'new',
                'priority' => $data['priority'] ?? 'medium',
                'due_date' => $data['due_date'],
                'due_time' => $data['due_time'],
                // Managers and super admins' tasks are auto-approved, regular users' tasks need approval
                'approval_status' => $user->isManager() || $user->isSuperAdmin() ? 'approved' : 'pending',
            ]);

            // Add assigned user as collaborator if specified
            if ($assignedUserId) {
                $task->collaborators()->attach($assignedUserId, ['invitation_status' => 'accepted']);

                TaskHistory::create([
                    'task_id' => $task->id,
                    'action' => 'Task Assigned',
                    'user_id' => $user->id,
                ]);

                $assignedUser = \App\Models\User::find($assignedUserId);
                if ($assignedUser) {
                    $this->createNotification($assignedUser, 'Pending Task', "Waiting for Manager approval. '{$task->title}'.", route('tasks.show', $task->id));
                }
            }

            // Add additional collaborators
            if (! empty($collaboratorIds)) {
                foreach ($collaboratorIds as $userId) {
                    if ($userId != $assignedUserId) {
                        $task->collaborators()->attach($userId, ['invitation_status' => 'pending']);

                        TaskHistory::create([
                            'task_id' => $task->id,
                            'action' => 'Collaborator Invited',
                            'user_id' => $user->id,
                        ]);

                        $collaboratorUser = \App\Models\User::find($userId);
                        if ($collaboratorUser) {
                            $this->createNotification($collaboratorUser, 'Collaboration Invitation', "You have been invited to collaborate on task '{$task->title}'.", route('tasks.show', $task->id));
                        }

                        // Notify admins for collaboration approval
                        $admins = \App\Models\User::whereIn('role', ['super_admin', 'manager'])->get();
                        foreach ($admins as $admin) {
                            $this->createNotification($admin, 'Collaboration Approval Needed', "A collaboration request for task '{$task->title}' is pending approval.", route('requests.index'));
                        }
                    }
                }
            }

            // Handle file attachment if provided
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('task_files', 'public');

                TaskFile::create([
                    'task_id' => $task->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                ]);
            }

            // Log task creation in history
            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Task Created',
                'user_id' => $user->id,
            ]);

            return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating task: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create task. Please try again.');
        }
    }

    /**
     * Display the specified task.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        try {
            $task = Task::with(['creator', 'files', 'collaborators', 'history.user', 'assignedUser'])->findOrFail($id);
            $user = auth()->user();

            // Check if user has permission to view this task
            if (! $this->canAccessTask($task, $user)) {
                abort(Response::HTTP_FORBIDDEN);
            }

            return view('tasks.show', compact('task'));
        } catch (\Exception $e) {
            \Log::error('Error displaying task: ' . $e->getMessage());
            return back()->with('error', 'Failed to load task details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified task.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $user = auth()->user();

            // Check if user has permission to edit this task
            if (! $this->canEditTask($task, $user)) {
                abort(Response::HTTP_FORBIDDEN);
            }

            return view('tasks.edit', compact('task'));
        } catch (\Exception $e) {
            \Log::error('Error loading edit form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load edit form. Please try again.');
        }
    }

    /**
     * Update the specified task in storage.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $user = auth()->user();

            // Allow status updates for authorized users (assigned users can update status)
            $isStatusOnlyUpdate = $request->has('status') && ! $request->has('title');

            if ($isStatusOnlyUpdate) {
                if (! $this->canUpdateStatus($task, $user)) {
                    abort(Response::HTTP_FORBIDDEN);
                }
            } else {
                // Full task edit requires edit permission
                if (! $this->canEditTask($task, $user)) {
                    abort(Response::HTTP_FORBIDDEN);
                }
            }

            // Define validation rules
            $rules = [
                'title' => $isStatusOnlyUpdate ? 'nullable' : 'required|max:255',
                'description' => $isStatusOnlyUpdate ? 'nullable' : 'required',
                'due_date' => $isStatusOnlyUpdate ? 'nullable' : 'required|date',
                'due_time' => $isStatusOnlyUpdate ? 'nullable' : 'required',
                'priority' => 'nullable|in:low,medium,high',
                'status' => 'nullable|in:new,ongoing,completed,late',
                'attachment' => 'nullable|mimes:pdf|max:51200',
            ];

            // Additional rules for managers and super admins (can assign users and add collaborators)
            if ($request->user()->isManager() || $request->user()->isSuperAdmin()) {
                $rules['assigned_to'] = 'nullable|array';
                $rules['assigned_to.*'] = 'exists:users,id';
                $rules['collaborate_with'] = 'nullable|array';
                $rules['collaborate_with.*'] = 'exists:users,id';
            }

            $data = $request->validate($rules);

            $assignedUsers = $request->user()->isManager() || $request->user()->isSuperAdmin()
                ? collect($data['assigned_to'] ?? [])->filter()->values()->all()
                : [];

            $collaborateUsers = $request->user()->isManager() || $request->user()->isSuperAdmin()
                ? collect($data['collaborate_with'] ?? [])->filter()->values()->all()
                : [];

            $oldAssignedTo = $task->assigned_to;

            // PDF constraint for completing tasks - requires attachment
            if (isset($data['status']) && $data['status'] === 'completed') {
                if (! $request->hasFile('attachment')) {
                    return redirect()->back()->with('error', 'You must upload a PDF file to mark the task as completed.');
                }
            }

            // Regular users cannot change status unless task is approved
            if (! ($user->isSuperAdmin() || $user->isManager()) && $task->approval_status !== 'approved') {
                unset($data['status']);
            }

            // Update task with validated data
            $task->update([
                'title' => $data['title'] ?? $task->title,
                'description' => $data['description'] ?? $task->description,
                'due_date' => $data['due_date'] ?? $task->due_date,
                'due_time' => $data['due_time'] ?? $task->due_time,
                'assigned_to' => $request->user()->isManager() || $request->user()->isSuperAdmin()
                    ? (count($assignedUsers) ? $assignedUsers[0] : null)
                    : $task->assigned_to,
                'priority' => $data['priority'] ?? $task->priority,
                'status' => $data['status'] ?? $task->status,
            ]);

            // Create task history for status change
            if (isset($data['status']) && $data['status'] !== $task->getOriginal('status')) {
                TaskHistory::create([
                    'task_id' => $task->id,
                    'action' => 'Status Changed to ' . ucfirst($data['status']),
                    'user_id' => $user->id,
                ]);
            }

            // Handle assigned users (managers/super admins only)
            if (! empty($assignedUsers)) {
                $syncData = [];
                foreach ($assignedUsers as $userId) {
                    $syncData[$userId] = ['invitation_status' => 'accepted'];
                }

                $task->collaborators()->sync($syncData);

                if ($task->assigned_to && $task->assigned_to !== $oldAssignedTo) {
                    TaskHistory::create([
                        'task_id' => $task->id,
                        'action' => 'Task Reassigned',
                        'user_id' => $request->user()->id,
                    ]);
                }

                // Send notifications for assigned users
                foreach ($assignedUsers as $userId) {
                    $assignedUser = \App\Models\User::find($userId);

                    if ($assignedUser) {
                        $this->createNotification($assignedUser, 'Task Assigned', "You have been assigned to task '{$task->title}'.", route('tasks.show', $task->id));
                    }
                }
            } elseif ($request->user()->isManager() || $request->user()->isSuperAdmin()) {
                $task->collaborators()->detach();
            }

            // Handle additional collaborators (managers/super admins only)
            if (! empty($collaborateUsers)) {
                foreach ($collaborateUsers as $userId) {
                    if (! in_array($userId, $assignedUsers) && ! $task->collaborators()->where('user_id', $userId)->exists()) {
                        $task->collaborators()->attach($userId, ['invitation_status' => 'pending']);

                        TaskHistory::create([
                            'task_id' => $task->id,
                            'action' => 'Collaborator Invited',
                            'user_id' => $request->user()->id,
                        ]);

                        $collaboratorUser = \App\Models\User::find($userId);
                        if ($collaboratorUser) {
                            $this->createNotification($collaboratorUser, 'Collaboration Invitation', "You have been invited to collaborate on task '{$task->title}'.", route('tasks.show', $task->id));
                        }

                        // Notify admins for collaboration approval
                        $admins = \App\Models\User::whereIn('role', ['super_admin', 'manager'])->get();
                        foreach ($admins as $admin) {
                            $this->createNotification($admin, 'Collaboration Approval Needed', "A collaboration request for task '{$task->title}' is pending approval.", route('requests.index'));
                        }
                    }
                }
            }

            // Handle file attachment if provided
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('task_files', 'public');

                TaskFile::create([
                    'task_id' => $task->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                ]);
            }

            // Log status update in history
            if (($data['status'] ?? $task->status) !== $task->getOriginal('status')) {
                TaskHistory::create([
                    'task_id' => $task->id,
                    'action' => 'Task Status Updated',
                    'user_id' => $user->id,
                ]);
            }

            // Log task update in history
            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Task Updated',
                'user_id' => $user->id,
            ]);

            return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating task: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update task. Please try again.');
        }
    }

    /**
     * Remove the specified task from storage.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $user = auth()->user();

            // Check if user has permission to delete this task
            if (! $this->canAccessTask($task, $user)) {
                abort(Response::HTTP_FORBIDDEN);
            }

            $task->delete();

            return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting task: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete task. Please try again.');
        }
    }

    /**
     * Approve a pending task (managers and super admins only).
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $user = auth()->user();

            // Only managers and super admins can approve tasks
            if (! ($user->isSuperAdmin() || $user->isManager())) {
                abort(Response::HTTP_FORBIDDEN);
            }

            // Update task approval status and reset status to 'new'
            $task->update(['approval_status' => 'approved', 'status' => 'new']);

            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Task Approved',
                'user_id' => $user->id,
            ]);

            // Notify task creator about approval
            $this->createNotification($task->creator, 'Task Approved', "Your task '{$task->title}' has been approved.", route('tasks.show', $task->id));

            return redirect()->route('tasks.show', $task)->with('success', 'Task approved successfully.');
        } catch (\Exception $e) {
            \Log::error('Error approving task: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve task. Please try again.');
        }
    }

    /**
     * Reject a pending task (managers and super admins only).
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $user = auth()->user();

            // Only managers and super admins can reject tasks
            if (! ($user->isSuperAdmin() || $user->isManager())) {
                abort(Response::HTTP_FORBIDDEN);
            }

            // Update task approval status and status to 'rejected'
            $task->update(['approval_status' => 'rejected', 'status' => 'rejected']);

            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Task Rejected',
                'user_id' => $user->id,
            ]);

            // Notify task creator about rejection
            $this->createNotification($task->creator, 'Task Rejected', "Your task '{$task->title}' has been rejected.");

            return redirect()->route('tasks.show', $task)->with('success', 'Task rejected successfully.');
        } catch (\Exception $e) {
            \Log::error('Error rejecting task: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject task. Please try again.');
        }
    }

    /**
     * Invite a user to collaborate on a task.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inviteCollaborator(Request $request, string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $user = auth()->user();

            // Check if user has permission to invite collaborators
            if (! ($user->isSuperAdmin() || $user->isManager() || $task->created_by === $user->id || $task->assigned_to === $user->id)) {
                abort(Response::HTTP_FORBIDDEN);
            }

            // Cannot invite collaborators to rejected tasks
            if ($task->status === 'rejected' || $task->approval_status === 'rejected') {
                abort(Response::HTTP_FORBIDDEN);
            }

            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $collaborator = \App\Models\User::findOrFail($data['user_id']);

            // Only regular users can be invited as collaborators
            if ($collaborator->role !== 'user') {
                return redirect()->route('tasks.show', $task)->with('error', 'Only regular users can be invited as collaborators.');
            }

            // Check if user is already a collaborator
            if ($task->collaborators()->where('user_id', $collaborator->id)->exists()) {
                return redirect()->route('tasks.show', $task)->with('error', 'User is already a collaborator.');
            }

            // Attach collaborator with pending status
            $task->collaborators()->attach($collaborator->id, ['invitation_status' => 'pending']);

            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Collaborator Invited',
                'user_id' => $user->id,
            ]);

            // Notify the invited user
            $this->createNotification($collaborator, 'Collaboration Invitation', "You have been invited to collaborate on task '{$task->title}'. This invitation is pending admin approval.", route('tasks.show', $task->id));

            // Notify admins for collaboration approval
            $admins = \App\Models\User::whereIn('role', ['super_admin', 'manager'])->get();
            foreach ($admins as $admin) {
                $this->createNotification($admin, 'Collaboration Approval Needed', "A collaboration request for task '{$task->title}' is pending approval.", route('requests.index'));
            }

            return redirect()->route('tasks.show', $task)->with('success', 'Collaborator invited successfully and pending admin approval.');
        } catch (\Exception $e) {
            \Log::error('Error inviting collaborator: ' . $e->getMessage());
            return back()->with('error', 'Failed to invite collaborator. Please try again.');
        }
    }

    /**
     * Accept a collaboration invitation (managers and super admins only).
     *
     * @param Request $request
     * @param string $taskId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptInvitation(Request $request, string $taskId)
    {
        try {
            $user = auth()->user();
            $task = Task::findOrFail($taskId);

            // Only managers and super admins can accept collaboration invitations
            if (! ($user->isSuperAdmin() || $user->isManager())) {
                abort(Response::HTTP_FORBIDDEN);
            }

            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $collaboration = $task->collaborators()->where('user_id', $data['user_id'])->first();

            // Check if collaboration exists and is pending
            if (! $collaboration || $collaboration->pivot->invitation_status !== 'pending') {
                abort(Response::HTTP_FORBIDDEN);
            }

            // Update collaboration status to accepted
            $collaboration->pivot->invitation_status = 'accepted';
            $collaboration->pivot->save();

            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Collaborator Approved',
                'user_id' => $user->id,
            ]);

            // Notify the collaborator about approval
            $collaboratorUser = \App\Models\User::find($data['user_id']);

            if ($collaboratorUser) {
                $this->createNotification($collaboratorUser, 'Collaboration Approved', "Your request to collaborate on '{$task->title}' has been approved.", route('tasks.show', $task->id));
            }

            return redirect()->route('tasks.show', $task)->with('success', 'Collaboration approved successfully.');
        } catch (\Exception $e) {
            \Log::error('Error accepting collaboration invitation: ' . $e->getMessage());
            return back()->with('error', 'Failed to accept collaboration invitation. Please try again.');
        }
    }

    /**
     * Reject a collaboration invitation (managers and super admins only).
     *
     * @param Request $request
     * @param string $taskId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectInvitation(Request $request, string $taskId)
    {
        try {
            $user = auth()->user();
            $task = Task::findOrFail($taskId);

            // Only managers and super admins can reject collaboration invitations
            if (! ($user->isSuperAdmin() || $user->isManager())) {
                abort(Response::HTTP_FORBIDDEN);
            }

            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $collaboration = $task->collaborators()->where('user_id', $data['user_id'])->first();

            // Check if collaboration exists and is pending
            if (! $collaboration || $collaboration->pivot->invitation_status !== 'pending') {
                abort(Response::HTTP_FORBIDDEN);
            }

            // Update collaboration status to rejected
            $collaboration->pivot->invitation_status = 'rejected';
            $collaboration->pivot->save();

            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Collaborator Rejected',
                'user_id' => $user->id,
            ]);

            // Notify the collaborator about rejection
            $collaboratorUser = \App\Models\User::find($data['user_id']);

            if ($collaboratorUser) {
                $this->createNotification($collaboratorUser, 'Collaboration Rejected', "Your request to collaborate on '{$task->title}' has been rejected.");
            }

            return redirect()->route('tasks.show', $task)->with('success', 'Collaboration rejected successfully.');
        } catch (\Exception $e) {
            \Log::error('Error rejecting collaboration invitation: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject collaboration invitation. Please try again.');
        }
    }

    /**
     * Create a task notification for a user.
     *
     * @param mixed $user
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return void
     */
    protected function createNotification($user, $title, $message, $link = null)
    {
        TaskNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    /**
     * Check if user has permission to access a task.
     *
     * @param Task $task
     * @param mixed $user
     * @return bool
     */
    protected function canAccessTask(Task $task, $user): bool
    {
        return $user->isSuperAdmin()
            || $user->isManager()
            || $task->created_by === $user->id
            || $task->assigned_to === $user->id
            || $task->collaborators()->where('user_id', $user->id)->where('invitation_status', 'accepted')->exists();
    }

    /**
     * Check if user has permission to edit a task.
     *
     * @param Task $task
     * @param mixed $user
     * @return bool
     */
    protected function canEditTask(Task $task, $user): bool
    {
        return $user->isSuperAdmin()
            || $user->isManager()
            || $task->created_by === $user->id
            || $task->assigned_to === $user->id
            || $task->collaborators()->where('user_id', $user->id)->where('invitation_status', 'accepted')->exists();
    }

    /**
     * Check if user has permission to update task status.
     *
     * @param Task $task
     * @param mixed $user
     * @return bool
     */
    protected function canUpdateStatus(Task $task, $user): bool
    {
        return $user->isSuperAdmin()
            || $user->isManager()
            || $task->assigned_to === $user->id
            || $task->created_by === $user->id
            || $task->collaborators()->where('user_id', $user->id)->where('invitation_status', 'accepted')->exists();
    }

    /**
     * Request a time revision for a task.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestTimeRevision(Request $request, string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $user = auth()->user();

            // Check if user has permission to access this task
            if (! $this->canAccessTask($task, $user)) {
                abort(Response::HTTP_FORBIDDEN);
            }

            $data = $request->validate([
                'requested_due_date' => 'required|date',
                'requested_due_time' => 'required',
                'reason' => 'nullable|string',
            ]);

            // Create time revision request
            $revisionRequest = TimeRevisionRequest::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'requested_due_date' => $data['requested_due_date'],
                'requested_due_time' => $data['requested_due_time'],
                'reason' => $data['reason'] ?? null,
                'status' => 'pending',
            ]);

            // Update task time revision status
            $task->update(['time_revision_status' => 'pending']);

            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Time Revision Requested',
                'user_id' => $user->id,
            ]);

            // Notify managers about the time revision request
            $managers = \App\Models\User::whereIn('role', ['super_admin', 'manager'])->get();
            foreach ($managers as $manager) {
                $this->createNotification($manager, 'Time Revision Request', "A time revision request has been submitted for task '{$task->title}'.", route('requests.index'));
            }

            return redirect()->route('tasks.show', $task)->with('success', 'Time revision request submitted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error requesting time revision: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit time revision request. Please try again.');
        }
    }

    /**
     * Get task details for modal display.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaskDetails(string $id)
    {
        try {
            $task = Task::with(['creator', 'assignedUser', 'collaborators', 'files', 'history.user', 'timeRevisionRequests'])->findOrFail($id);
            $user = auth()->user();

            // Check if user has permission to access this task
            if (! $this->canAccessTask($task, $user)) {
                abort(Response::HTTP_FORBIDDEN);
            }

            $html = view('tasks.partials.details', compact('task'))->render();

            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            \Log::error('Error fetching task details: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load task details'], 500);
        }
    }
}
