<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('employees can open the visit create page', function (): void {
    $employee = User::factory()->create([
        'role' => 'employee',
    ]);

    $response = $this->actingAs($employee)->get(route('visits.create'));

    $response->assertSuccessful();
    $response->assertSee('Nieuw bezoek');
});

test('visitors are redirected from visit create to mailbox create', function (): void {
    $visitor = User::factory()->create([
        'role' => 'visitor',
    ]);

    $response = $this->actingAs($visitor)->get(route('visits.create'));

    $response->assertForbidden();
});
