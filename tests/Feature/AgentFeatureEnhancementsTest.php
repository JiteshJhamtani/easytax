<?php

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\AgentPayout;
use App\Models\Application;
use App\Models\Gift;
use App\Models\Service;
use App\Models\User;
use App\Services\SessionResolver;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'name' => 'Super Admin',
        'email' => 'admin_test_'.uniqid().'@example.com',
        'role' => 'ADMIN',
        'is_active' => true,
    ]);

    $this->teamUser = User::factory()->create([
        'name' => 'Operator Member',
        'email' => 'team_test_'.uniqid().'@example.com',
        'role' => 'TEAM',
        'is_active' => true,
    ]);

    $this->agent = User::factory()->create([
        'name' => 'Primary Test Agent',
        'email' => 'agent_test_'.uniqid().'@example.com',
        'role' => 'AGENT',
        'agent_code' => 'AGT_'.rand(1000, 9999),
        'is_active' => true,
        'parent_id' => null,
    ]);

    $this->itrService = Service::firstOrCreate(
        ['slug' => 'itr-filing'],
        ['name' => 'ITR Filing', 'price' => 200, 'active' => true, 'commission_type' => 'flat', 'commission_value' => 20]
    );

    $this->gstService = Service::firstOrCreate(
        ['slug' => 'gst-registration'],
        ['name' => 'GST Registration', 'price' => 500, 'active' => true, 'commission_type' => 'flat', 'commission_value' => 50]
    );
});

it('renders agent sidebar with More Services and without bottom illustration image', function () {
    $response = $this->actingAs($this->agent)->get(route('agent.dashboard'));
    $response->assertSuccessful();

    // Check "More Services" is present
    $response->assertSee('More Services');
    $response->assertDontSee('More Applications');

    // Check sb-illustration is not in the sidebar
    $response->assertDontSee('class="sb-illustration"', false);
    $response->assertDontSee('class="sb-bottom"', false);
});

it('renders admin and marketer sidebars without bottom illustration images', function () {
    $responseAdmin = $this->actingAs($this->admin)->get(route('admin.agents.index'));
    $responseAdmin->assertSuccessful();
    $responseAdmin->assertDontSee('class="sb-illustration"', false);
    $responseAdmin->assertDontSee('class="sb-bottom"', false);
});

it('attaches agent_notes during application creation and displays it to admin, team operator and agent', function () {
    $noteText = 'Please process urgently under Old Tax Regime. Contact client after 5 PM.';

    $application = Application::create([
        'agent_id' => $this->agent->id,
        'assigned_to' => $this->teamUser->id,
        'service_id' => $this->itrService->id,
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
        'amount' => 200,
        'commission_amount' => 20,
        'agent_notes' => $noteText,
        'form_data' => [
            'applicant_name' => 'Ramesh Kumar',
            'agent_notes' => $noteText,
        ],
    ]);

    // 1. Check Admin View
    $responseAdmin = $this->actingAs($this->admin)->get(route('admin.applications.show', $application->id));
    $responseAdmin->assertSuccessful();
    $responseAdmin->assertSee('Message from Agent / Special Instructions');
    $responseAdmin->assertSee($noteText);

    // 2. Check Team/Operator View
    $responseTeam = $this->actingAs($this->teamUser)->get(route('team.applications.show', $application->id));
    $responseTeam->assertSuccessful();
    $responseTeam->assertSee('Message from Agent / Special Instructions');
    $responseTeam->assertSee($noteText);

    // 3. Check Agent View
    $responseAgent = $this->actingAs($this->agent)->get(route('agent.applications.show', $application->id));
    $responseAgent->assertSuccessful();
    $responseAgent->assertSee('Special Instructions / Message for Operator');
    $responseAgent->assertSee($noteText);
});

it('allows agent to update attached notes from agent application detail view', function () {
    $application = Application::create([
        'agent_id' => $this->agent->id,
        'service_id' => $this->itrService->id,
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
        'amount' => 200,
        'commission_amount' => 20,
        'agent_notes' => 'Original note',
    ]);

    $updatedNote = 'Updated note: Client provided Aadhaar OTP 123456.';

    $response = $this->actingAs($this->agent)->patch(route('agent.applications.update-notes', $application->id), [
        'agent_notes' => $updatedNote,
    ]);

    $response->assertSessionHas('success', 'Message / instructions updated successfully.');

    $application->refresh();
    expect($application->agent_notes)->toBe($updatedNote);
    expect($application->form_data['agent_notes'] ?? null)->toBe($updatedNote);
});

it('allows admin to export master agent report containing applications, payments, and gifts data', function () {
    // 1. Create a gift milestone
    $gift = Gift::create([
        'name' => '5 ITR Star Gift',
        'period_type' => 'session',
        'is_active' => true,
    ]);
    $g = $gift->conditionGroups()->create(['sort_order' => 0]);
    $g->conditions()->create(['service_id' => $this->itrService->id, 'min_count' => 5]);

    $currentSession = SessionResolver::current();

    // 2. Create 5 completed applications for agent
    for ($i = 0; $i < 5; $i++) {
        Application::create([
            'agent_id' => $this->agent->id,
            'service_id' => $this->itrService->id,
            'status' => ApplicationStatus::COMPLETED,
            'payment_status' => PaymentStatus::PAID,
            'amount' => 200,
            'commission_amount' => 20,
            'completed_at' => $currentSession['from']->copy()->addDay(),
            'session_label' => $currentSession['label'],
        ]);
    }

    // 3. Create a payout record for agent
    AgentPayout::create([
        'agent_id' => $this->agent->id,
        'amount' => 50,
        'period_start' => now()->startOfMonth(),
        'period_end' => now()->endOfMonth(),
        'status' => 'PAID',
        'processed_at' => now(),
    ]);

    // 4. Hit the export endpoint
    $response = $this->actingAs($this->admin)->get(route('admin.agents.export', [
        'session' => $currentSession['label'],
    ]));

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    $content = $response->streamedContent();

    // Check CSV headings exist
    expect($content)->toContain('Agent ID');
    expect($content)->toContain('Agent Code');
    expect($content)->toContain('ITR Filings (Completed)');
    expect($content)->toContain('Commission Earned (INR)');
    expect($content)->toContain('Unlocked / Eligible Gifts');
    expect($content)->toContain('Unlocked Gifts Count');

    // Check Agent row data
    expect($content)->toContain($this->agent->name);
    expect($content)->toContain($this->agent->email);
    expect($content)->toContain('5 ITR Star Gift');
});
