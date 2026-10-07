<?php

use App\Models\Gift;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'ADMIN', 'is_active' => true]);
    $this->service = Service::firstOrCreate(
        ['slug' => 'test-gift-service'],
        ['name' => 'Gift Service', 'price' => 500, 'active' => true]
    );
});

it('allows admin to update gift banner image and replaces old media', function () {
    Storage::fake('public');

    $gift = Gift::create([
        'name' => 'Test Gift Box',
        'description' => 'Test Gift Description',
        'period_type' => 'monthly',
        'is_active' => true,
    ]);

    $group = $gift->conditionGroups()->create(['sort_order' => 0]);
    $group->conditions()->create([
        'service_id' => $this->service->id,
        'min_count' => 5,
    ]);

    // First upload
    $initialFile = UploadedFile::fake()->image('first_banner.png', 300, 300);

    $response = $this->actingAs($this->admin)->put(route('admin.gifts.update', $gift), [
        'name' => 'Test Gift Box Updated',
        'description' => 'Test Gift Description Updated',
        'period_type' => 'monthly',
        'is_active' => 1,
        'banner' => $initialFile,
        'groups' => [
            [
                'conditions' => [
                    [
                        'service_id' => $this->service->id,
                        'min_count' => 5,
                    ],
                ],
            ],
        ],
    ]);

    $response->assertRedirect(route('admin.gifts.index'));
    $response->assertSessionHas('success', 'Gift updated.');

    $gift->refresh();
    expect($gift->hasMedia('gift_banner'))->toBeTrue();
    expect($gift->getFirstMedia('gift_banner')->file_name)->toBe('first_banner.png');

    // Replace with second upload
    $replacementFile = UploadedFile::fake()->image('replacement_banner.png', 400, 400);

    $response2 = $this->actingAs($this->admin)->put(route('admin.gifts.update', $gift), [
        'name' => 'Test Gift Box Updated 2',
        'description' => 'Test Gift Description Updated',
        'period_type' => 'monthly',
        'is_active' => 1,
        'banner' => $replacementFile,
        'groups' => [
            [
                'conditions' => [
                    [
                        'service_id' => $this->service->id,
                        'min_count' => 5,
                    ],
                ],
            ],
        ],
    ]);

    $response2->assertRedirect(route('admin.gifts.index'));
    $gift->refresh();

    // Ensure singleFile() kept only the latest one
    expect($gift->media()->count())->toBe(1);
    expect($gift->getFirstMedia('gift_banner')->file_name)->toBe('replacement_banner.png');
});

it('serves storage media directly via StorageMediaController when requested', function () {
    $testDir = storage_path('app/public/test_serve');
    if (! file_exists($testDir)) {
        mkdir($testDir, 0755, true);
    }
    $testFile = $testDir.'/sample.png';
    $im = imagecreatetruecolor(20, 20);
    imagepng($im, $testFile);
    imagedestroy($im);

    $response = $this->get('/storage/test_serve/sample.png');

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'image/png');
    $response->assertHeader('Cache-Control', 'immutable, max-age=31536000, public');

    // Clean up
    @unlink($testFile);
    @rmdir($testDir);
});

it('aborts with 404 for missing files or directory traversal attempts', function () {
    $this->get('/storage/non_existent_file.png')->assertNotFound();
    $this->get('/storage/../etc/passwd')->assertNotFound();
});
