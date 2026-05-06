<?php

// User Story #9: Medewerkers beheren
// Als beheerder wil ik accounts kunnen aanmaken en beheren,
// zodat gebruikers toegang hebben.
//
// Acceptatiecriteria:
// ✅ Nieuwe gebruiker toevoegen met rol
// ✅ Rollen toewijzen (receptionist/medewerker/admin)
// ✅ Gebruiker bewerken (rol aanpassen)
// ✅ Gebruiker verwijderen

use App\Models\User;

// ✅ SLAAGT — nieuwe gebruiker wordt aangemaakt met de juiste rol
test('beheerder kan een nieuwe gebruiker aanmaken met een rol', function () {
    $this->post(route('users.store'), [
        'name' => 'Sophie Receptionist',
        'email' => 'sophie@example.com',
        'password' => 'geheim123',
        'password_confirmation' => 'geheim123',
        'role' => 'visitor',
    ])->assertRedirect(route('users.index'));

    $user = User::where('email', 'sophie@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->role)->toBe('visitor');
});

// ✅ SLAAGT — rollen employee en admin zijn ook geldig bij aanmaken
test('beheerder kan gebruikers aanmaken met verschillende rollen', function (string $role) {
    $this->post(route('users.store'), [
        'name' => 'Test Gebruiker',
        'email' => "test_{$role}@example.com",
        'password' => 'geheim123',
        'password_confirmation' => 'geheim123',
        'role' => $role,
    ])->assertRedirect(route('users.index'));

    expect(User::where('role', $role)->exists())->toBeTrue();
})->with(['admin', 'employee', 'visitor']);

// ✅ SLAAGT — bestaande gebruiker kan worden verwijderd
test('beheerder kan een gebruiker verwijderen', function () {
    $user = User::factory()->create(['role' => 'visitor']);

    $this->delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'));

    expect(User::find($user->id))->toBeNull();
});
