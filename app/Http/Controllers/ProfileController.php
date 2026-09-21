<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\AuditLog;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load('department.college');
        $recentLogs = AuditLog::where('user_id', $user->id)
            ->latest()
            ->take(15)
            ->get();

        // Calculate role-specific mini statistics
        $stats = [];
        if ($user->role === 'pi') {
            $stats = [
                ['label' => 'Total Proposals', 'val' => \App\Models\Project::where('pi_id', $user->id)->count(), 'icon' => 'bi-folder2-open'],
                ['label' => 'Active Research', 'val' => \App\Models\Project::where('pi_id', $user->id)->where('status', 'Active')->count(), 'icon' => 'bi-play-circle'],
                ['label' => 'Completed', 'val' => \App\Models\Project::where('pi_id', $user->id)->where('status', 'Completed')->count(), 'icon' => 'bi-trophy'],
            ];
        } elseif ($user->role === 'reviewer') {
            $stats = [
                ['label' => 'Assigned', 'val' => \App\Models\Evaluation::where('examiner_id', $user->id)->count(), 'icon' => 'bi-file-earmark-check'],
                ['label' => 'Completed', 'val' => \App\Models\Evaluation::where('examiner_id', $user->id)->where('decision', '!=', 'Pending')->count(), 'icon' => 'bi-check-all'],
                ['label' => 'Pending', 'val' => \App\Models\Evaluation::where('examiner_id', $user->id)->where('decision', 'Pending')->count(), 'icon' => 'bi-hourglass-split'],
            ];
        } elseif ($user->role === 'admin') {
            $stats = [
                ['label' => 'Total Users', 'val' => \App\Models\User::count(), 'icon' => 'bi-people'],
                ['label' => 'Active Accounts', 'val' => \App\Models\User::where('status', 'active')->count(), 'icon' => 'bi-person-check'],
                ['label' => 'Audit Events', 'val' => \App\Models\AuditLog::count(), 'icon' => 'bi-shield-check'],
            ];
        } elseif (in_array($user->role, ['dh', 'coordinator', 'dean', 'rcsc', 'vparttcs', 'irerc', 'finance'])) {
            $stats = [
                ['label' => 'College Depts', 'val' => \App\Models\Department::count(), 'icon' => 'bi-building'],
                ['label' => 'All Grants', 'val' => \App\Models\Project::count(), 'icon' => 'bi-journal-richtext'],
                ['label' => 'Active Call', 'val' => 'AY 26/27', 'icon' => 'bi-calendar-check'],
            ];
        }

        return view('profile.show', compact('user', 'recentLogs', 'stats'));
    }

    public function deleteAvatar()
    {
        $user = Auth::user();
        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }
        $user->update(['avatar_path' => null]);

        \App\Services\AuditService::log(
            'UPDATE_USER',
            'User',
            $user->id,
            "User {$user->name} removed their profile photo.",
            $user->id
        );

        return back()->with('success', 'Profile photo removed.');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'   => 'sometimes|required|string|max:255',
            'email'  => 'sometimes|required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only('name', 'email');

        if ($request->hasFile('avatar')) {
            $oldPath = $user->avatar_path;
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $user->update($data);

        \App\Services\AuditService::log(
            'UPDATE_USER',
            'User',
            $user->id,
            "User {$user->name} updated their personal profile details" . ($request->hasFile('avatar') ? ' and avatar' : '') . '.',
            $user->id
        );

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        \App\Services\AuditService::log(
            'RESET_PASSWORD',
            'User',
            $user->id,
            "User {$user->name} successfully changed their account password via self-service.",
            $user->id
        );

        return back()->with('success', 'Password changed successfully.');
    }
}
