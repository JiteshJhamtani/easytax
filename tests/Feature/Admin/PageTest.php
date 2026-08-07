<?php

use App\Models\User;
use App\Models\Page;

beforeEach(function () {
    // Assuming 'role' or simple boolean for admin state based on application standard
    // Or we just use a generic user and rely on the actingAs to bypass guard for tests
    $this->admin = User::factory()->create(['role' => 'ADMIN']);
});

it('can view pages index', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.pages.index'))
        ->assertSuccessful()
        ->assertSee('Pages Management');
});

it('can view create page form', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.pages.create'))
        ->assertSuccessful()
        ->assertSee('Create New Page');
});

it('can store a new page and auto-generate slug', function () {
    $data = [
        'title' => 'Test Page Title',
        'content' => '<p>Content</p>',
        'is_active' => '1',
    ];

    $this->actingAs($this->admin)
        ->post(route('admin.pages.store'), $data)
        ->assertRedirect(route('admin.pages.index'));

    $this->assertDatabaseHas('pages', [
        'title' => 'Test Page Title',
        'slug' => 'test-page-title',
        'is_active' => 1,
    ]);
});

it('can edit a page', function () {
    $page = Page::factory()->create();

    $this->actingAs($this->admin)
        ->get(route('admin.pages.edit', $page))
        ->assertSuccessful()
        ->assertSee(htmlentities($page->title, ENT_QUOTES, 'UTF-8'), false) // Might be escaped in input
        ->assertSee('Edit Page');
});

it('can update a page', function () {
    $page = Page::factory()->create(['title' => 'Old Title']);

    $this->actingAs($this->admin)
        ->put(route('admin.pages.update', $page), [
            'title' => 'New Title',
            'slug' => 'new-slug',
            'content' => 'Updated content',
            'is_active' => '0',
        ])
        ->assertRedirect(route('admin.pages.index'));

    $this->assertDatabaseHas('pages', [
        'id' => $page->id,
        'title' => 'New Title',
        'slug' => 'new-slug',
        'is_active' => 0,
    ]);
});

it('can delete a page', function () {
    $page = Page::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.pages.destroy', $page))
        ->assertRedirect(route('admin.pages.index'));

    $this->assertDatabaseMissing('pages', ['id' => $page->id]);
});

it('can toggle page status', function () {
    $page = Page::factory()->create(['is_active' => true]);

    $this->actingAs($this->admin)
        ->patch(route('admin.pages.toggle', $page))
        ->assertRedirect();

    expect($page->fresh()->is_active)->toBeFalse();
});
