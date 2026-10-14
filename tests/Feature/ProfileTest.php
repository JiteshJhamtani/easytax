<?php

use App\Enums\NotificationPreference;
use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertSoftDeleted($user);
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

test('profile contact details and notification preference can be updated', function () {
    $user = User::factory()->create(['role' => 'ADMIN']);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Admin Boss',
            'email' => 'boss@example.com',
            'mobile_number' => '9876543210',
            'whatsapp_no' => '9876543210',
            'address' => '123 Business Street, Financial District',
            'notification_preference' => 'SILENT',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    expect($user->name)->toBe('Admin Boss')
        ->and($user->email)->toBe('boss@example.com')
        ->and($user->mobile_number)->toBe('9876543210')
        ->and($user->whatsapp_no)->toBe('9876543210')
        ->and($user->address)->toBe('123 Business Street, Financial District')
        ->and($user->notification_preference)->toBe(NotificationPreference::SILENT);
});

test('profile page renders properly for admin, agent, and marketer users', function (string $role) {
    $user = User::factory()->create([
        'role' => $role,
        'agent_code' => $role === 'AGENT' ? 'ET-AGENT-01' : null,
        'is_active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk()
        ->assertSee('Account Profile &amp; Settings', false)
        ->assertSee($user->name);
})->with(['ADMIN', 'AGENT', 'MARKETER']);

test('profile page displays b2b partner certificate generator with customization options', function () {
    $user = User::factory()->create([
        'name' => 'Raj Narayan Kumar',
        'role' => 'AGENT',
        'agent_code' => 'AGT-RNK-01',
        'is_active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk()
        ->assertSee('B2B Partner Certificate Generator')
        ->assertSee('certCanvas')
        ->assertSee('certPrefix')
        ->assertSee('certSuffix')
        ->assertSee('Raj Narayan Kumar')
        ->assertSee('Rahul Sharma')
        ->assertSee('b2b_partner_certificate_template.jpg')
        ->assertSee('Download Certificate (PNG Image)')
        ->assertSee('Download PDF')
        ->assertDontSee('id="certName"', false)
        ->assertDontSee('id="certDate"', false);
});
