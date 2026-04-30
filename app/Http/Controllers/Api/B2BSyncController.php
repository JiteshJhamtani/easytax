<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Models\User;

class B2BSyncController extends Controller
{
    public function export(Request $request)
    {
        // 1. THE SECURITY GUARD: Check if the caller has the exact secret key
        $token = $request->bearerToken();
        $secret = env('B2B_SYNC_SECRET');

        if (!$token || $token !== $secret) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized Access. Invalid B2B Token.'
            ], 401);
        }

        // 2. FETCH INSTRUCTIONS: Find out the last ID the B2B server already downloaded
        $lastId = $request->query('last_id', 0);

        // 3. THE DATA PACKER: Fetch applications newer than the last downloaded ID
        $applications = Application::with(['agent', 'service'])
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->limit(500) // Safety limit: Only send 500 at a time so your server doesn't crash
            ->get();

        // 4. SEND IT OUT
        return response()->json([
            'success' => true,
            'count'   => $applications->count(),
            'data'    => $applications
        ]);
    }

 public function exportAgents(Request $request)
    {
        $token = $request->bearerToken();
        $secret = env('B2B_SYNC_SECRET', 'EasyTax_Super_Secret_Key_2026!');

        if (!$token || $token !== $secret) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $lastId = $request->query('last_id', 0);

      // We added lowercase and Capitalized versions to be 100% safe!
        $agents = User::whereIn('role', ['AGENT', 'MARKETER', 'agent', 'marketer', 'Agent'])
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->limit(500)
            ->get();

        return response()->json([
            'success' => true,
            'count'   => $agents->count(),
            'data'    => $agents
        ]);
    }
}