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
     * @param  string  $toolSlug  'balance-sheet' or 'project-report'
     * @param  Application|null  $application  Optional associated ITR application
     * @return string Signed redirect URL
     */
    public static function buildSignedUrl(string $toolSlug, ?Application $application = null): string
    {
        $secret = config('services.easytax.external_secret') ?: env('EASYTAX_EXTERNAL_SECRET', self::DEFAULT_SECRET);
        $agent = Auth::user();

        $params = [
            'tool' => $toolSlug,
            'agent_id' => $agent ? (string) $agent->id : '0',
            'agent_email' => $agent ? (string) $agent->email : '',
            'timestamp' => time(),
            'expires' => time() + 7200, // Valid for 2 hours
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

            // Check all common variations of turnover field in form_data
            $turnoverKeys = ['business_turnover', 'turnover', 'gross_turnover', 'gross_receipts', 'total_turnover', 'sales'];
            $turnover = 0.0;
            foreach ($turnoverKeys as $tk) {
                if (!empty($formData[$tk])) {
                    $turnover = (float) preg_replace('/[^0-9.]/', '', (string) $formData[$tk]);
                    if ($turnover > 0) {
                        break;
                    }
                }
            }

            // Check all common variations of net profit / business income in form_data
            $incomeKeys = ['business_income', 'business_profession_income', 'net_profit', 'profit', 'taxable_income', 'income', 'gross_total_income', 'total_income'];
            $income = 0.0;
            foreach ($incomeKeys as $ik) {
                if (!empty($formData[$ik])) {
                    $income = (float) preg_replace('/[^0-9.]/', '', (string) $formData[$ik]);
                    if ($income > 0) {
                        break;
                    }
                }
            }

            $params['app_id'] = (string) $application->id;
            $params['client_name'] = trim((string) ($formData['applicant_name'] ?? ''));
            $params['client_pan'] = strtoupper(trim((string) ($formData['pan_number'] ?? '')));
            $params['client_phone'] = trim((string) ($formData['mobile'] ?? ''));
            $params['address'] = trim((string) ($formData['address'] ?? ''));
            if ($turnover > 0) {
                $params['turnover'] = $turnover;
            }
            if ($income > 0) {
                $params['net_income'] = $income;
            }
            $params['itr_paid'] = $isPaid ? '1' : '0';

            // Find best financial/client document attached to the application (bank statement, computation, client docs)
            if ($application->relationLoaded('media') || $application->media()->exists()) {
                $mediaItems = $application->relationLoaded('media') ? $application->media : $application->media()->get();
                $financialDoc = $mediaItems
                    ->filter(function ($m) {
                        $ext = strtolower(pathinfo($m->file_name ?? '', PATHINFO_EXTENSION));
                        return in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'webp']);
                    })
                    ->sortByDesc(function ($m) {
                        $name = strtolower(($m->name ?? '') . ' ' . ($m->file_name ?? ''));
                        if (in_array($m->collection_name, ['bank_statement', 'computation_sheet', 'form_16'])) {
                            return 100;
                        }
                        if (str_contains($name, 'bank') || str_contains($name, 'statement') || str_contains($name, 'computation')) {
                            return 90;
                        }
                        if ($m->collection_name === 'client_documents') {
                            return 50;
                        }
                        return 10;
                    })
                    ->first();

                if ($financialDoc) {
                    $docExpires = time() + 7200;
                    $docSig = hash_hmac('sha256', "media_id={$financialDoc->id}&app_id={$application->id}&expires={$docExpires}", $secret);
                    $b2bBase = rtrim(config('app.url') ?: 'https://b2b.easytax.live', '/');
                    $params['doc_url'] = "{$b2bBase}/api/v1/bridge/document/{$financialDoc->id}?app_id={$application->id}&expires={$docExpires}&sig={$docSig}";
                    $params['doc_name'] = $financialDoc->file_name;
                }
            }
        } else {
            $params['itr_paid'] = '0';
        }

        ksort($params);
        $queryString = http_build_query($params);
        $sig = hash_hmac('sha256', $queryString, $secret);

        $path = $toolSlug === 'project-report' ? '/project-report-generator' : '/balance-sheet-generator';

        return self::BASE_URL.$path.'?'.$queryString.'&sig='.$sig;
    }
}
