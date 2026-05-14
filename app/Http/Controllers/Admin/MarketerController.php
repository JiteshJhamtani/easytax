<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MarketerController extends Controller implements HasMiddleware
{
    // The new Laravel 11+ way to apply middleware inside a controller
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (strtoupper(auth()->user()->role) !== 'ADMIN') {
                    abort(403, 'Unauthorized action.');
                }
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        return view('admin.marketers.index');
    }

    public function create()
    {
        return view('admin.marketers.create');
    }

   public function datatable()
    {
        // Keeping your robust role check
        $marketers = User::query()->whereIn('role', ['marketer', 'MARKETER']);

        return datatables()->of($marketers)
            // 1. Keeping your exact Leads Count calculation
            ->addColumn('leads_count', function ($marketer) {
                return \App\Models\Lead::where('marketer_id', $marketer->id)->count();
            })
            // 2. NEW: Combined Name & Email UI
            ->addColumn('name_email', function ($row) {
                return '<div class="font-weight-bold text-dark">' . $row->name . '</div>
                        <div class="small text-muted">' . $row->email . '</div>';
            })
            // 3. NEW: Status Badge UI
            ->editColumn('is_active', function ($row) {
                return $row->is_active 
                    ? '<span class="badge badge-success px-2 py-1">Active</span>' 
                    : '<span class="badge badge-danger px-2 py-1">Suspended</span>';
            })
            // 4. NEW: The 3 Icon Action Buttons (matching the Operator page)
            ->addColumn('action', function ($row) {
                // Toggle Button
                $btn = '<form action="' . route('crm.marketers.toggle-status', $row->id) . '" method="POST" class="d-inline">';
                $btn .= csrf_field() . method_field('PATCH');
                $btnClass = $row->is_active ? 'btn-outline-warning' : 'btn-outline-success';
                $iconClass = $row->is_active ? 'fa-ban' : 'fa-check';
                $title = $row->is_active ? 'Suspend' : 'Activate';
                $btn .= '<button type="submit" class="btn btn-sm ' . $btnClass . ' mr-1" title="' . $title . '"><i class="fas ' . $iconClass . '"></i></button></form>';

                // Edit Button
                $btn .= '<a href="' . route('crm.marketers.edit', $row->id) . '" class="btn btn-sm btn-outline-primary mr-1" title="Edit"><i class="fas fa-edit"></i></a>';

                // Delete Button
                $btn .= '<form action="' . route('crm.marketers.destroy', $row->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Permanently delete this marketer?\');">';
                $btn .= csrf_field() . method_field('DELETE');
                $btn .= '<button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button></form>';

                return $btn;
            })
            ->rawColumns(['name_email', 'is_active', 'action'])
            ->make(true);
    }

   // 1. Load the Edit Page
    public function edit($id)
    {
        $marketer = User::whereIn('role', ['marketer', 'MARKETER'])->findOrFail($id);
        return view('admin.marketers.edit', compact('marketer'));
    }

    // 2. Save the Updates
    public function update(Request $request, $id)
    {
        $marketer = User::whereIn('role', ['marketer', 'MARKETER'])->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $marketer->id,
        ]);

        $marketer->name = $request->name;
        $marketer->email = $request->email;

        // Only update password if they typed a new one.
        // No Hash::make() because Laravel 11's User model does it automatically!
        if ($request->filled('password')) {
            $marketer->password = $request->password;
        }

        $marketer->save();

        return redirect()->route('crm.marketers.index')->with('success', 'Marketer updated successfully!');
    }

    // 3. Delete Marketer
    public function destroy($id)
    {
        $marketer = User::whereIn('role', ['marketer', 'MARKETER'])->findOrFail($id);
        $marketer->delete();

        return back()->with('success', 'Marketer removed completely.');
    } 

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => 'marketer',
            'is_active' => true
        ]);

        return redirect()->route('crm.marketers.index')->with('success', 'Marketer created successfully');
    }

    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'Marketer status updated');
    }
}