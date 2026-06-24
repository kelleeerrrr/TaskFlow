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
    public function index()
    {
        $user = auth()->user();

        if (! $user->isManager()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $collaborationRequests = TaskCollaborator::with(['task.creator', 'user'])
            ->where('invitation_status', 'pending')
            ->latest()
            ->get();

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
    }

    public function approveCollaboration(Request $request, $taskId)
    {
        $user = auth()->user();

        if (! $user->isManager()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $task = Task::findOrFail($taskId);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $collaboration = $task->collaborators()->where('user_id', $data['user_id'])->first();

        if (! $collaboration || $collaboration->pivot->invitation_status !== 'pending') {
            abort(Response::HTTP_FORBIDDEN);
        }

        $collaboration->pivot->invitation_status = 'accepted';
        $collaboration->pivot->save();

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Collaborator Approved',
            'user_id' => $user->id,
        ]);

        $collaboratorUser = \App\Models\User::find($data['user_id']);

        if ($collaboratorUser) {
            $this->createNotification($collaboratorUser, 'Collaboration Approved', "Your request to collaborate on '{$task->title}' has been approved.", route('tasks.show', $task->id));
        }

        return redirect()->route('requests.index')->with('success', 'Collaboration approved successfully.');
    }

    public function rejectCollaboration(Request $request, $taskId)
    {
        $user = auth()->user();

        if (! $user->isManager()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $task = Task::findOrFail($taskId);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $collaboration = $task->collaborators()->where('user_id', $data['user_id'])->first();

        if (! $collaboration || $collaboration->pivot->invitation_status !== 'pending') {
            abort(Response::HTTP_FORBIDDEN);
        }

        $collaboration->pivot->invitation_status = 'rejected';
        $collaboration->pivot->save();

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Collaborator Rejected',
            'user_id' => $user->id,
        ]);

        $collaboratorUser = \App\Models\User::find($data['user_id']);

        if ($collaboratorUser) {
            $this->createNotification($collaboratorUser, 'Collaboration Rejected', "Your request to collaborate on '{$task->title}' has been rejected.");
        }

        return redirect()->route('requests.index')->with('success', 'Collaboration rejected successfully.');
    }

    public function approveTimeRevision($id)
    {
        $user = auth()->user();

        if (! $user->isManager()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $revisionRequest = TimeRevisionRequest::findOrFail($id);
        $task = $revisionRequest->task;

        $task->update([
            'due_date' => $revisionRequest->requested_due_date,
            'due_time' => $revisionRequest->requested_due_time,
            'time_revision_status' => 'approved',
        ]);

        $revisionRequest->update(['status' => 'approved']);

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Time Revision Approved',
            'user_id' => $user->id,
        ]);

        $this->createNotification($revisionRequest->user, 'Time Revision Approved', "Your time revision request for task '{$task->title}' has been approved.");

        return redirect()->route('requests.index')->with('success', 'Time revision approved successfully.');
    }

    public function rejectTimeRevision($id)
    {
        $user = auth()->user();

        if (! $user->isManager()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $revisionRequest = TimeRevisionRequest::findOrFail($id);
        $task = $revisionRequest->task;

        $task->update(['time_revision_status' => 'rejected']);
        $revisionRequest->update(['status' => 'rejected']);

        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'Time Revision Rejected',
            'user_id' => $user->id,
        ]);

        $this->createNotification($revisionRequest->user, 'Time Revision Rejected', "Your time revision request for task '{$task->title}' has been rejected.", route('tasks.show', $task->id));

        return redirect()->route('requests.index')->with('success', 'Time revision rejected successfully.');
    }

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

    public function getCollaborationDetails($id)
    {
        $request = TaskCollaborator::with(['task.creator', 'user'])->findOrFail($id);
        
        $html = view('requests.partials.collaboration-details', compact('request'))->render();
        
        return response()->json(['html' => $html]);
    }

    public function getTimeRevisionDetails($id)
    {
        $request = TimeRevisionRequest::with(['task', 'user'])->findOrFail($id);
        
        $html = view('requests.partials.time-revision-details', compact('request'))->render();
        
        return response()->json(['html' => $html]);
    }
}
