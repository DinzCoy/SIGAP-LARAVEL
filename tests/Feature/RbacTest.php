<?php

use App\Models\Role;
use App\Models\User;

function buatUserDenganRole(int $roleId): User
{
    $user = User::factory()->create();

    $role = Role::firstOrCreate(['id' => $roleId], ['name' => "Role-{$roleId}"]);
    $user->roles()->attach($role->id);
    return $user;
}

test('admin diarahkan ke admin.dashboard setelah login', function () {
    $user = buatUserDenganRole(User::ROLE_ADMIN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/dashboard');

    $response->assertRedirect(route('admin.dashboard'));
});

test('pimpinan diarahkan ke pimpinan.dashboard setelah login', function () {
    $user = buatUserDenganRole(User::ROLE_PIMPINAN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_PIMPINAN])
        ->get('/dashboard');

    $response->assertRedirect(route('pimpinan.dashboard'));
});

test('teknisi diarahkan ke teknisi.dashboard setelah login', function () {
    $user = buatUserDenganRole(User::ROLE_TEKNISI);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_TEKNISI])
        ->get('/dashboard');

    $response->assertRedirect(route('teknisi.dashboard'));
});

test('pengelola ruangan diarahkan ke ruangan.dashboard setelah login', function () {
    $user = buatUserDenganRole(User::ROLE_PIC_RUANGAN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_PIC_RUANGAN])
        ->get('/dashboard');

    $response->assertRedirect(route('ruangan.dashboard'));
});

test('user biasa diarahkan ke user.dashboard setelah login', function () {
    $user = buatUserDenganRole(User::ROLE_USER);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get('/dashboard');

    $response->assertRedirect(route('user.dashboard'));
});

test('ketua tim diarahkan ke ketua_tim.dashboard setelah login', function () {
    $user = buatUserDenganRole(User::ROLE_KETUA_TIM);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_KETUA_TIM])
        ->get('/dashboard');

    $response->assertRedirect(route('ketua_tim.dashboard'));
});

test('admin dapat mengakses admin dashboard', function () {
    $user = buatUserDenganRole(User::ROLE_ADMIN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/admin/dashboard');

    $response->assertStatus(200);
});

test('non-admin diblokir dari admin dashboard dengan 403', function () {
    $user = buatUserDenganRole(User::ROLE_TEKNISI);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_TEKNISI])
        ->get('/admin/dashboard');

    $response->assertStatus(403);
});

test('pimpinan dapat mengakses pimpinan dashboard', function () {
    $user = buatUserDenganRole(User::ROLE_PIMPINAN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_PIMPINAN])
        ->get('/pimpinan/dashboard');

    $response->assertStatus(200);
});

test('non-pimpinan diblokir dari pimpinan dashboard dengan 403', function () {
    $user = buatUserDenganRole(User::ROLE_USER);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get('/pimpinan/dashboard');

    $response->assertStatus(403);
});

test('teknisi dapat mengakses teknisi dashboard', function () {
    $user = buatUserDenganRole(User::ROLE_TEKNISI);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_TEKNISI])
        ->get('/teknisi/dashboard');

    $response->assertStatus(200);
});

test('user biasa diblokir dari teknisi dashboard dengan 403', function () {
    $user = buatUserDenganRole(User::ROLE_USER);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get('/teknisi/dashboard');

    $response->assertStatus(403);
});

test('pengelola ruangan dapat mengakses ruangan dashboard', function () {
    $user = buatUserDenganRole(User::ROLE_PIC_RUANGAN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_PIC_RUANGAN])
        ->get('/ruangan/dashboard');

    $response->assertStatus(200);
});

test('ketua tim dapat mengakses ketua-tim dashboard', function () {
    $user = buatUserDenganRole(User::ROLE_KETUA_TIM);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_KETUA_TIM])
        ->get('/ketua-tim/dashboard');

    $response->assertStatus(200);
});

test('user biasa dapat mengakses user dashboard', function () {
    $user = buatUserDenganRole(User::ROLE_USER);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get('/user/dashboard');

    $response->assertStatus(200);
});

test('admin dapat mengakses halaman detail laporan perangkat', function () {
    $user = buatUserDenganRole(User::ROLE_ADMIN);

    $report = \App\Models\PcReport::create([
        'mac_address' => '00:1A:2B:3C:4D:5E',
        'hostname' => 'Test-PC',
        'ip_address' => '127.0.0.1',
        'last_seen' => now(),
    ]);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get("/admin/reports/{$report->id}");

    $response->assertStatus(200);
});

test('non-admin tidak dapat mengakses detail laporan perangkat', function () {
    $user = buatUserDenganRole(User::ROLE_PENGELOLA_ASET);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_PENGELOLA_ASET])
        ->get('/admin/reports/1');

    $response->assertStatus(403);
});

test('admin dapat mengakses halaman pengaturan sistem', function () {
    $user = buatUserDenganRole(User::ROLE_ADMIN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/settings');

    $response->assertStatus(200);
});

test('non-admin diblokir dari halaman pengaturan dengan 403', function () {
    $user = buatUserDenganRole(User::ROLE_TEKNISI);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_TEKNISI])
        ->get('/settings');

    $response->assertStatus(403);
});

test('admin dapat mengakses halaman daftar aset', function () {
    $user = buatUserDenganRole(User::ROLE_ADMIN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/asset-manager/aset');

    $response->assertStatus(200);
});

test('pengelola barang dapat mengakses halaman daftar aset', function () {
    $user = buatUserDenganRole(User::ROLE_PENGELOLA_ASET);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_PENGELOLA_ASET])
        ->get('/asset-manager/aset');

    $response->assertStatus(200);
});

test('ketua tim dapat mengakses halaman daftar aset', function () {
    $user = buatUserDenganRole(User::ROLE_KETUA_TIM);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_KETUA_TIM])
        ->get('/asset-manager/aset');

    $response->assertStatus(200);
});

test('user biasa diblokir dari asset manager dengan 403', function () {
    $user = buatUserDenganRole(User::ROLE_USER);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get('/asset-manager/aset');

    $response->assertStatus(403);
});

test('teknisi diblokir dari asset manager dengan 403', function () {
    $user = buatUserDenganRole(User::ROLE_TEKNISI);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_TEKNISI])
        ->get('/asset-manager/aset');

    $response->assertStatus(403);
});

test('admin dapat mengakses halaman user management', function () {
    $user = buatUserDenganRole(User::ROLE_ADMIN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/user-management');

    $response->assertStatus(200);
});

test('non-admin diblokir dari user management dengan 403', function () {
    $user = buatUserDenganRole(User::ROLE_PENGELOLA_ASET);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_PENGELOLA_ASET])
        ->get('/user-management');

    $response->assertStatus(403);
});

test('guest diarahkan ke login saat mengakses admin dashboard', function () {
    $response = $this->get('/admin/dashboard');
    $response->assertRedirect('/login');
});

test('guest diarahkan ke login saat mengakses asset manager', function () {
    $response = $this->get('/asset-manager/aset');
    $response->assertRedirect('/login');
});

test('guest diarahkan ke login saat mengakses tiket', function () {
    $response = $this->get('/tickets');
    $response->assertRedirect('/login');
});

test('user bisa ganti ke role yang ia miliki', function () {
    $user = User::factory()->create();
    $roleAdmin = Role::firstOrCreate(['id' => User::ROLE_ADMIN], ['name' => 'Administrator']);
    $rolePimpinan = Role::firstOrCreate(['id' => User::ROLE_PIMPINAN], ['name' => 'Pimpinan']);
    $user->roles()->attach([$roleAdmin->id, $rolePimpinan->id]);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/switch-role/' . User::ROLE_PIMPINAN);

    $response->assertRedirect(route('dashboard'));
});

test('user tidak bisa ganti ke role yang tidak ia miliki', function () {
    $user = buatUserDenganRole(User::ROLE_USER);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post('/switch-role/' . User::ROLE_ADMIN);

    $response->assertSessionHas('error');
});
