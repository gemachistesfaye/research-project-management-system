<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\ThematicArea;
use App\Models\Department;
use App\Models\Role;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        $query = User::with('department');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $departments = Department::all();
        return view('admin.users', compact('users', 'departments'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|string|unique:users,staff_id',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role'     => 'required|in:pi,tm,reviewer,dh,coordinator,dean,irerc,vparttcs,rcsc,finance,admin',
            'dept_id'  => 'nullable|exists:departments,id',
        ]);

        User::create([
            'staff_id' => $request->staff_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'role_id' => Role::where('name', $request->role)->value('id'),
            'dept_id' => $request->dept_id,
            'status' => 'active',
        ]);

        return back()->with('success', "New user account '{$request->name}' created successfully with role " . strtoupper($request->role) . ".");
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', "Password for user '{$user->name}' reset successfully.");
    }

    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('user_search')) {
            $search = $request->user_search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        $filters = $request->only(['date_from', 'date_to', 'action', 'user_search']);

        return view('admin.audit_logs', compact('logs', 'filters'));
    }

    public function thematicAreas()
    {
        $thematics = ThematicArea::all();
        return view('admin.thematic_areas', compact('thematics'));
    }

    public function storeThematicArea(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:500',
            'category'    => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ThematicArea::create($request->only('title', 'category', 'description'));

        return back()->with('success', 'Thematic area created successfully.');
    }

    public function updateThematicArea(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string|max:500',
            'category'    => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $thematic = ThematicArea::findOrFail($id);
        $thematic->update($request->only('title', 'category', 'description'));

        return back()->with('success', 'Thematic area updated successfully.');
    }

    public function destroyThematicArea($id)
    {
        ThematicArea::findOrFail($id)->delete();
        return back()->with('success', 'Thematic area deleted successfully.');
    }

    public function hrmsSync()
    {
        return view('admin.hrms_sync');
    }

    // Department Management
    public function departments()
    {
        $departments = Department::with('college', 'users')->get();
        $colleges = \App\Models\College::all();
        return view('admin.departments', compact('departments', 'colleges'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:20|unique:departments,code',
            'college_id' => 'required|exists:colleges,id',
        ]);

        Department::create($request->only('name', 'code', 'college_id'));

        return back()->with('success', 'Department created successfully.');
    }

    public function updateDepartment(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:20|unique:departments,code,' . $id,
            'college_id' => 'required|exists:colleges,id',
        ]);

        $dept = Department::findOrFail($id);
        $dept->update($request->only('name', 'code', 'college_id'));

        return back()->with('success', 'Department updated successfully.');
    }

    public function destroyDepartment($id)
    {
        Department::findOrFail($id)->delete();
        return back()->with('success', 'Department deleted successfully.');
    }

    // College Management
    public function colleges()
    {
        $colleges = \App\Models\College::with('departments')->get();
        return view('admin.colleges', compact('colleges'));
    }

    public function storeCollege(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:colleges,code',
        ]);

        \App\Models\College::create($request->only('name', 'code'));

        return back()->with('success', 'College created successfully.');
    }

    public function updateCollege(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:colleges,code,' . $id,
        ]);

        $college = \App\Models\College::findOrFail($id);
        $college->update($request->only('name', 'code'));

        return back()->with('success', 'College updated successfully.');
    }

    public function destroyCollege($id)
    {
        \App\Models\College::findOrFail($id)->delete();
        return back()->with('success', 'College deleted successfully.');
    }
}
