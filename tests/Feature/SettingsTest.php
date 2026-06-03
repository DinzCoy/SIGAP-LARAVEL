<?php

use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WhitelistedIp;

function buatAdmin(): User
{
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['id' => User::ROLE_ADMIN], ['name' => 'Administrator']);
    $user->roles()->attach($role->id);
    return $user;
}

function buatUserBiasa(): User
{
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['id' => User::ROLE_USER], ['name' => 'User']);
    $user->roles()->attach($role->id);
    return $user;
}

test('admin dapat mengakses halaman settings', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/settings');

    $response->assertStatus(200);
    $response->assertViewIs('settings.index');
});

test('admin dapat memperbarui profil nama dan email', function () {
    $admin = buatAdmin();
    $namaLama = $admin->name;

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/profile', [
            'name'  => 'Admin Baru Test',
            'email' => 'admin.baru.' . uniqid() . '@bps.go.id',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $admin->refresh();
    expect($admin->name)->toBe('Admin Baru Test');
});

test('validasi menolak update profil dengan email yang sudah dipakai', function () {
    $admin1 = buatAdmin();
    $admin2 = buatAdmin();

    $response = $this->actingAs($admin1)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/profile', [
            'name'  => 'Admin Test',
            'email' => $admin2->email,
        ]);

    $response->assertSessionHasErrors(['email']);
});

test('admin dapat mengganti password dengan password lama yang benar', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/password', [
            'current_password'      => 'password',
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('validasi menolak ganti password jika current_password salah', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/password', [
            'current_password'      => 'password-salah',
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

    $response->assertSessionHasErrors(['current_password']);
});

test('validasi menolak ganti password jika konfirmasi tidak cocok', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/password', [
            'current_password'      => 'password',
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'BedaPassword!',
        ]);

    $response->assertSessionHasErrors(['password']);
});

test('admin dapat menyimpan konfigurasi sistem', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/system', [
            'api_key'    => 'kunci-api-test-yang-panjang-123',
            'server_url' => 'http://localhost:8080',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('validasi menolak api_key yang terlalu pendek', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/system', [
            'api_key'    => 'pendek',
            'server_url' => 'http://localhost',
        ]);

    $response->assertSessionHasErrors(['api_key']);
});

test('admin dapat memperbarui threshold anomali', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/thresholds', [
            'ram_threshold'         => 85,
            'disk_threshold_gb'     => 10,
            'report_interval_days'  => 7,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('validasi menolak ram_threshold di luar range 50-99', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/thresholds', [
            'ram_threshold'         => 100,
            'disk_threshold_gb'     => 10,
            'report_interval_days'  => 7,
        ]);

    $response->assertSessionHasErrors(['ram_threshold']);
});

test('admin dapat menambahkan IP ke whitelist', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/whitelist', [
            'ip_address' => '192.168.1.' . rand(100, 200),
            'label'      => 'PC Ujicoba',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('validasi menolak IP yang format tidak valid', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/whitelist', [
            'ip_address' => 'bukan-ip-valid',
        ]);

    $response->assertSessionHasErrors(['ip_address']);
});

test('validasi menolak IP yang sudah ada di whitelist', function () {
    $admin = buatAdmin();

    WhitelistedIp::create([
        'ip_address' => '10.0.0.99',
        'label'      => 'IP sudah ada',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/whitelist', [
            'ip_address' => '10.0.0.99',
        ]);

    $response->assertSessionHasErrors(['ip_address']);
});

test('admin dapat menghapus IP dari whitelist', function () {
    $admin = buatAdmin();
    $ip = WhitelistedIp::create([
        'ip_address' => '172.16.0.' . rand(1, 254),
        'label'      => 'IP untuk dihapus',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->delete("/settings/whitelist/{$ip->id}");

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('whitelisted_ips', ['id' => $ip->id]);
});

test('admin dapat memperbarui pengaturan retensi log', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/retention', [
            'log_retention_months' => 12,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('validasi menolak retensi log yang terlalu besar', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/retention', [
            'log_retention_months' => 100,
        ]);

    $response->assertSessionHasErrors(['log_retention_months']);
});

test('admin dapat memperbarui jadwal agent', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/agent-schedule', [
            'agent_schedule_hours' => '6,12,18',
            'agent_delay_per_room' => 300,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('validasi menolak agent_delay_per_room yang terlalu kecil', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/agent-schedule', [
            'agent_schedule_hours' => '8',
            'agent_delay_per_room' => 30,
        ]);

    $response->assertSessionHasErrors(['agent_delay_per_room']);
});

test('admin dapat membersihkan log lama', function () {
    $admin = buatAdmin();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/settings/clean-logs');

    $response->assertRedirect();
    $response->assertSessionHas('success');
});
