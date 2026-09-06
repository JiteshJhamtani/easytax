<?php

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\AgentMarginLog;
use App\Models\AgentMarginPayout;
use App\Models\Application;
use App\Models\Service;
use App\Models\SubAgentServicePricing;
use App\Models\User;
use App\Notifications\ParentMarginCreditedNotification;
use App\Notifications\ParentMarginSettledNotification;
use App\Services\AgentCodeService;
use App\Services\ParentMarginRefundService;
use App\Services\SessionResolver;
use App\Services\SubAgentPricingService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    // Create Parent Agent
    $this->parentAgent = User::factory()->create([
        'role' => 'AGENT',
        'is_active' => true,
        'agent_code' => 'AGT-100001',
    ]);

    // Create Sub-Agent
    $this->subAgentCode = AgentCodeService::generateSubAgentCode($this->parentAgent);
    $this->subAgent = User::factory()->create([
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => $this->parentAgent->id,
        'agent_code' => $this->subAgentCode,
    ]);

    // Create Another Independent Parent Agent
    $this->otherAgent = User::factory()->create([
        'role' => 'AGENT',
        'is_active' => true,
        'agent_code' => 'AGT-200001',
    ]);

    // Create Service: Base Price = 350, Base Commission = 50 -> Company Min = 300
    $this->service = Service::updateOrCreate(
        ['slug' => 'test-itr-service'],
        [
            'name' => 'Test ITR Service',
            'price' => 350.00,
            'commission_type' => 'flat',
            'commission_value' => 50.00,
            'active' => true,
        ]
    );
});

/*
|--------------------------------------------------------------------------
| 1. Code Generation & Hierarchy
|--------------------------------------------------------------------------
*/

it('generates hierarchical sub-agent code with parent code prefix', function () {
    expect($this->subAgentCode)->toBe('AGT-100001-01');

    $nextCode = AgentCodeService::generateSubAgentCode($this->parentAgent);
    expect($nextCode)->toBe('AGT-100001-02');
});

it('identifies sub-agents and parent agents correctly', function () {
    expect($this->parentAgent->isParentAgent())->toBeTrue();
    expect($this->parentAgent->isSubAgent())->toBeFalse();

    expect($this->subAgent->isParentAgent())->toBeFalse();
    expect($this->subAgent->isSubAgent())->toBeTrue();
    expect($this->subAgent->effectiveParentId())->toBe($this->parentAgent->id);
});

/*
|--------------------------------------------------------------------------
| 2. Pricing Resolution & Zero-Loss Company Invariant
|--------------------------------------------------------------------------
*/

it('resolves default service pricing when no custom pricing rule exists', function () {
    $pricing = SubAgentPricingService::resolveForSubAgent($this->service, $this->subAgent);

    expect($pricing['sub_agent_price'])->toBe(350.0);
    expect($pricing['sub_agent_commission'])->toBe(50.0);
    expect($pricing['sub_agent_payable'])->toBe(300.0);
    expect($pricing['company_minimum'])->toBe(300.0);
    expect($pricing['parent_margin'])->toBe(0.0);
});

it('resolves custom sub-agent pricing and calculates parent margin correctly', function () {
    // Parent sets custom price: 400, commission: 50 -> Sub pays: 350, Company min: 300 -> Parent margin: 50
    SubAgentServicePricing::create([
        'parent_agent_id' => $this->parentAgent->id,
        'service_id' => $this->service->id,
        'sub_agent_id' => null, // Agency-wide rule
        'price' => 400.00,
        'commission' => 50.00,
    ]);

    $pricing = SubAgentPricingService::resolveForSubAgent($this->service, $this->subAgent);

    expect($pricing['sub_agent_price'])->toBe(400.0);
    expect($pricing['sub_agent_commission'])->toBe(50.0);
    expect($pricing['sub_agent_payable'])->toBe(350.0);
    expect($pricing['company_minimum'])->toBe(300.0);
    expect($pricing['parent_margin'])->toBe(50.0);
});

it('enforces zero-loss company invariant and rejects underpriced rules', function () {
    // If Parent sets price = 320, commission = 50 -> Sub pays 270, but Company minimum is 300!
    expect(function () {
        SubAgentPricingService::assertValidPricing($this->service, 320.00, 50.00);
    })->toThrow(InvalidArgumentException::class);
});

/*
|--------------------------------------------------------------------------
| 3. Parent Margin Refund Execution & Idempotency
|--------------------------------------------------------------------------
*/

it('credits parent margin upon payment confirmation and logs transaction', function () {
    Notification::fake();

    // Create an application submitted by sub-agent with ₹50 parent margin
    $application = Application::factory()->create([
        'agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'service_id' => $this->service->id,
        'amount' => 400.00,
        'commission_amount' => 50.00,
        'sub_agent_amount' => 400.00,
        'sub_agent_commission' => 50.00,
        'company_minimum_amount' => 300.00,
        'parent_margin' => 50.00,
        'parent_margin_status' => 'PENDING',
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
    ]);

    $result = ParentMarginRefundService::processMarginRefund($application);

    expect($result)->not->toBeNull();
    expect($application->fresh()->parent_margin_status)->toBe('ACCRUED');
    expect($application->fresh()->parent_margin_refunded_at)->toBeNull();

    // Check margin log in database
    $log = AgentMarginLog::where('application_id', $application->id)->first();
    expect($log)->not->toBeNull();
    expect((float) $log->margin_amount)->toBe(50.0);
    expect($log->parent_agent_id)->toBe($this->parentAgent->id);
    expect($log->sub_agent_id)->toBe($this->subAgent->id);
    expect($log->status)->toBe('ACCRUED');

    // Notification sent to parent
    Notification::assertSentTo($this->parentAgent, ParentMarginCreditedNotification::class);
});

it('is strictly idempotent and does not credit margin twice', function () {
    Notification::fake();

    $application = Application::factory()->create([
        'agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'service_id' => $this->service->id,
        'amount' => 400.00,
        'commission_amount' => 50.00,
        'sub_agent_amount' => 400.00,
        'sub_agent_commission' => 50.00,
        'company_minimum_amount' => 300.00,
        'parent_margin' => 50.00,
        'parent_margin_status' => 'PENDING',
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
    ]);

    // Run once
    $firstRun = ParentMarginRefundService::processMarginRefund($application);
    expect($firstRun)->not->toBeNull();

    // Run twice
    $secondRun = ParentMarginRefundService::processMarginRefund($application);
    expect($secondRun->id)->toBe($firstRun->id);

    // Still only 1 log
    expect(AgentMarginLog::where('application_id', $application->id)->count())->toBe(1);
});

/*
|--------------------------------------------------------------------------
| 4. Permissions & Route Protection (ParentAgentOnlyMiddleware)
|--------------------------------------------------------------------------
*/

it('blocks sub-agents from team management, commissions, payouts, and gifts', function () {
    $restrictedRoutes = [
        'agent.sub-agents.index',
        'agent.sub-agents.create',
        'agent.sub-agents.bulk-create',
        'agent.team-pricing.index',
        'agent.margin-ledger.index',
        'agent.gifts',
        'agent.commissions',
        'agent.payouts',
    ];

    foreach ($restrictedRoutes as $route) {
        $this->actingAs($this->subAgent)
            ->get(route($route))
            ->assertForbidden();
    }
});

it('allows parent agents to access team management and financial screens', function () {
    $this->actingAs($this->parentAgent)
        ->get(route('agent.sub-agents.index'))
        ->assertSuccessful();

    $this->actingAs($this->parentAgent)
        ->get(route('agent.team-pricing.index'))
        ->assertSuccessful();

    $this->actingAs($this->parentAgent)
        ->get(route('agent.margin-ledger.index'))
        ->assertSuccessful();
});

/*
|--------------------------------------------------------------------------
| 5. Team Management Actions (Single, Bulk, Toggle, Password Reset)
|--------------------------------------------------------------------------
*/

it('allows parent agent to create a sub-agent via controller', function () {
    $this->actingAs($this->parentAgent)
        ->post(route('agent.sub-agents.store'), [
            'name' => 'Team Operator 1',
            'email' => 'operator1@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'mobile_number' => '9876543210',
        ])
        ->assertRedirect(route('agent.sub-agents.index'));

    $created = User::where('email', 'operator1@example.com')->first();
    expect($created)->not->toBeNull();
    expect($created->parent_id)->toBe($this->parentAgent->id);
    expect($created->agent_code)->toBe('AGT-100001-02');
});

it('allows parent agent to toggle sub-agent status', function () {
    expect($this->subAgent->is_active)->toBeTrue();

    $this->actingAs($this->parentAgent)
        ->patch(route('agent.sub-agents.toggle-status', $this->subAgent->id))
        ->assertRedirect();

    expect($this->subAgent->fresh()->is_active)->toBeFalse();
});

it('allows parent agent to reset sub-agent password', function () {
    $this->actingAs($this->parentAgent)
        ->post(route('agent.sub-agents.reset-password', $this->subAgent->id), [
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])
        ->assertRedirect();

    expect(Hash::check('NewPassword123!', $this->subAgent->fresh()->password))->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| 6. Data Isolation
|--------------------------------------------------------------------------
*/

it('scopes applications correctly: sub-agent sees only their own; parent sees all team applications', function () {
    // 1. Sub-agent application
    $subApp = Application::factory()->create([
        'agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'service_id' => $this->service->id,
    ]);

    // 2. Direct parent application
    $parentDirectApp = Application::factory()->create([
        'agent_id' => $this->parentAgent->id,
        'sub_agent_id' => null,
        'service_id' => $this->service->id,
    ]);

    // 3. Other agent application
    $otherApp = Application::factory()->create([
        'agent_id' => $this->otherAgent->id,
        'sub_agent_id' => null,
        'service_id' => $this->service->id,
    ]);

    // Sub-Agent datatable request
    $subResponse = $this->actingAs($this->subAgent)
        ->getJson(route('agent.applications.data'))
        ->assertSuccessful()
        ->json();

    $subAppIds = collect($subResponse['data'])->pluck('id')->all();
    expect($subAppIds)->toContain($subApp->id);
    expect($subAppIds)->not->toContain($parentDirectApp->id);
    expect($subAppIds)->not->toContain($otherApp->id);

    // Parent Agent datatable request (default view shows both)
    $parentResponse = $this->actingAs($this->parentAgent)
        ->getJson(route('agent.applications.data'))
        ->assertSuccessful()
        ->json();

    $parentAppIds = collect($parentResponse['data'])->pluck('id')->all();
    expect($parentAppIds)->toContain($subApp->id);
    expect($parentAppIds)->toContain($parentDirectApp->id);
    expect($parentAppIds)->not->toContain($otherApp->id);

    // Parent Agent filters by self only
    $selfOnlyResponse = $this->actingAs($this->parentAgent)
        ->getJson(route('agent.applications.data', ['sub_agent_id' => 'self']))
        ->assertSuccessful()
        ->json();

    $selfAppIds = collect($selfOnlyResponse['data'])->pluck('id')->all();
    expect($selfAppIds)->toContain($parentDirectApp->id);
    expect($selfAppIds)->not->toContain($subApp->id);
});

/*
|--------------------------------------------------------------------------
| 7. Bulk Onboarding & CSV Operations
|--------------------------------------------------------------------------
*/

it('allows parent agent to bulk onboard team members in a single submission', function () {
    $bulkData = [
        'members' => [
            [
                'name' => 'Operator Alpha',
                'email' => 'alpha@agency.com',
                'password' => 'secret123',
                'mobile_number' => '9988776655',
            ],
            [
                'name' => 'Operator Beta',
                'email' => 'beta@agency.com',
                'password' => 'secret123',
                'mobile_number' => '9988776644',
            ],
        ],
    ];

    $this->actingAs($this->parentAgent)
        ->post(route('agent.sub-agents.bulk-store'), $bulkData)
        ->assertRedirect(route('agent.sub-agents.index'));

    $alpha = User::where('email', 'alpha@agency.com')->first();
    $beta = User::where('email', 'beta@agency.com')->first();

    expect($alpha)->not->toBeNull();
    expect($alpha->parent_id)->toBe($this->parentAgent->id);
    expect($alpha->agent_code)->toBe('AGT-100001-02');

    expect($beta)->not->toBeNull();
    expect($beta->parent_id)->toBe($this->parentAgent->id);
    expect($beta->agent_code)->toBe('AGT-100001-03');
});

it('allows parent agent to download the CSV onboarding template', function () {
    $response = $this->actingAs($this->parentAgent)
        ->get(route('agent.sub-agents.download-template'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');

    expect($response->streamedContent())->toContain('name,email,password,mobile_number,whatsapp_no');
});

/*
|--------------------------------------------------------------------------
| 8. Parent Account Suspension Cascade
|--------------------------------------------------------------------------
*/

it('suspends sub-agent access if parent agency is suspended', function () {
    $this->parentAgent->update(['is_active' => false]);
    $this->subAgent->unsetRelation('parentAgent');

    // Sub-agent is active, but parent is suspended
    expect($this->subAgent->is_active)->toBeTrue();
    expect($this->subAgent->parentAgent->is_active)->toBeFalse();

    // Accessing any agent page should abort 403 Forbidden
    $this->actingAs($this->subAgent)
        ->get(route('agent.applications.index'))
        ->assertForbidden();
});

/*
|--------------------------------------------------------------------------
| 9. Master Admin (B2B Admin) Visibility
|--------------------------------------------------------------------------
*/

it('displays sub-agents and team margin stats in admin agent 360 view', function () {
    $admin = User::factory()->create([
        'role' => 'ADMIN',
        'is_active' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.agents.show', $this->parentAgent->id))
        ->assertSuccessful()
        ->assertSee('Agency Team (Sub-Agents)')
        ->assertSee($this->subAgent->name)
        ->assertSee($this->subAgent->agent_code);
});

it('displays team member badge in admin applications data table', function () {
    $admin = User::factory()->create([
        'role' => 'ADMIN',
        'is_active' => true,
    ]);

    $subApp = Application::factory()->create([
        'agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'service_id' => $this->service->id,
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
        'session_label' => SessionResolver::activeSessionLabel(),
    ]);

    $response = $this->actingAs($admin)
        ->getJson(route('admin.applications.data'))
        ->assertSuccessful()
        ->json();

    $row = collect($response['data'])->firstWhere('id', $subApp->id);
    expect($row)->not->toBeNull();
    expect($row['agent'])->toContain('Team: '.$this->subAgent->name);
});

it('allows parent agent and sub-agent to load the agent dashboard successfully without error', function () {
    $this->actingAs($this->parentAgent)
        ->get(route('agent.dashboard'))
        ->assertSuccessful();

    $this->actingAs($this->subAgent)
        ->get(route('agent.dashboard'))
        ->assertSuccessful();
});

/*
|--------------------------------------------------------------------------
| 10. Manual Margin Payouts by Company Admin (Replaces Razorpay Refunds)
|--------------------------------------------------------------------------
*/

it('allows parent agent to update payout bank and upi details', function () {
    $this->actingAs($this->parentAgent)
        ->post(route('agent.margin-ledger.update-bank'), [
            'bank_name' => 'State Bank of India',
            'bank_account_number' => '123456789012',
            'bank_ifsc' => 'SBIN0001234',
            'bank_account_holder' => 'Parent Agency Owner',
            'bank_upi_id' => 'agency@sbi',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $updated = $this->parentAgent->fresh();
    expect($updated->bank_name)->toBe('State Bank of India');
    expect($updated->bank_account_number)->toBe('123456789012');
    expect($updated->bank_ifsc)->toBe('SBIN0001234');
    expect($updated->bank_account_holder)->toBe('Parent Agency Owner');
    expect($updated->bank_upi_id)->toBe('agency@sbi');
});

it('allows admin to view accrued margins overview and agency breakdown', function () {
    $admin = User::factory()->create(['role' => 'ADMIN', 'is_active' => true]);

    // Create accrued margin log
    $app = Application::factory()->create([
        'agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'service_id' => $this->service->id,
        'parent_margin' => 75.00,
        'parent_margin_status' => 'ACCRUED',
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
    ]);

    AgentMarginLog::create([
        'parent_agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'application_id' => $app->id,
        'sub_agent_paid' => 375.00,
        'company_retained' => 300.00,
        'margin_amount' => 75.00,
        'status' => 'ACCRUED',
        'notes' => 'Accrued margin for test',
    ]);

    // Admin index view
    $this->actingAs($admin)
        ->get(route('admin.margin-payouts.index'))
        ->assertSuccessful()
        ->assertSee($this->parentAgent->name)
        ->assertSee('75.00');

    // Admin accrued details endpoint for settlement modal
    $response = $this->actingAs($admin)
        ->getJson(route('admin.margin-payouts.accrued', $this->parentAgent->id))
        ->assertSuccessful()
        ->json();

    expect($response['total_amount'])->toEqual(75.0);
    expect($response['items_count'])->toBe(1);
    expect($response['logs'][0]['application_id'])->toBe($app->id);
});

it('allows admin to execute manual margin payout with UTR and updates state to PAID', function () {
    Notification::fake();
    $admin = User::factory()->create(['role' => 'ADMIN', 'is_active' => true]);

    $app = Application::factory()->create([
        'agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'service_id' => $this->service->id,
        'parent_margin' => 100.00,
        'parent_margin_status' => 'ACCRUED',
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
    ]);

    $log = AgentMarginLog::create([
        'parent_agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'application_id' => $app->id,
        'sub_agent_paid' => 400.00,
        'company_retained' => 300.00,
        'margin_amount' => 100.00,
        'status' => 'ACCRUED',
        'notes' => 'Accrued margin for manual payout',
    ]);

    // Admin settles payout
    $response = $this->actingAs($admin)
        ->postJson(route('admin.margin-payouts.settle', $this->parentAgent->id), [
            'payment_method' => 'bank_transfer',
            'transaction_reference' => 'HDFCNEFT987654321',
            'payment_date' => now()->format('Y-m-d'),
            'notes' => 'Paid via corporate banking batch',
            'log_ids' => [$log->id],
        ])
        ->assertSuccessful()
        ->json();

    expect($response['success'])->toBeTrue();
    expect($response['payout']['amount'])->toBe('100.00');
    expect($response['payout']['payout_number'])->toContain('MPAY-');

    // Assert DB state transitions to PAID
    $freshLog = $log->fresh();
    expect($freshLog->status)->toBe('PAID');
    expect($freshLog->margin_payout_id)->toBe($response['payout']['id']);
    expect($freshLog->payout_reference)->toBe('HDFCNEFT987654321');

    $freshApp = $app->fresh();
    expect($freshApp->parent_margin_status)->toBe('PAID');
    expect($freshApp->parent_margin_refunded_at)->not->toBeNull();

    // Assert notification sent to parent agent
    Notification::assertSentTo($this->parentAgent, ParentMarginSettledNotification::class);
});

it('prevents double settlement when no eligible accrued margins remain', function () {
    $admin = User::factory()->create(['role' => 'ADMIN', 'is_active' => true]);

    // Parent agent has no accrued margins
    $this->actingAs($admin)
        ->postJson(route('admin.margin-payouts.settle', $this->parentAgent->id), [
            'payment_method' => 'bank_transfer',
            'transaction_reference' => 'UTR123456',
            'payment_date' => now()->format('Y-m-d'),
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('log_ids');
});

it('allows parent agent to query payout history via data endpoint', function () {
    $admin = User::factory()->create(['role' => 'ADMIN', 'is_active' => true]);

    $payout = AgentMarginPayout::create([
        'payout_number' => 'MPAY-20260906-0001',
        'parent_agent_id' => $this->parentAgent->id,
        'admin_id' => $admin->id,
        'amount' => 150.00,
        'payment_method' => 'upi',
        'transaction_reference' => 'UPI987654321',
        'payment_date' => now()->format('Y-m-d'),
    ]);

    $response = $this->actingAs($this->parentAgent)
        ->getJson(route('agent.margin-ledger.payouts'))
        ->assertSuccessful()
        ->json();

    expect($response['data'])->toHaveCount(1);
    expect($response['data'][0]['voucher'])->toContain('MPAY-20260906-0001');
    expect($response['data'][0]['reference'])->toContain('UPI987654321');
});

it('voids accrued margin if the sub-agent application is cancelled by agent', function () {
    $app = Application::factory()->create([
        'agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'service_id' => $this->service->id,
        'parent_margin' => 60.00,
        'parent_margin_status' => 'ACCRUED',
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
    ]);

    $log = AgentMarginLog::create([
        'parent_agent_id' => $this->parentAgent->id,
        'sub_agent_id' => $this->subAgent->id,
        'application_id' => $app->id,
        'sub_agent_paid' => 360.00,
        'company_retained' => 300.00,
        'margin_amount' => 60.00,
        'status' => 'ACCRUED',
        'notes' => 'Accrued margin before cancellation',
    ]);

    // Parent agent cancels application
    $this->actingAs($this->parentAgent)
        ->patch(route('agent.applications.cancel', $app->id))
        ->assertRedirect();

    expect($app->fresh()->status)->toBe(ApplicationStatus::CANCELLED);
    expect($app->fresh()->parent_margin_status)->toBe('CANCELLED');
    expect($log->fresh()->status)->toBe('CANCELLED');
});
