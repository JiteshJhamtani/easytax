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
        $marketers = User::query()->whereIn('role', ['marketer', 'MARKETER']);

        return DataTables::of($marketers)
            ->addColumn('leads_count', function ($marketer) {
                // Count how many leads this marketer generated
                return Lead::where('marketer_id', $marketer->id)->count();
            })
            ->addColumn('action', function ($marketer) {
                $toggle = $marketer->is_active ? 'Suspend' : 'Activate';
                $btnClass = $marketer->is_active ? 'btn-outline-danger' : 'btn-outline-success';
                
                return '
                <form method="POST" action="' . route('crm.marketers.toggle-status', $marketer) . '" style="display:inline;">
                    ' . csrf_field() . method_field('PATCH') . '
                    <button class="btn btn-sm ' . $btnClass . ' font-weight-bold shadow-sm">' . $toggle . '</button>
                </form>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
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