<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    // 1. Show the list of Operators
   // 1. Show the list of Operators
    public function index()
    {
        $teamMembers = User::whereIn('role', ['team', 'TEAM'])->orderBy('id', 'desc')->get();

        $assignedCounts = \App\Models\Application::whereIn('assigned_to', $teamMembers->pluck('id'))
            ->whereNotIn('status', ['COMPLETED', 'CANCELLED', 'REJECTED']) // Optional: Only count active/pending tasks
            ->selectRaw('assigned_to, count(*) as count')
            ->groupBy('assigned_to')
            ->pluck('count', 'assigned_to');

        return view('admin.team.index', compact('teamMembers', 'assignedCounts'));
    }

   // 3. Create Operator
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            
            'password' => $request->password, 
            
            'mobile_number' => $request->phone, 
            
            'role' => 'TEAM',
            'is_active' => true,
        ]);

        return redirect()->route('admin.team.index')->with('success', 'Operator created successfully!');
    }

    // 5. Update Operator
    public function update(Request $request, $id)
    {
        $teamMember = User::whereIn('role', ['team', 'TEAM'])->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $teamMember->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $teamMember->name = $request->name;
        $teamMember->email = $request->email;
        
        $teamMember->mobile_number = $request->phone;

        if ($request->filled('password')) {
            $teamMember->password = $request->password;
        }

        $teamMember->save();

        return redirect()->route('admin.team.index')->with('success', 'Operator updated successfully!');
    }

    // 4. Delete an Operator
    public function destroy($id)
    {
        $teamMember = User::where('role', 'team')->findOrFail($id);
        $teamMember->delete();

        return back()->with('success', 'Operator removed completely.');
    }

    // Loads the new dedicated Create Page
    public function create()
    {
        return view('admin.team.create');
    }

    // Loads the new dedicated Edit Page
    public function edit($id)
    {
        $teamMember = User::where('role', 'team')->findOrFail($id);
        return view('admin.team.edit', compact('teamMember'));
    }

    // Flips the user between Active (1) and Suspended (0)
    public function toggleStatus($id)
    {
        $teamMember = User::where('role', 'team')->findOrFail($id);
        $teamMember->is_active = !$teamMember->is_active; // Flips the boolean
        $teamMember->save();

        $status = $teamMember->is_active ? 'Activated' : 'Suspended';
        return back()->with('success', "Operator account has been {$status}.");
    }
}