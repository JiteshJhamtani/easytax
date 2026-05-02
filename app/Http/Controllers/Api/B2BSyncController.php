<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application; 
use App\Models\User;        
use Illuminate\Http\Request;


class B2BSyncController extends Controller 
{
    // 1. Export Agents
    public function exportAgents(Request $request) {
        $token = $request->bearerToken();
        $secret = env('B2B_SYNC_SECRET', 'EasyTax_Super_Secret_Key_2026!');
        
        if (!$token || $token !== $secret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $lastId = $request->query('last_id', 0);
        
        // Case-insensitive role check
        $agents = User::whereIn('role', ['AGENT', 'agent', 'MARKETER', 'marketer'])
                      ->where('id', '>', $lastId)
                      ->get()
                      ->makeVisible(['password', 'agent_code']); // Ensure agent_code isn't hidden!

        return response()->json([
            'success' => true,
            'data' => $agents
        ]);
    }
    
    
    // 2. Export Applications
    public function export(Request $request) {
        $token = $request->bearerToken();
        $secret = env('B2B_SYNC_SECRET', 'EasyTax_Super_Secret_Key_2026!');
        
        if (!$token || $token !== $secret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $lastId = $request->query('last_id', 0);

        try {
            $applications = Application::where('id', '>', $lastId)
                ->orderBy('id', 'asc')
                // ->limit(100) 🚨 LIMIT REMOVED! The server will now send all data for a perfect 1:1 mirror.
                ->get()
                ->map(function($app) {
                    $data = $app->toArray();
                    // Fixes the 500 error!
                    $data['form_data'] = is_string($app->form_data) ? json_decode($app->form_data, true) : $app->form_data;
                    return $data;
                });

            return response()->json([
                'success' => true,
                'data' => $applications
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}