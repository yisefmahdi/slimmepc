<?php

use App\Models\ContentBlock;
use App\Models\User;
use App\Support\Cms;

function makeReparatieAdmin(): User
{
    $u = User::factory()->create(['email_verified_at' => now()]);
    $u->role = 'admin';
    $u->save();

    return $u;
}

it('shows the sidebar defaults on the reparatie page', function () {
    $this->get('/reparatie-aanmelden')
        ->assertOk()
        ->assertSee('Waarom aanmelden?', false)
        ->assertSee('Snellere verwerking', false)
        ->assertSee('055 203 21 45', false);
});

it('renders CMS-edited sidebar content on the reparatie page', function () {
    ContentBlock::updateOrCreate(
        ['page' => 'reparatie', 'section' => 'hero', 'block_key' => 'why_title'],
        ['type' => 'text', 'value' => 'Waarom bij Slimme-PC?']
    );
    ContentBlock::updateOrCreate(
        ['page' => 'reparatie', 'section' => 'hero', 'block_key' => 'why_phone_label'],
        ['type' => 'text', 'value' => '088 123 4567']
    );
    Cms::bust();

    $this->get('/reparatie-aanmelden')
        ->assertOk()
        ->assertSee('Waarom bij Slimme-PC?', false)
        ->assertSee('088 123 4567', false);
});

it('shows the sidebar controls at the end of the hero admin page', function () {
    $this->actingAs(makeReparatieAdmin())
        ->get('/admin/content/reparatie/section/hero')
        ->assertOk()
        ->assertSee('Zijbalk titel', false)
        ->assertSee('WhatsApp nummer', false);
});
