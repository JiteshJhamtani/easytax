<?php

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Service;
use App\Models\SubAgentServicePricing;
use App\Models\User;
use App\Services\AgentDashboardService;
use App\Services\RazorpayService;

beforeEach(function () {
    // Create Parent Agent 1
    $this->parentAgent = User::factory()->create([
        'role' => 'AGENT',
        'is_active' => true,
        'agent_code' => 'AGT-100001',
    ]);

    // Create Sub-Agent under Parent 1
    $this->subAgent = User::factory()->create([
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => $this->parentAgent->id,
        'agent_code' => 'AGT-100001-01',
    ]);

    // Create Second Sub-Agent under Parent 1
    $this->secondSubAgent = User::factory()->create([
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => $this->parentAgent->id,
        'agent_code' => 'AGT-100001-02',
    ]);

    // Create Independent Parent Agent 2 & their Sub-Agent
    $this->otherParent = User::factory()->create([
        'role' => 'AGENT',
        'is_active' => true,
        'agent_code' => 'AGT-200001',
    ]);
    $this->otherSubAgent = User::factory()->create([
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => $this->otherParent->id,
        'agent_code' => 'AGT-200001-01',
    ]);

    // Create Service with base price 4321.00
    $this->service = Service::updateOrCreate(
        ['slug' => 'itr-filing'],
        [
            'name' => 'AAA ITR Filing Special',
            'price' => 4321.00,
            'commission_type' => 'flat',
            'commission_value' => 500.00,
            'active' => true,
            'sort_order' => -999,
        ]
    );

    // Parent Agent 1 updates the service price specifically for Sub-Agent 1 to 5678.00
    SubAgentServicePricing::updateOrCreate(
        [
            'parent_agent_id' => $this->parentAgent->id,
            'sub_agent_id' => $this->subAgent->id,
            'service_id' => $this->service->id,
        ],
        [
            'price' => 5678.00,
            'commission' => 300.00,
        ]
    );
});

it('displays the parent customized price on the service catalog /services for subagent', function () {
    // Sub-agent views the catalog
    $subResponse = $this->actingAs($this->subAgent)->get(route('services.index'));
    $subResponse->assertSuccessful();

    $subHtml = $subResponse->getContent();
    expect($subHtml)->toContain('5,678')
        ->and($subHtml)->not->toContain('4,321');

    // Direct parent agent views the catalog and sees standard admin base price
    $parentResponse = $this->actingAs($this->parentAgent)->get(route('services.index'));
    $parentResponse->assertSuccessful();

    $parentHtml = $parentResponse->getContent();
    expect($parentHtml)->toContain('4,321')
        ->and($parentHtml)->not->toContain('5,678');
});

it('displays the parent customized price on the service details page /services/{slug} for subagent', function () {
    // Sub-agent views service show page
    $subResponse = $this->actingAs($this->subAgent)->get(route('services.show', $this->service->slug));
    $subResponse->assertSuccessful();

    $subHtml = $subResponse->getContent();
    expect($subHtml)->toContain('5,678')
        ->and($subHtml)->not->toContain('4,321');

    // Direct parent agent views service show page and sees standard admin base price
    $parentResponse = $this->actingAs($this->parentAgent)->get(route('services.show', $this->service->slug));
    $parentResponse->assertSuccessful();

    $parentHtml = $parentResponse->getContent();
    expect($parentHtml)->toContain('4,321')
        ->and($parentHtml)->not->toContain('5,678');
});

it('displays agency-wide pricing rules to all subagents when sub_agent_id is null', function () {
    // Create another service with agency-wide rule for Parent 1
    $service2 = Service::create([
        'name' => 'Agency Wide Test Service',
        'slug' => 'agency-wide-test',
        'price' => 2000.00,
        'commission_type' => 'flat',
        'commission_value' => 200.00,
        'active' => true,
        'sort_order' => -998,
    ]);

    // Parent 1 sets agency-wide price = 2500 (sub_agent_id = null)
    SubAgentServicePricing::create([
        'parent_agent_id' => $this->parentAgent->id,
        'sub_agent_id' => null,
        'service_id' => $service2->id,
        'price' => 2500.00,
        'commission' => 150.00,
    ]);

    // Both subagents of Parent 1 should see 2,500
    $sub1Response = $this->actingAs($this->subAgent)->get(route('services.index'));
    expect($sub1Response->getContent())->toContain('2,500');

    $sub2Response = $this->actingAs($this->secondSubAgent)->get(route('services.index'));
    expect($sub2Response->getContent())->toContain('2,500');

    // Sub-agent of Parent 2 should see admin price 2,000, NOT 2,500
    $otherSubResponse = $this->actingAs($this->otherSubAgent)->get(route('services.index'));
    expect($otherSubResponse->getContent())->toContain('2,000')
        ->and($otherSubResponse->getContent())->not->toContain('2,500');
});

it('displays the subagent amount in the dashboard recent activity table', function () {
    $application = Application::create([
        'agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'service_id' => $this->service->id,
        'form_data' => ['pan_number' => 'ABCDE1234F'],
        'amount' => 4321.00,
        'commission_amount' => 500.00,
        'sub_agent_amount' => 5678.00,
        'sub_agent_commission' => 300.00,
        'company_minimum_amount' => 3821.00,
        'parent_margin' => 1557.00,
        'parent_margin_status' => 'PENDING',
        'status' => ApplicationStatus::DRAFT,
        'payment_status' => PaymentStatus::PENDING,
    ]);

    // Sub-agent dashboard
    $subResponse = $this->actingAs($this->subAgent)->get(route('agent.dashboard'));
    $subResponse->assertSuccessful();

    $subHtml = $subResponse->getContent();
    expect($subHtml)->toContain('5,678')
        ->and($subHtml)->not->toContain('4,321');

    // Parent agent dashboard
    $parentResponse = $this->actingAs($this->parentAgent)->get(route('agent.dashboard'));
    $parentResponse->assertSuccessful();

    $parentHtml = $parentResponse->getContent();
    expect($parentHtml)->toContain('4,321');
});

it('displays the subagent amount in the applications data table and show page', function () {
    $application = Application::create([
        'agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'service_id' => $this->service->id,
        'form_data' => ['pan_number' => 'ABCDE1234F'],
        'amount' => 4321.00,
        'commission_amount' => 500.00,
        'sub_agent_amount' => 5678.00,
        'sub_agent_commission' => 300.00,
        'company_minimum_amount' => 3821.00,
        'parent_margin' => 1557.00,
        'parent_margin_status' => 'PENDING',
        'status' => ApplicationStatus::DRAFT,
        'payment_status' => PaymentStatus::PENDING,
    ]);

    // 1. Check DataTables for sub-agent
    $subDataResponse = $this->actingAs($this->subAgent)->get(route('agent.applications.data', ['type' => 'itr-filing']));
    $subDataResponse->assertSuccessful();

    $subDataArr = json_decode($subDataResponse->getContent(), true);
    $subTableAmount = $subDataArr['data'][0]['amount'] ?? null;
    expect($subTableAmount)->toContain('5,678')
        ->and($subTableAmount)->not->toContain('4,321');

    // 2. Check DataTables for parent agent
    $parentDataResponse = $this->actingAs($this->parentAgent)->get(route('agent.applications.data', ['type' => 'itr-filing']));
    $parentDataResponse->assertSuccessful();

    $parentDataArr = json_decode($parentDataResponse->getContent(), true);
    $parentTableAmount = $parentDataArr['data'][0]['amount'] ?? null;
    expect($parentTableAmount)->toContain('4,321')
        ->and($parentTableAmount)->not->toContain('5,678');

    // 3. Check Show page for sub-agent
    $subShowResponse = $this->actingAs($this->subAgent)->get(route('agent.applications.show', $application->id));
    $subShowResponse->assertSuccessful();

    $subShowHtml = $subShowResponse->getContent();
    expect($subShowHtml)->toContain('5,678')
        ->and($subShowHtml)->not->toContain('4,321');

    // 4. Check Show page for parent agent
    $parentShowResponse = $this->actingAs($this->parentAgent)->get(route('agent.applications.show', $application->id));
    $parentShowResponse->assertSuccessful();

    $parentShowHtml = $parentShowResponse->getContent();
    expect($parentShowHtml)->toContain('4,321')
        ->and($parentShowHtml)->not->toContain('5,678');
});

it('exports the effective subagent amount in CSV export for subagent', function () {
    $application = Application::create([
        'agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'service_id' => $this->service->id,
        'form_data' => ['pan_number' => 'ABCDE1234F'],
        'amount' => 4321.00,
        'commission_amount' => 500.00,
        'sub_agent_amount' => 5678.00,
        'sub_agent_commission' => 300.00,
        'company_minimum_amount' => 3821.00,
        'parent_margin' => 1557.00,
        'parent_margin_status' => 'PENDING',
        'status' => ApplicationStatus::IN_PROGRESS,
        'payment_status' => PaymentStatus::PAID,
    ]);

    // Sub-agent exports CSV
    $subExportResponse = $this->actingAs($this->subAgent)->get(route('agent.applications.export', ['type' => 'itr-filing']));
    $subExportResponse->assertSuccessful();
    expect($subExportResponse->getContent())->toContain('5678')
        ->and($subExportResponse->getContent())->not->toContain('4321');

    // Parent agent exports CSV
    $parentExportResponse = $this->actingAs($this->parentAgent)->get(route('agent.applications.export', ['type' => 'itr-filing']));
    $parentExportResponse->assertSuccessful();
    expect($parentExportResponse->getContent())->toContain('4321');
});

it('calculates dashboard stats using subagent commission for subagents', function () {
    Application::create([
        'agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'service_id' => $this->service->id,
        'form_data' => ['pan_number' => 'ABCDE1234F'],
        'amount' => 4321.00,
        'commission_amount' => 500.00,
        'sub_agent_amount' => 5678.00,
        'sub_agent_commission' => 300.00,
        'company_minimum_amount' => 3821.00,
        'parent_margin' => 1557.00,
        'parent_margin_status' => 'PENDING',
        'status' => ApplicationStatus::COMPLETED,
        'payment_status' => PaymentStatus::PAID,
    ]);

    $dashboardService = app(AgentDashboardService::class);

    $subStats = $dashboardService->getStats($this->parentAgent->id, null, $this->subAgent->id);
    expect((float) $subStats->total_commission)->toEqual(300.00);

    $parentStats = $dashboardService->getStats($this->parentAgent->id, null, null);
    expect((float) $parentStats->total_commission)->toEqual(500.00);
});

it('correctly creates application with custom subagent pricing and charges subagent payable amount', function () {
    $mockRazorpay = Mockery::mock(RazorpayService::class);
    $mockRazorpay->shouldReceive('createOrder')
        ->once()
        ->with(
            Mockery::type('string'),
            537800, // (5678 - 300) = 5378 * 100 paise
            Mockery::type('array')
        )
        ->andReturn([
            'success' => true,
            'order_id' => 'order_test_subagent_123',
            'amount' => 537800,
            'currency' => 'INR',
        ]);

    $this->app->instance(RazorpayService::class, $mockRazorpay);
    $this->service->pricingRules()->delete();

    $response = $this->actingAs($this->subAgent)->post(route('applications.store', $this->service->slug), [
        'itr_year_1' => '2026-27',
        'type_user' => 'user',
        'has_salary' => 'no',
        'has_capital_gains' => 'no',
        'income_over_250k' => 'no',
        'has_business' => 'no',
        'bank_account_type' => 'savings',
        'bank_account_number' => '1234567890',
        'ifsc_code' => 'SBIN0001234',
        'pan_number' => 'ABCDE1234F',
    ]);

    $response->assertSessionHas('razorpay_order');
    $order = session('razorpay_order');
    expect($order['order_id'])->toEqual('order_test_subagent_123');
    expect($order['amount'])->toEqual(537800);

    $application = Application::where('sub_agent_id', $this->subAgent->id)->latest()->first();
    expect($application)->not->toBeNull();
    expect((float) $application->amount)->toEqual(4321.00);
    expect((float) $application->sub_agent_amount)->toEqual(5678.00);
    expect((float) $application->sub_agent_commission)->toEqual(300.00);
    expect((float) $application->company_minimum_amount)->toEqual(3821.00);
    expect((float) $application->parent_margin)->toEqual(1557.00);

    // Verify effective amounts for sub-agent vs parent agent
    expect($application->getEffectiveAmount($this->subAgent))->toEqual(5678.00);
    expect($application->getEffectiveCommission($this->subAgent))->toEqual(300.00);

    expect($application->getEffectiveAmount($this->parentAgent))->toEqual(4321.00);
    expect($application->getEffectiveCommission($this->parentAgent))->toEqual(500.00);
});
