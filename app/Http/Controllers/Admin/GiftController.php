<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftController extends Controller
{
    public function index()
    {
        $gifts = Gift::withCount('conditionGroups')->latest()->paginate(15);

        return view('admin.gifts.index', compact('gifts'));
    }

    public function create()
    {
        $services = Service::where('active', true)->orderBy('name')->get();

        return view('admin.gifts.form', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'period_type' => 'required|in:monthly,quarterly,yearly',
            'banner' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'groups' => 'required|array|min:1',
            'groups.*.conditions' => 'required|array|min:1',
            'groups.*.conditions.*.service_id' => 'required|exists:services,id',
            'groups.*.conditions.*.min_count' => 'required|integer|min:1',
        ]);

        $gift = DB::transaction(function () use ($request) {
            $gift = Gift::create([
                'name' => $request->name,
                'description' => $request->description,
                'period_type' => $request->period_type,
                'is_active' => $request->boolean('is_active'),
            ]);

            foreach ($request->input('groups') as $i => $groupData) {
                $group = $gift->conditionGroups()->create(['sort_order' => $i]);
                foreach ($groupData['conditions'] as $condData) {
                    $group->conditions()->create([
                        'service_id' => $condData['service_id'],
                        'min_count' => $condData['min_count'],
                    ]);
                }
            }

            return $gift;
        });

        // Media upload is file I/O — must be outside the DB transaction
        if ($request->hasFile('banner')) {
            $gift->addMediaFromRequest('banner')
                ->toMediaCollection('gift_banner');
        }

        return redirect()->route('admin.gifts.index')->with('success', 'Gift created.');
    }

    public function edit(Gift $gift)
    {
        $gift->load('conditionGroups.conditions');
        $services = Service::where('active', true)->orderBy('name')->get();

        return view('admin.gifts.form', compact('gift', 'services'));
    }

    public function update(Request $request, Gift $gift)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'period_type' => 'required|in:monthly,quarterly,yearly',
            'banner' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'groups' => 'required|array|min:1',
            'groups.*.conditions' => 'required|array|min:1',
            'groups.*.conditions.*.service_id' => 'required|exists:services,id',
            'groups.*.conditions.*.min_count' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $gift) {
            $gift->update([
                'name' => $request->name,
                'description' => $request->description,
                'period_type' => $request->period_type,
                'is_active' => $request->boolean('is_active'),
            ]);

            // Wipe and recreate — simplest safe approach for nested conditions
            $gift->conditionGroups()->delete();

            foreach ($request->input('groups') as $i => $groupData) {
                $group = $gift->conditionGroups()->create(['sort_order' => $i]);
                foreach ($groupData['conditions'] as $condData) {
                    $group->conditions()->create([
                        'service_id' => $condData['service_id'],
                        'min_count' => $condData['min_count'],
                    ]);
                }
            }
        });

        // Media upload outside transaction — singleFile() auto-removes the old one
        if ($request->hasFile('banner')) {
            $gift->addMediaFromRequest('banner')
                ->toMediaCollection('gift_banner');
        }

        return redirect()->route('admin.gifts.index')->with('success', 'Gift updated.');
    }

    public function destroy(Gift $gift)
    {
        $gift->delete(); // cascades to groups + conditions via DB foreign keys

        return redirect()->route('admin.gifts.index')->with('success', 'Gift deleted.');
    }
}
