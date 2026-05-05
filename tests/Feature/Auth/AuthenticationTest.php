<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $role = \App\Models\Role::factory()->create();
    $user = User::factory()->create();
    $user->roles()->attach($role->id);

    $response = $this->post('/login', [
        'login' => $user->email,
        'password' => 'password',
        'role_id' => $role->id,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $role = \App\Models\Role::factory()->create();
    $user = User::factory()->create();
    $user->roles()->attach($role->id);

    $this->post('/login', [
        'login' => $user->email,
        'password' => 'wrong-password',
        'role_id' => $role->id,
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
