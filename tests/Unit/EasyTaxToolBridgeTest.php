<?php

namespace Tests\Unit;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\User;
use App\Services\EasyTaxToolBridge;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class EasyTaxToolBridgeTest extends TestCase
{
    protected function tearDown(): void
    {
        Auth::logout();
        parent::tearDown();
    }

    public function test_builds_valid_signed_url_for_paid_itr_balance_sheet(): void
    {
        $agent = new User;
        $agent->id = 77;
        $agent->email = 'agent_77@easytax.live';
        Auth::setUser($agent);

        $app = new Application;
        $app->id = 501;
        $app->payment_status = PaymentStatus::PAID;
        $app->status = ApplicationStatus::COMPLETED;
        $app->form_data = [
            'applicant_name' => 'Rajesh Sharma',
            'pan_number' => 'ABCDE1234F',
            'mobile' => '9876543210',
            'address' => 'Shop 12, Main Market, Jaipur',
            'business_turnover' => '1500000',
            'business_income' => '350000',
        ];

        $url = EasyTaxToolBridge::buildSignedUrl('balance-sheet', $app);

        $this->assertStringStartsWith('https://easytax.live/balance-sheet-generator?', $url);

        $parsed = parse_url($url);
        parse_str($parsed['query'], $params);

        $this->assertArrayHasKey('sig', $params);
        $sig = $params['sig'];
        unset($params['sig']);

        $this->assertEquals('balance-sheet', $params['tool']);
        $this->assertEquals('77', $params['agent_id']);
        $this->assertEquals('501', $params['app_id']);
        $this->assertEquals('Rajesh Sharma', $params['client_name']);
        $this->assertEquals('ABCDE1234F', $params['client_pan']);
        $this->assertEquals('9876543210', $params['client_phone']);
        $this->assertEquals('1500000', $params['turnover']);
        $this->assertEquals('350000', $params['net_income']);
        $this->assertEquals('Shop 12, Main Market, Jaipur', $params['address']);
        $this->assertEquals('1', $params['itr_paid']);

        // Verify cryptographic HMAC-SHA256 signature match with secret
        ksort($params);
        $canonical = http_build_query($params);
        $expectedSig = hash_hmac('sha256', $canonical, EasyTaxToolBridge::DEFAULT_SECRET);

        $this->assertEquals($expectedSig, $sig);
    }

    public function test_builds_valid_signed_url_for_project_report(): void
    {
        $agent = new User;
        $agent->id = 77;
        $agent->email = 'agent_77@easytax.live';
        Auth::setUser($agent);

        $app = new Application;
        $app->id = 501;
        $app->payment_status = PaymentStatus::PAID;
        $app->status = ApplicationStatus::COMPLETED;
        $app->form_data = [
            'applicant_name' => 'Rajesh Sharma',
            'pan_number' => 'ABCDE1234F',
        ];

        $url = EasyTaxToolBridge::buildSignedUrl('project-report', $app);

        $this->assertStringStartsWith('https://easytax.live/project-report-generator?', $url);

        $parsed = parse_url($url);
        parse_str($parsed['query'], $params);

        $this->assertArrayHasKey('sig', $params);
        $sig = $params['sig'];
        unset($params['sig']);

        $this->assertEquals('project-report', $params['tool']);
        $this->assertEquals('Rajesh Sharma', $params['client_name']);
        $this->assertEquals('ABCDE1234F', $params['client_pan']);
        $this->assertEquals('1', $params['itr_paid']);

        ksort($params);
        $canonical = http_build_query($params);
        $expectedSig = hash_hmac('sha256', $canonical, EasyTaxToolBridge::DEFAULT_SECRET);

        $this->assertEquals($expectedSig, $sig);
    }

    public function test_sets_itr_paid_to_zero_for_unpaid_application(): void
    {
        $app = new Application;
        $app->id = 502;
        $app->payment_status = PaymentStatus::PENDING;
        $app->status = ApplicationStatus::SUBMITTED;
        $app->form_data = [
            'applicant_name' => 'Suresh Gupta',
            'pan_number' => 'XYZPK9988A',
        ];

        $url = EasyTaxToolBridge::buildSignedUrl('balance-sheet', $app);

        $parsed = parse_url($url);
        parse_str($parsed['query'], $params);

        $this->assertEquals('0', $params['itr_paid']);
    }

    public function test_sets_itr_paid_to_zero_when_no_application_provided(): void
    {
        $url = EasyTaxToolBridge::buildSignedUrl('balance-sheet', null);

        $parsed = parse_url($url);
        parse_str($parsed['query'], $params);

        $this->assertEquals('0', $params['itr_paid']);
        $this->assertEquals('balance-sheet', $params['tool']);
    }
}
