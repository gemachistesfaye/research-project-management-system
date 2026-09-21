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
            ->take(10)
            ->get();
        return view('profile.show', compact('user', 'recentLogs'));
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
