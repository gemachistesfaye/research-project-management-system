<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class LoadNotifications
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $notifCount = 0;
            $notifItems = [];
            $role = $user->role;

            if ($role === 'reviewer') {
                $notifCount = \App\Models\Evaluation::where('examiner_id', $user->id)
                    ->where('decision', 'Pending')->count();
            } elseif (in_array($role, ['coordinator', 'dh'])) {
                $notifCount = \App\Models\Project::where('status', 'Submitted')->count();
            } elseif (in_array($role, ['dean', 'rcsc', 'vparttcs'])) {
                $notifCount = \App\Models\BudgetRequest::where('status', 'Pending')->count();
            } elseif ($role === 'pi') {
                $notifCount = \App\Models\Project::where('pi_id', $user->id)
                    ->whereNotIn('status', ['Completed', 'Terminated'])->count();
            } elseif ($role === 'irerc') {
                $notifCount = \App\Models\IRERCClearance::where('status', 'Pending')->count();
            } elseif ($role === 'admin') {
                $notifCount = 1;
                $notifItems[] = [
                    'title' => 'System Overview',
                    'desc'  => \App\Models\User::where('status', 'active')->count() . ' active users',
                    'time'  => 'Now',
                    'icon'  => 'bi-gear text-primary',
                ];
            } elseif ($role === 'tm') {
                $notifCount = \App\Models\ProjectMember::where('user_id', $user->id)->count();
            }

            View::share('notifCount', $notifCount);
            View::share('notifItems', $notifItems);
        }

        return $next($request);
    }
}
