<?php

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\DeviceName;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;

function buatUserDenganRoleAset(int $roleId): User
{
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['id' => $roleId], ['name' => "Role-{$roleId}"]);
    $user->roles()->attach($role->id);
    return $user;
}

function buatRuangan(): Room
{
    return Room::create([
        'name' => 'Ruang Test ' . uniqid(),
        'slug' => 'ruang-test-' . uniqid(),
    ]);
}

function buatDeviceName(): DeviceName
{
    return DeviceName::create([
        'brand' => 'Lenovo',
        'name'  => 'ThinkPad X1 ' . uniqid(),
        'type'  => 'Laptop',
    ]);
}

function buatAset(?int $roomId = null, ?int $userId = null): Asset
{
    $deviceName = buatDeviceName();
    return Asset::create([
        'device_name_id' => $deviceName->id,
        'serial_number'  => 'SN-' . uniqid(),
        'status_kondisi' => Asset::KONDISI_BAIK,
        'room_id'        => $roomId,
        'user_id'        => $userId,
    ]);
}

test('admin dapat melihat daftar aset', function () {
    $user = buatUserDenganRoleAset(User::ROLE_ADMIN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/asset-manager/aset');

    $response->assertStatus(200);
    $response->assertViewIs('assets.index');
});

test('pengelola barang dapat melihat daftar aset', function () {
    $user = buatUserDenganRoleAset(User::ROLE_PENGELOLA_ASET);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_PENGELOLA_ASET])
        ->get('/asset-manager/aset');

    $response->assertStatus(200);
});

test('admin dapat menambah aset baru', function () {
    $user = buatUserDenganRoleAset(User::ROLE_ADMIN);
    $deviceName = buatDeviceName();
    $room = buatRuangan();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/asset-manager/store', [
            'device_name_id' => $deviceName->id,
            'bmn_number'     => 'BMN-' . uniqid(),
            'serial_number'  => 'SN-BARU-' . uniqid(),
            'status_kondisi' => Asset::KONDISI_BAIK,
            'room_id'        => $room->id,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('pengelola barang dapat menambah aset baru', function () {
    $user = buatUserDenganRoleAset(User::ROLE_PENGELOLA_ASET);
    $deviceName = buatDeviceName();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_PENGELOLA_ASET])
        ->post('/asset-manager/store', [
            'device_name_id' => $deviceName->id,
            'bmn_number'     => 'BMN-PENGELOLA-' . uniqid(),
            'serial_number'  => 'SN-PENGELOLA-' . uniqid(),
            'status_kondisi' => Asset::KONDISI_BAIK,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('validasi menolak tambah aset tanpa device_name_id', function () {
    $user = buatUserDenganRoleAset(User::ROLE_ADMIN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/asset-manager/store', [
            'bmn_number'     => 'BMN-INVALID-' . uniqid(),
            'serial_number'  => 'SN-INVALID',
            'status_kondisi' => Asset::KONDISI_BAIK,
        ]);

    $response->assertSessionHasErrors(['device_name_id']);
});

test('admin dapat mengedit data aset', function () {
    $user = buatUserDenganRoleAset(User::ROLE_ADMIN);
    $aset = buatAset();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->put("/asset-manager/{$aset->id}", [
            'device_name_id' => $aset->device_name_id,
            'bmn_number'     => 'BMN-UPDATED-' . uniqid(),
            'serial_number'  => 'SN-UPDATED-' . uniqid(),
            'status_kondisi' => Asset::KONDISI_RUSAK_RINGAN,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('ketua tim juga dapat mengedit aset (role 7 diizinkan di route)', function () {
    $user = buatUserDenganRoleAset(User::ROLE_KETUA_TIM);
    $aset = buatAset();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_KETUA_TIM])
        ->put("/asset-manager/{$aset->id}", [
            'device_name_id' => $aset->device_name_id,
            'bmn_number'     => 'BMN-KETUA-' . uniqid(),
            'serial_number'  => 'SN-KETUA-EDIT',
            'status_kondisi' => Asset::KONDISI_BAIK,
        ]);

    $response->assertRedirect();
});

test('admin dapat menghapus aset', function () {
    $user = buatUserDenganRoleAset(User::ROLE_ADMIN);
    $aset = buatAset();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->delete("/asset-manager/{$aset->id}");

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('assets', ['id' => $aset->id]);
});

test('pengelola barang dapat menghapus aset', function () {
    $user = buatUserDenganRoleAset(User::ROLE_PENGELOLA_ASET);
    $aset = buatAset();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_PENGELOLA_ASET])
        ->delete("/asset-manager/{$aset->id}");

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('assets', ['id' => $aset->id]);
});

test('ketua tim juga dapat menghapus aset (role 7 diizinkan di route)', function () {
    $user = buatUserDenganRoleAset(User::ROLE_KETUA_TIM);
    $aset = buatAset();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_KETUA_TIM])
        ->delete("/asset-manager/{$aset->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('assets', ['id' => $aset->id]);
});

test('user biasa tidak bisa menghapus aset', function () {
    $user = buatUserDenganRoleAset(User::ROLE_USER);
    $aset = buatAset();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->delete("/asset-manager/{$aset->id}");

    $response->assertStatus(403);
});

test('pengguna authenticated dapat mencetak label QR aset', function () {
    $user = buatUserDenganRoleAset(User::ROLE_USER);
    $aset = buatAset();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get("/assets/{$aset->id}/print");

    $response->assertStatus(200);
    $response->assertViewIs('assets.print');
});

test('pengguna authenticated dapat melihat halaman scan QR aset', function () {
    $user = buatUserDenganRoleAset(User::ROLE_USER);
    $aset = buatAset();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get("/assets/{$aset->id}/scan");

    $response->assertStatus(200);
    $response->assertViewIs('assets.scan');
});

test('user dapat mengajukan peminjaman aset', function () {
    $pemilik = buatUserDenganRoleAset(User::ROLE_USER);
    $peminjam = buatUserDenganRoleAset(User::ROLE_USER);
    $aset = buatAset(null, $pemilik->id);

    $response = $this->actingAs($peminjam)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post("/assets/{$aset->id}/loan", [
            'loan_reason' => 'Butuh laptop untuk presentasi hari ini',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('asset_loans', [
        'asset_id'    => $aset->id,
        'borrower_id' => $peminjam->id,
        'status'      => AssetLoan::STATUS_PENDING,
    ]);
});

test('validasi menolak peminjaman dengan alasan terlalu pendek', function () {
    $user = buatUserDenganRoleAset(User::ROLE_USER);
    $aset = buatAset();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post("/assets/{$aset->id}/loan", [
            'loan_reason' => 'Mau',
        ]);

    $response->assertSessionHasErrors(['loan_reason']);
});

test('pemilik aset sendiri tidak bisa meminjam miliknya sendiri', function () {
    $user = buatUserDenganRoleAset(User::ROLE_USER);
    $aset = buatAset(null, $user->id);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post("/assets/{$aset->id}/loan", [
            'loan_reason' => 'Coba pinjam aset sendiri',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('info');
});

test('user dapat melakukan serah terima aset yang bukan miliknya', function () {
    $pemilikLama = buatUserDenganRoleAset(User::ROLE_USER);
    $pemilikBaru = buatUserDenganRoleAset(User::ROLE_USER);
    $aset = buatAset(null, $pemilikLama->id);

    $response = $this->actingAs($pemilikBaru)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post("/assets/{$aset->id}/takeover");

    $response->assertRedirect(route('assets.scan', $aset->id));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('assets', [
        'id'      => $aset->id,
        'user_id' => $pemilikBaru->id,
    ]);
});

test('user tidak bisa takeover aset yang sudah jadi miliknya', function () {
    $user = buatUserDenganRoleAset(User::ROLE_USER);
    $aset = buatAset(null, $user->id);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post("/assets/{$aset->id}/takeover");

    $response->assertRedirect();
    $response->assertSessionHas('info');
});

test('admin dapat melihat daftar ruangan', function () {
    $user = buatUserDenganRoleAset(User::ROLE_ADMIN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/asset-manager/rooms');

    $response->assertStatus(200);
});

test('admin dapat membuat ruangan baru', function () {
    $user = buatUserDenganRoleAset(User::ROLE_ADMIN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/asset-manager/rooms', [
            'name' => 'Ruang Server ' . uniqid(),
            'slug' => 'ruang-server-' . uniqid(),
        ]);

    $response->assertRedirect();
});

test('pengelola barang dapat membuat ruangan baru', function () {
    $user = buatUserDenganRoleAset(User::ROLE_PENGELOLA_ASET);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_PENGELOLA_ASET])
        ->post('/asset-manager/rooms', [
            'name' => 'Ruang Baru ' . uniqid(),
            'slug' => 'ruang-baru-' . uniqid(),
        ]);

    $response->assertRedirect();
});

test('ketua tim diblokir dari membuat ruangan', function () {
    $user = buatUserDenganRoleAset(User::ROLE_KETUA_TIM);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_KETUA_TIM])
        ->post('/asset-manager/rooms', [
            'name' => 'Ruang Hack',
            'slug' => 'ruang-hack',
        ]);

    $response->assertStatus(403);
});

test('admin dapat melihat daftar nama perangkat', function () {
    $user = buatUserDenganRoleAset(User::ROLE_ADMIN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/asset-manager/device-names');

    $response->assertStatus(200);
});

test('admin dapat membuat nama perangkat baru', function () {
    $user = buatUserDenganRoleAset(User::ROLE_ADMIN);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/asset-manager/device-names', [
            'brand' => 'HP',
            'name'  => 'EliteBook ' . uniqid(),
            'type'  => 'Laptop',
        ]);

    $response->assertRedirect();
});

test('ketua tim diblokir dari membuat nama perangkat baru', function () {
    $user = buatUserDenganRoleAset(User::ROLE_KETUA_TIM);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_KETUA_TIM])
        ->post('/asset-manager/device-names', [
            'brand' => 'Acer',
            'name'  => 'Aspire',
            'type'  => 'Laptop',
        ]);

    $response->assertStatus(403);
});

test('admin dapat mengupdate ruangan aset lewat API', function () {
    $admin = buatUserDenganRoleAset(User::ROLE_ADMIN);
    $aset = buatAset();
    $room = buatRuangan();

    $response = $this->actingAs($admin)
        ->withHeaders([
            'X-Active-Role-ID' => User::ROLE_ADMIN,
            'Accept' => 'application/json',
            'SUPER-API-KEY' => 'SIGAP_SECRET_API_KEY_2026',
        ])
        ->postJson("/api/admin/assets/{$aset->id}/room", [
            'room_id' => $room->id,
        ]);

    $response->assertStatus(200);
    $response->assertJsonPath('data.room', $room->name);
    $this->assertEquals($room->id, $aset->fresh()->room_id);

    $this->assertDatabaseHas('asset_movement_logs', [
        'asset_id' => $aset->id,
        'old_room_id' => null,
        'new_room_id' => $room->id,
        'action_type' => 'room_change',
    ]);
});

test('admin dapat mengupdate ruangan aset dengan membuat ruangan baru lewat API', function () {
    $admin = buatUserDenganRoleAset(User::ROLE_ADMIN);
    $aset = buatAset();
    $newRoomName = 'Ruangan Baru Dari Test';

    $response = $this->actingAs($admin)
        ->withHeaders([
            'X-Active-Role-ID' => User::ROLE_ADMIN,
            'Accept' => 'application/json',
            'SUPER-API-KEY' => 'SIGAP_SECRET_API_KEY_2026',
        ])
        ->postJson("/api/admin/assets/{$aset->id}/room", [
            'new_room_name' => $newRoomName,
        ]);

    $response->assertStatus(200);
    $response->assertJsonPath('data.room', $newRoomName);
    $this->assertDatabaseHas('rooms', ['name' => $newRoomName]);
});

test('role selain admin tidak dapat mengupdate ruangan aset lewat API', function () {
    $user = buatUserDenganRoleAset(User::ROLE_USER);
    $aset = buatAset();
    $room = buatRuangan();

    $response = $this->actingAs($user)
        ->withHeaders([
            'X-Active-Role-ID' => User::ROLE_USER,
            'Accept' => 'application/json',
            'SUPER-API-KEY' => 'SIGAP_SECRET_API_KEY_2026',
        ])
        ->postJson("/api/admin/assets/{$aset->id}/room", [
            'room_id' => $room->id,
        ]);

    $response->assertStatus(403);
});

test('guest tidak dapat mengupdate ruangan aset lewat API', function () {
    $aset = buatAset();
    $room = buatRuangan();

    $response = $this->postJson("/api/admin/assets/{$aset->id}/room", [
        'room_id' => $room->id,
    ], [
        'Accept' => 'application/json',
        'SUPER-API-KEY' => 'SIGAP_SECRET_API_KEY_2026',
    ]);

    $response->assertStatus(401);
});
