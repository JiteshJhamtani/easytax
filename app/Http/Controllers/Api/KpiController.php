<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function getKpis(Request $request)
    {
        $token = (string) $request->bearerToken();
        $secret = config('b2b.cross_server_secret', '');

        // Security Check using the secret in your config
        if (! $token || ! hash_equals($secret, $token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Exact same logic from your DashboardController so numbers match perfectly
        $kpis = [
            'total_applications' => Application::query()
                ->whereNotIn('status', ['DRAFT', 'CANCELLED'])->where('payment_status', '!=', 'FAILED')->count(),
            'completed_applications' => Application::query()
                ->where('status', 'COMPLETED')->count(),
            'pending_applications' => Application::query()
                ->whereNotIn('status', ['COMPLETED', 'DRAFT', 'CANCELLED'])->where('payment_status', '!=', 'FAILED')->count(),
            'processed_applications' => Application::query()->where(function ($query) {
                $query->whereIn('status', ['DRAFT', 'CANCELLED'])->orWhere('payment_status', 'FAILED');
            })->count(),
            'total_revenue' => Application::query()->whereNotIn('status', ['DRAFT', 'CANCELLED', 'CANCELED', 'FAILED', 'draft', 'cancelled', 'canceled', 'failed'])->where('payment_status', '!=', 'FAILED')->sum('amount'),
            'total_commission' => Application::query()->whereNotIn('status', ['DRAFT', 'CANCELLED', 'CANCELED', 'FAILED', 'draft', 'cancelled', 'canceled', 'failed'])->where('payment_status', '!=', 'FAILED')->sum('commission_amount'),
            'total_agents' => User::query()->where('role', 'AGENT')->where('is_active', true)->count(),
            'total_marketers' => User::query()->whereIn('role', ['marketer', 'MARKETER'])->where('is_active', true)->count(),
        ];

        return response()->json($kpis);
    }
}
