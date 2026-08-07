<?php

use App\Models\Page;

it('can view an active page', function () {
    $page = Page::factory()->create([
        'title' => 'Public Policy',
        'slug' => 'public-policy',
        'is_active' => true,
    ]);

    $this->get(route('pages.show', $page->slug))
        ->assertSuccessful()
        ->assertSee('Public Policy');
});

it('returns 404 for an inactive page', function () {
    $page = Page::factory()->create([
        'title' => 'Hidden Page',
        'slug' => 'hidden-page',
        'is_active' => false,
    ]);

    $this->get(route('pages.show', $page->slug))
        ->assertNotFound();
});

it('returns 404 for a non-existent page', function () {
    $this->get(route('pages.show', 'does-not-exist'))
        ->assertNotFound();
});
