<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\TimeRevisionRequest;
use App\Models\TaskHistory;
use App\Models\TaskNotification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestController extends Controller
{
    /**
     * Display a listing of pending requests (collaboration and time revision).
     * Only accessible to managers and super admins.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $user = auth()->user();

            // Only managers and super admins can view requests
            if (! ($user->isManager() || $user->isSuperAdmin())) {
                abort(Response::HTTP_FORBIDDEN);
            }

            // Fetch pending collaboration requests with related data
            $collaborationRequests = TaskCollaborator::with(['task.creator', 'user'])
                ->where('invitation_status', 'pending')
                ->latest()
                ->get();

            // Fetch pending time revision requests with related data
            $timeRevisionRequests = TimeRevisionRequest::with(['task', 'user'])
                ->where('status', 'pending')
                ->latest()
                ->get();

            // Summary data for collaboration requests
            $collaborationSummary = [
                'pending' => TaskCollaborator::where('invitation_status', 'pending')->count(),
                'approved' => TaskCollaborator::where('invitation_status', 'accepted')->count(),
                'rejected' => TaskCollaborator::where('invitation_status', 'rejected')->count(),
            ];

            // Summary data for time revision requests
            $timeRevisionSummary = [
                'pending' => TimeRevisionRequest::where('status', 'pending')->count(),
                'approved' => TimeRevisionRequest::where('status', 'approved')->count(),
                'rejected' => TimeRevisionRequest::where('status', 'rejected')->count(),
            ];

            return view('requests.index', compact('collaborationRequests', 'timeRevisionRequests', 'collaborationSummary', 'timeRevisionSummary'));
        } catch (\Exception $e) {
            \Log::error('Error fetching requests: ' . $e->getMessage());
            return back()->with('error', 'Failed to load requests. Please try again.');
        }
    }

    /**
     * Approve a collaboration request (managers and super admins only).
     *
     * @param Request $request
     * @param string $taskId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveCollaboration(Request $request, $taskId)
    {
        try {
            $user = auth()->user();

            // Only managers and super admins can approve collaboration requests
            if (! ($user->isManager() || $user->isSuperAdmin())) {
                abort(Response::HTTP_FORBIDDEN);
            }

            $task = Task::findOrFail($taskId);

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

            return redirect()->route('requests.index')->with('success', 'Collaboration approved successfully.');
        } catch (\Exception $e) {
            \Log::error('Error approving collaboration: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve collaboration. Please try again.');
        }
    }

    /**
     * Reject a collaboration request (managers and super admins only).
     *
     * @param Request $request
     * @param string $taskId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectCollaboration(Request $request, $taskId)
    {
        try {
            $user = auth()->user();

            // Only managers and super admins can reject collaboration requests
            if (! ($user->isManager() || $user->isSuperAdmin())) {
                abort(Response::HTTP_FORBIDDEN);
            }

            $task = Task::findOrFail($taskId);

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

            return redirect()->route('requests.index')->with('success', 'Collaboration rejected successfully.');
        } catch (\Exception $e) {
            \Log::error('Error rejecting collaboration: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject collaboration. Please try again.');
        }
    }

    /**
     * Approve a time revision request (managers only).
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveTimeRevision($id)
    {
        try {
            $user = auth()->user();

            // Only managers can approve time revision requests
            if (! $user->isManager()) {
                abort(Response::HTTP_FORBIDDEN);
            }

            $revisionRequest = TimeRevisionRequest::findOrFail($id);
            $task = $revisionRequest->task;

            // Update task with new due date and time
            $task->update([
                'due_date' => $revisionRequest->requested_due_date,
                'due_time' => $revisionRequest->requested_due_time,
                'time_revision_status' => 'approved',
            ]);

            // Update revision request status
            $revisionRequest->update(['status' => 'approved']);

            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Time Revision Approved',
                'user_id' => $user->id,
            ]);

            // Notify the requester about approval
            $this->createNotification($revisionRequest->user, 'Time Revision Approved', "Your time revision request for task '{$task->title}' has been approved.");

            return redirect()->route('requests.index')->with('success', 'Time revision approved successfully.');
        } catch (\Exception $e) {
            \Log::error('Error approving time revision: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve time revision. Please try again.');
        }
    }

    /**
     * Reject a time revision request (managers only).
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectTimeRevision($id)
    {
        try {
            $user = auth()->user();

            // Only managers can reject time revision requests
            if (! $user->isManager()) {
                abort(Response::HTTP_FORBIDDEN);
            }

            $revisionRequest = TimeRevisionRequest::findOrFail($id);
            $task = $revisionRequest->task;

            // Update task and revision request status to rejected
            $task->update(['time_revision_status' => 'rejected']);
            $revisionRequest->update(['status' => 'rejected']);

            TaskHistory::create([
                'task_id' => $task->id,
                'action' => 'Time Revision Rejected',
                'user_id' => $user->id,
            ]);

            // Notify the requester about rejection
            $this->createNotification($revisionRequest->user, 'Time Revision Rejected', "Your time revision request for task '{$task->title}' has been rejected.", route('tasks.show', $task->id));

            return redirect()->route('requests.index')->with('success', 'Time revision rejected successfully.');
        } catch (\Exception $e) {
            \Log::error('Error rejecting time revision: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject time revision. Please try again.');
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
     * Get collaboration request details for modal display.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCollaborationDetails($id)
    {
        try {
            $request = TaskCollaborator::with(['task.creator', 'user'])->findOrFail($id);
            
            $html = view('requests.partials.collaboration-details', compact('request'))->render();
            
            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            \Log::error('Error fetching collaboration details: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load collaboration details'], 500);
        }
    }

    /**
     * Get time revision request details for modal display.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTimeRevisionDetails($id)
    {
        try {
            $request = TimeRevisionRequest::with(['task', 'user'])->findOrFail($id);
            
            $html = view('requests.partials.time-revision-details', compact('request'))->render();
            
            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            \Log::error('Error fetching time revision details: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load time revision details'], 500);
        }
    }
}
