<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;

class TeamMemberController extends Controller
{
    public function index($projectId)
    {
        $user = Auth::user();
        $project = Project::where('project_id', $projectId)
            ->with(['members.user', 'pi'])
            ->firstOrFail();

        if ($user->role === 'pi' && (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only view team members for your own projects.');
        }

        $currentMemberIds = $project->members->pluck('user_id')->toArray();
        $availableUsers = User::where('id', '!=', $project->pi_id)
            ->where('status', 'active')
            ->whereNotIn('id', $currentMemberIds)
            ->get();

        return view('team.index', compact('project', 'availableUsers'));
    }

    public function store(Request $request, $projectId)
    {
        $user = Auth::user();
        $project = Project::where('project_id', $projectId)->firstOrFail();

        if ((int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only manage team members for your own projects.');
        }

        $request->validate([
            'user_id'                => 'required|exists:users,id',
            'role_in_project'        => 'required|string|max:255',
            'contribution_percentage'=> 'required|numeric|min:0|max:100',
        ]);

        ProjectMember::create([
            'project_id'             => $projectId,
            'user_id'                => $request->user_id,
            'role_in_project'        => $request->role_in_project,
            'contribution_percentage'=> $request->contribution_percentage,
        ]);

        return back()->with('success', 'Team member added successfully.');
    }

    public function update(Request $request, $memberId)
    {
        $user = Auth::user();
        $member = ProjectMember::findOrFail($memberId);
        $project = Project::where('project_id', $member->project_id)->firstOrFail();

        if ((int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only update team members for your own projects.');
        }

        $request->validate([
            'role_in_project'        => 'required|string|max:255',
            'contribution_percentage'=> 'required|numeric|min:0|max:100',
        ]);

        $member->update($request->only('role_in_project', 'contribution_percentage'));

        return back()->with('success', 'Team member role updated.');
    }

    public function destroy($memberId)
    {
        $user = Auth::user();
        $member = ProjectMember::findOrFail($memberId);
        $project = Project::where('project_id', $member->project_id)->firstOrFail();

        if ((int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only remove team members from your own projects.');
        }

        $member->delete();

        return back()->with('success', 'Team member removed.');
    }
}
