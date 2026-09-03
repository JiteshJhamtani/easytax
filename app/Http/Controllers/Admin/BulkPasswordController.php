<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BulkPasswordController extends Controller
{
    /**
     * Display a listing of all non-admin users for bulk password update.
     */
    public function index()
    {
        $currentUserId = auth()->id();

        // Fetch all users who are NOT super-admins (agents, sub-admins, operators/team, marketers)
        // Check both database 'role' column AND Spatie roles
        $users = User::query()
            ->where('id', '!=', $currentUserId)
            ->where(function ($q) {
                $q->whereNotIn('role', ['ADMIN', 'SUPER_ADMIN', 'admin', 'super_admin'])
                    ->orWhereNull('role');
            })
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['admin', 'super-admin', 'super_admin']);
            })
            ->with('roles')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.bulk-passwords.index', compact('users'));
    }

    /**
     * Update the passwords for the selected users.
     */
    public function update(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $hashedPassword = Hash::make($request->password);
        $currentUserId = auth()->id();

        // Security check: Never allow changing passwords of super admins or yourself via bulk update
        $updatedCount = User::whereIn('id', $request->user_ids)
            ->where('id', '!=', $currentUserId)
            ->where(function ($q) {
                $q->whereNotIn('role', ['ADMIN', 'SUPER_ADMIN', 'admin', 'super_admin'])
                    ->orWhereNull('role');
            })
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['admin', 'super-admin', 'super_admin']);
            })
            ->update([
                'password' => $hashedPassword,
            ]);

        return redirect()->back()->with('success', "{$updatedCount} user(s) password(s) have been successfully updated.");
    }
}
