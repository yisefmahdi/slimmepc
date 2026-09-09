<?php

use App\Models\User;

it('shows the redesigned account page', function () {
    $user = User::factory()->create(['name' => 'Jan Jansen']);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertViewHasAll(['user', 'c', 'design'])
        ->assertSee('Mijn account')
        ->assertSee('Hallo, Jan')
        ->assertSee('Profielgegevens')
        ->assertSee('Wachtwoord wijzigen')
        ->assertSee('Account verwijderen')
        ->assertSee('Mijn bestellingen');
});

it('updates the profile name and email', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', ['name' => 'Piet Pietersen', 'email' => $user->email])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    expect($user->fresh()->name)->toBe('Piet Pietersen');
});
