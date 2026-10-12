<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class EasyTaxToolBridge
{
    public const BASE_URL = 'https://easytax.live';
    public const DEFAULT_SECRET = 'et_live_sec_89347519283741928347';

    /**
     * Generate HMAC signed URL for Balance Sheet or Project Report.
     *
     * @param string $toolSlug 'balance-sheet' or 'project-report'
     * @param Application|null $application Optional associated ITR application
     * @return string Signed redirect URL
     */
    public static function buildSignedUrl(string $toolSlug, ?Application $application = null): string
    {
        $secret = config('services.easytax.external_secret') ?: env('EASYTAX_EXTERNAL_SECRET', self::DEFAULT_SECRET);
        $agent = Auth::user();

        $params = [
            'tool'        => $toolSlug,
            'agent_id'    => $agent ? (string) $agent->id : '0',
            'agent_email' => $agent ? (string) $agent->email : '',
            'timestamp'   => time(),
            'expires'     => time() + 7200, // Valid for 2 hours
        ];

        if ($application) {
            $formData = is_string($application->form_data)
                ? (json_decode($application->form_data, true) ?: [])
                : ($application->form_data ?: []);

            $paymentStatus = $application->payment_status instanceof PaymentStatus
                ? $application->payment_status->value
                : (string) $application->payment_status;

            $appStatus = $application->status instanceof ApplicationStatus
                ? $application->status->value
                : (string) $application->status;

            $isPaid = in_array(strtoupper($paymentStatus), ['PAID', 'SUCCESS'], true)
                   || in_array(strtoupper($appStatus), ['COMPLETED', 'IN_PROGRESS', 'E_FILING', 'OTP_VERIFICATION'], true);

            $turnover = (float) preg_replace('/[^0-9.]/', '', (string) ($formData['business_turnover'] ?? 0));
            $income = (float) preg_replace('/[^0-9.]/', '', (string) ($formData['business_income'] ?? $formData['business_profession_income'] ?? 0));

            $params['app_id']       = (string) $application->id;
            $params['client_name']  = trim((string) ($formData['applicant_name'] ?? ''));
            $params['client_pan']   = strtoupper(trim((string) ($formData['pan_number'] ?? '')));
            $params['client_phone'] = trim((string) ($formData['mobile'] ?? ''));
            $params['address']      = trim((string) ($formData['address'] ?? ''));
            if ($turnover > 0) {
                $params['turnover'] = $turnover;
            }
            if ($income > 0) {
                $params['net_income'] = $income;
            }
            $params['itr_paid']     = $isPaid ? '1' : '0';
        } else {
            $params['itr_paid']     = '0';
        }

        ksort($params);
        $queryString = http_build_query($params);
        $sig = hash_hmac('sha256', $queryString, $secret);

        $path = $toolSlug === 'project-report' ? '/project-report-generator' : '/balance-sheet-generator';
        return self::BASE_URL . $path . '?' . $queryString . '&sig=' . $sig;
    }
}
