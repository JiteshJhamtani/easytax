<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use App\Services\SessionResolver;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $sessions = SessionResolver::all();
        $currentSessionLabel = SessionResolver::activeSessionLabel($request->get('session'));

        $appQuery = fn () => Application::query()->inSession($currentSessionLabel);

        // ── KPI Cards (Aggregated in 2 fast queries) ──
        $appStats = $appQuery()
            ->selectRaw("
                COUNT(CASE WHEN status NOT IN ('DRAFT', 'CANCELLED') AND payment_status != 'FAILED' THEN 1 END) as total_applications,
                COUNT(CASE WHEN status = 'COMPLETED' THEN 1 END) as completed_applications,
                COUNT(CASE WHEN status NOT IN ('COMPLETED', 'DRAFT', 'CANCELLED') AND payment_status != 'FAILED' THEN 1 END) as pending_applications,
                COUNT(CASE WHEN status IN ('DRAFT', 'CANCELLED') OR payment_status = 'FAILED' THEN 1 END) as processed_applications,
                COALESCE(SUM(CASE WHEN status NOT IN ('DRAFT', 'CANCELLED', 'CANCELED', 'FAILED', 'draft', 'cancelled', 'canceled', 'failed') AND payment_status != 'FAILED' THEN (amount - commission_amount) END), 0) as total_revenue,
                COALESCE(SUM(CASE WHEN status NOT IN ('DRAFT', 'CANCELLED', 'CANCELED', 'FAILED', 'draft', 'cancelled', 'canceled', 'failed') AND payment_status != 'FAILED' THEN commission_amount END), 0) as total_commission
            ")
            ->first();

        $userCounts = User::query()
            ->where('is_active', true)
            ->whereIn('role', ['AGENT', 'agent', 'MARKETER', 'marketer'])
            ->selectRaw("
                COUNT(CASE WHEN role IN ('AGENT', 'agent') THEN 1 END) as total_agents,
                COUNT(CASE WHEN role IN ('MARKETER', 'marketer') THEN 1 END) as total_marketers
            ")
            ->first();

        $kpis = [
            'total_applications' => (int) ($appStats->total_applications ?? 0),
            'completed_applications' => (int) ($appStats->completed_applications ?? 0),
            'pending_applications' => (int) ($appStats->pending_applications ?? 0),
            'processed_applications' => (int) ($appStats->processed_applications ?? 0),
            'total_revenue' => (float) ($appStats->total_revenue ?? 0),
            'total_commission' => (float) ($appStats->total_commission ?? 0),
            'total_agents' => (int) ($userCounts->total_agents ?? 0),
            'total_marketers' => (int) ($userCounts->total_marketers ?? 0),
        ];

        // ── Monthly Charts (scoped to session) ──
        $monthlyData = $appQuery()
            ->select(
                DB::raw("DATE_FORMAT(submitted_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as applications_count'),
                DB::raw("SUM(CASE WHEN payment_status = 'PAID' THEN (amount - commission_amount) ELSE 0 END) as revenue")
            )
            ->whereNotIn('status', ['DRAFT', 'CANCELLED', 'CANCELED', 'FAILED', 'draft', 'cancelled', 'canceled', 'failed'])
            ->where('payment_status', '!=', 'FAILED')
            ->whereNotNull('submitted_at')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartLabels = $monthlyData->pluck('month')->toArray();
        $chartApplications = $monthlyData->pluck('applications_count')->toArray();
        $chartRevenue = $monthlyData->pluck('revenue')->map(fn ($v) => (float) $v)->toArray();

        // ── Top Agents (Dynamic Sequence) ──
        $totalAgentsCount = (int) ($kpis['total_agents'] ?? 0);
        if ($totalAgentsCount === 0) {
            $totalAgentsCount = User::query()->where('role', 'AGENT')->where('is_active', true)->count();
        }

        // Generate dynamic sequential options based on total agents count
        $potentialSteps = [10, 15, 20, 25, 30, 40, 50, 75, 100, 150, 200, 250, 500];
        $topAgentsOptions = [];

        foreach ($potentialSteps as $step) {
            if ($step < $totalAgentsCount) {
                $topAgentsOptions[(string) $step] = "Top {$step}";
            }
        }

        $topAgentsOptions['all'] = "All Agents ({$totalAgentsCount})";

        // Current requested limit
        $topAgentsLimit = (string) $request->get('top_agents_limit', '10');
        if (! array_key_exists($topAgentsLimit, $topAgentsOptions)) {
            $topAgentsLimit = array_key_first($topAgentsOptions) ?? '10';
        }

        $numericLimit = ($topAgentsLimit === 'all')
            ? max(1000, $totalAgentsCount)
            : (int) $topAgentsLimit;

        $topAgents = User::query()
            ->select(
                'users.id',
                'users.agent_code',
                'users.name',
                DB::raw('COUNT(applications.id) as applications_count'),
                DB::raw('COALESCE(SUM(applications.amount - applications.commission_amount), 0) as total_revenue'),
                DB::raw('COALESCE(SUM(applications.commission_amount), 0) as commission_earned')
            )
            ->join('applications', 'users.id', '=', 'applications.agent_id')
            ->where('applications.session_label', $currentSessionLabel)
            ->where('users.role', 'AGENT')
            ->whereNotIn('applications.status', ['DRAFT', 'CANCELLED', 'CANCELED', 'FAILED', 'draft', 'cancelled', 'canceled', 'failed'])
            ->where('applications.payment_status', '!=', 'FAILED')
            ->groupBy('users.id', 'users.agent_code', 'users.name')
            ->orderByDesc('total_revenue')
            ->limit($numericLimit)
            ->get();

        // AJAX response for live dynamic updating
        if ($request->ajax() && $request->has('top_agents_limit')) {
            $title = ($topAgentsLimit === 'all')
                ? "All Agents ({$totalAgentsCount})"
                : "Top {$numericLimit} Agents";

            return response()->json([
                'success' => true,
                'title' => $title,
                'limit' => $topAgentsLimit,
                'count' => $topAgents->count(),
                'html' => view('admin.dashboard_top_agents_rows', compact('topAgents'))->render(),
            ]);
        }

        // ── Top 10 Services (Fixed for Cross-Server databases) ──

        // 1. We query the LOCAL applications table first (keeping your filters intact)
        $topServicesStats = $appQuery()
            ->selectRaw('service_id, COUNT(id) as applications_count, COALESCE(SUM(amount - commission_amount), 0) as revenue')
            ->whereNotIn('status', ['DRAFT', 'CANCELLED', 'CANCELED', 'FAILED', 'draft', 'cancelled', 'canceled', 'failed'])
            ->where('payment_status', '!=', 'FAILED')
            ->groupBy('service_id')
            ->orderByDesc('applications_count')
            ->limit(10)
            ->get();

        // 2. We ask Laravel to silently fetch the matching Service names from the Master Server
        $topServicesStats->load('service:id,name');

        // 3. Now we format it exactly how your blade file expects it:
        $topServices = $topServicesStats->map(function ($stat) {
            return (object) [
                'id' => $stat->service_id,
                'name' => $stat->service ? $stat->service->name : 'Unknown Service',
                'applications_count' => $stat->applications_count,
                'revenue' => $stat->revenue,
            ];
        });

        // ── Recent 10 Applications ──
        $recentApplications = $appQuery()
            ->with(['agent:id,name', 'service:id,name'])
            ->whereNotIn('status', ['DRAFT', 'CANCELLED', 'CANCELED', 'FAILED', 'draft', 'cancelled', 'canceled', 'failed'])
            ->where('payment_status', '!=', 'FAILED')
            ->latest('submitted_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'sessions',
            'currentSessionLabel',
            'kpis',
            'chartLabels',
            'chartApplications',
            'chartRevenue',
            'topAgents',
            'topAgentsLimit',
            'topAgentsOptions',
            'totalAgentsCount',
            'topServices',
            'recentApplications'
        ));
    }

    public function switchServer($target)
    {
        // 1. Define your destination URLs
        $destinations = [
            'upwest' => 'https://upwest.easytax.live',
            'b2b' => 'https://b2b.easytax.live',
            'marketing' => 'https://marketing.easytax.live',
            'uat' => 'https://uat.easytax.live',
        ];

        if (! array_key_exists($target, $destinations)) {
            abort(404, 'Server destination not found.');
        }

        // 2. Create a payload with the user's email and a 60-second expiration timestamp
        $payload = json_encode([
            'email' => auth()->user()->email,
            'expires_at' => now()->addSeconds(60)->timestamp,
        ]);

        // 3. Encrypt the payload using the secure config value
        $encrypter = new Encrypter(config('services.cross_server.secret'), config('app.cipher'));
        $token = $encrypter->encryptString($payload);

        // 4. Redirect them to the auto-login route on the other server
        return redirect()->away($destinations[$target].'/auto-login?token='.urlencode($token));
    }
}
