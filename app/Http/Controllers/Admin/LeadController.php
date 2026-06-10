<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LeadController extends Controller
{

   public function dashboard()
    {
        $user = auth()->user();
        
        // Gather quick stats for the Marketer
        $totalLeads = Lead::where('marketer_id', $user->id)->count();
        $convertedLeads = Lead::where('marketer_id', $user->id)->where('status', 'CONVERTED')->count();
        $lostLeads = Lead::where('marketer_id', $user->id)->where('status', 'LOST')->count();

        $recentLeads = Lead::where('marketer_id', $user->id)
                           ->orderBy('created_at', 'desc')
                           ->take(5)
                           ->get();

        return view('marketer.dashboard', compact('totalLeads', 'convertedLeads', 'lostLeads', 'recentLeads'));
    }
   public function index()
    {
        // Removed the compact('marketers') since we moved it to create()
        return view('admin.leads.index');
    }

    public function create()
    {
        // Get active marketers for the dropdown
        $marketers = User::whereIn('role', ['marketer', 'MARKETER'])->where('is_active', true)->get();
        return view('admin.leads.create', compact('marketers'));
    }

    public function datatable()
    {
        $user = auth()->user();
        $query = Lead::with('marketer')->where(function($q) {
            $q->where('source', '!=', 'VLE')->orWhereNull('source');
        });

        // Security: Marketers only see their own leads
        if (strtolower($user->role) === 'marketer') {
            $query->where('marketer_id', $user->id);
        }

        // Order by newest first
        $query->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addColumn('date', function ($lead) {
                return $lead->created_at->format('d M Y, h:i A');
            })
            ->addColumn('marketer_name', function ($lead) {
                return $lead->marketer ? $lead->marketer->name : '<span class="text-muted font-italic">Direct / None</span>';
            })
            ->addColumn('status_badge', function ($lead) {
                $colors = [
                    'NEW' => 'badge-info-soft',
                    'CONTACTED' => 'badge-warning-soft',
                    'IN_DISCUSSION' => 'badge-primary-soft',
                    'CONVERTED' => 'badge-success-soft',
                    'LOST' => 'badge-danger-soft'
                ];
                $class = $colors[$lead->status] ?? 'badge-secondary-soft';
                return '<span class="badge ' . $class . ' px-2 py-1">' . str_replace('_', ' ', $lead->status) . '</span>';
            })
            ->addColumn('action', function ($lead) {
                // Button to open a quick edit modal (we will build this in the view)
               return '<a href="' . route('crm.leads.edit', $lead->id) . '" class="btn btn-sm btn-primary shadow-sm font-weight-bold">Update Status</a>';
            })
            ->rawColumns(['marketer_name', 'status_badge', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $leadData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'service_interested' => 'nullable|string',
            'source' => 'nullable|string',
            'notes' => 'nullable|string',
            'marketer_id' => 'nullable|exists:users,id',
        ]);

        // If a marketer submits it, automatically assign them.
        // If an admin submits it, check if they selected a marketer in the dropdown.
        if (strtoupper(auth()->user()->role) === 'MARKETER') {
            $leadData['marketer_id'] = auth()->id();
        }

        Lead::create($leadData);

     return redirect()->route('crm.leads.index')->with('success', 'Lead captured successfully!');
    }

    // --- NEW VLE METHODS ---
    public function createVle()
    {
        return view('marketer.vle.create');
    }

    public function storeVle(Request $request)
    {
        $leadData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'service_interested' => 'nullable|string',
            'amount' => 'required|integer|min:10', // STRICT RULE: Minimum 10
            'notes' => 'nullable|string',
        ]);
        $leadData['source'] = 'VLE'; // HARDCODED: Automatically tags as VLE
        $leadData['marketer_id'] = auth()->id(); // Assigned to the logged-in marketer

        Lead::create($leadData);

        return redirect()->route('marketer.dashboard')->with('success', 'VLE Customer added successfully!');
    }


    public function edit(Lead $lead)
    {
        // Security check: Marketers cannot edit leads that belong to other marketers or admins
        if (strtoupper(auth()->user()->role) === 'MARKETER' && $lead->marketer_id !== auth()->id()) {
            abort(403);
        }

        return view('admin.leads.edit', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        // Security check for Marketers trying to update someone else's lead
        if (strtoupper(auth()->user()->role) === 'MARKETER' && $lead->marketer_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|string|in:NEW,CONTACTED,IN_DISCUSSION,CONVERTED,LOST',
            'notes' => 'nullable|string'
        ]);

        $lead->status = $request->status;
        
        if ($request->filled('notes')) {
            // Append new notes with a timestamp
            $timestamp = now()->format('d M, h:i A');
            $lead->notes = $lead->notes . "\n\n[" . $timestamp . "]: " . $request->notes;
        }

        $lead->save();

        return back()->with('success', 'Lead updated successfully!');
    }

    public function indexVle()
    {
        return view('marketer.vle.index');
    }

    public function vleDatatable()
    {
        $user = auth()->user();
        
        // Fetch ONLY VLE leads
        $query = Lead::with('marketer')->where('source', 'VLE');

        if (strtolower($user->role) === 'marketer') {
            $query->where('marketer_id', $user->id);
        }

        $query->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addColumn('date', function ($lead) {
                return $lead->created_at->format('d M Y, h:i A');
            })
            ->addColumn('contact_info', function ($lead) {
                return '<div><div class="font-weight-bold text-dark">'.$lead->phone.'</div><div class="text-muted small">'.($lead->email ?? '').'</div></div>';
            })
            ->addColumn('status_badge', function ($lead) {
                $colors = [
                    'NEW' => 'badge-info',
                    'CONTACTED' => 'badge-warning',
                    'IN_DISCUSSION' => 'badge-primary',
                    'CONVERTED' => 'badge-success',
                    'LOST' => 'badge-danger'
                ];
                $class = $colors[$lead->status] ?? 'badge-secondary';
                return '<span class="badge ' . $class . ' px-2 py-1">' . str_replace('_', ' ', $lead->status) . '</span>';
            })
            ->addColumn('action', function ($lead) {
                return '<a href="' . route('crm.leads.edit', $lead->id) . '" class="btn btn-sm btn-primary shadow-sm font-weight-bold" style="border-radius:6px;">Update Status</a>';
            })
            ->rawColumns(['contact_info', 'status_badge', 'action'])
            ->make(true);
    }
}