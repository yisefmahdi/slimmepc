<?php

use App\Models\ContentBlock;

it('renders the over-ons page with all sections', function () {
    $response = $this->get('/over-ons');

    $response->assertOk();
    $response->assertSee('Over Slimme-PC', false);
    $response->assertSee('Wij lossen problemen op.', false);
    $response->assertSee('Meet Mo', false);
    $response->assertSee('Binnen in onze werkplaats', false);
    $response->assertSee('Onze reis', false);
    $response->assertSee('Wat klanten zeggen', false);
    $response->assertSee('Duidelijke diagnose', false);
    $response->assertSee('about-werkplaats-1.jpg', false);
    $response->assertSee('overons.css', false);
    $response->assertSee('data-werkplaats-track', false);
});

it('seeds the overons content blocks for every section', function () {
    $sections = ContentBlock::where('page', 'overons')->distinct('section')->orderBy('section')->pluck('section')->all();

    expect($sections)->toBe(['hero', 'meet', 'reis', 'reviews', 'trust', 'werkplaats', 'why']);
});

it('seeds the overons hero with the Google rating fields', function () {
    $rating = ContentBlock::where('page', 'overons')->where('section', 'hero')->where('block_key', 'rating_value')->first();

    expect($rating)->not->toBeNull();
    expect($rating->value)->toBe('4.9');
});