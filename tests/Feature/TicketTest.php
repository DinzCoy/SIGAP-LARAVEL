<?php

use App\Models\Asset;
use App\Models\DeviceName;
use App\Models\Role;
use App\Models\Room;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;

function buatUserTiket(int $roleId): User
{
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['id' => $roleId], ['name' => "Role-{$roleId}"]);
    $user->roles()->attach($role->id);
    return $user;
}

function buatRuanganTiket(?int $picId = null): Room
{
    return Room::create([
        'name'   => 'Ruang Tiket ' . uniqid(),
        'slug'   => 'ruang-tiket-' . uniqid(),
        'pic_id' => $picId,
    ]);
}

function buatAsetTiket(?int $roomId = null, ?int $userId = null): Asset
{
    $deviceName = DeviceName::create([
        'brand' => 'Dell',
        'name'  => 'OptiPlex ' . uniqid(),
        'type'  => 'Desktop',
    ]);

    return Asset::create([
        'device_name_id' => $deviceName->id,
        'serial_number'  => 'SN-TIKET-' . uniqid(),
        'status_kondisi' => Asset::KONDISI_BAIK,
        'room_id'        => $roomId,
        'user_id'        => $userId,
    ]);
}

function buatTiket(int $reportedById, string $status = null, ?int $roomId = null, ?int $assetId = null): Ticket
{
    return Ticket::create([
        'type'        => 'General',
        'title'       => 'Test Tiket ' . uniqid(),
        'description' => 'Deskripsi test tiket yang cukup panjang untuk memenuhi validasi',
        'priority'    => 'Sedang',
        'status'      => $status ?? Ticket::STATUS_MENUNGGU_PENGELOLA,
        'reported_by' => $reportedById,
        'room_id'     => $roomId,
        'asset_id'    => $assetId,
    ]);
}

test('user biasa dapat membuat tiket baru', function () {
    $user = buatUserTiket(User::ROLE_USER);
    $room = buatRuanganTiket();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post('/tickets', [
            'title'       => 'Komputer saya error',
            'description' => 'Komputer saya tidak bisa menyala sejak tadi pagi, sudah dicoba restart tapi tetap error',
            'priority'    => 'Sedang',
            'room_id'     => $room->id,
        ]);

    $response->assertRedirect(route('tickets.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('tickets', [
        'title'       => 'Komputer saya error',
        'reported_by' => $user->id,
        'status'      => Ticket::STATUS_MENUNGGU_PENGELOLA,
    ]);
});

test('validasi menolak tiket tanpa judul', function () {
    $user = buatUserTiket(User::ROLE_USER);
    $room = buatRuanganTiket();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post('/tickets', [
            'description' => 'Deskripsi ada tapi judul tidak',
            'priority'    => 'Sedang',
            'room_id'     => $room->id,
        ]);

    $response->assertSessionHasErrors(['title']);
});

test('validasi menolak prioritas yang tidak valid', function () {
    $user = buatUserTiket(User::ROLE_USER);
    $room = buatRuanganTiket();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post('/tickets', [
            'title'       => 'Test Tiket',
            'description' => 'Deskripsi yang cukup panjang untuk memenuhi validasi minimum',
            'priority'    => 'SuperTinggi',
            'room_id'     => $room->id,
        ]);

    $response->assertSessionHasErrors(['priority']);
});

test('user biasa hanya melihat tiketnya sendiri di daftar', function () {
    $user1 = buatUserTiket(User::ROLE_USER);
    $user2 = buatUserTiket(User::ROLE_USER);

    buatTiket($user1->id);
    buatTiket($user2->id);

    $response = $this->actingAs($user1)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get('/tickets');

    $response->assertStatus(200);
    $response->assertViewIs('tickets.index');
});

test('admin dapat mengakses daftar semua tiket', function () {
    $admin = buatUserTiket(User::ROLE_ADMIN);
    $user = buatUserTiket(User::ROLE_USER);
    buatTiket($user->id);

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/tickets');

    $response->assertStatus(200);
});

test('teknisi dapat mengakses daftar tiket yang ditugaskan padanya', function () {
    $teknisi = buatUserTiket(User::ROLE_TEKNISI);
    $user = buatUserTiket(User::ROLE_USER);

    $tiket = buatTiket($user->id, Ticket::STATUS_KE_TEKNISI);
    $tiket->technician_id = $teknisi->id;
    $tiket->save();

    $response = $this->actingAs($teknisi)
        ->withSession(['active_role_id' => User::ROLE_TEKNISI])
        ->get('/tickets');

    $response->assertStatus(200);
});

test('pelapor dapat melihat detail tiketnya sendiri', function () {
    $user = buatUserTiket(User::ROLE_USER);
    $tiket = buatTiket($user->id);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get("/tickets/{$tiket->id}");

    $response->assertStatus(200);
    $response->assertViewIs('tickets.show');
});

test('user lain tidak dapat melihat tiket orang lain', function () {
    $user1 = buatUserTiket(User::ROLE_USER);
    $user2 = buatUserTiket(User::ROLE_USER);
    $tiket = buatTiket($user1->id);

    $response = $this->actingAs($user2)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get("/tickets/{$tiket->id}");

    $response->assertStatus(403);
});

test('admin dapat melihat detail tiket siapapun', function () {
    $user = buatUserTiket(User::ROLE_USER);
    $admin = buatUserTiket(User::ROLE_ADMIN);
    $tiket = buatTiket($user->id);

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get("/tickets/{$tiket->id}");

    $response->assertStatus(200);
});

test('pelapor dapat menambah balasan ke tiketnya sendiri', function () {
    $user = buatUserTiket(User::ROLE_USER);
    $tiket = buatTiket($user->id);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post("/tickets/{$tiket->id}/reply", [
            'message' => 'Ini balasan dari pelapor, ada update terbaru tentang masalah ini.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('ticket_replies', [
        'ticket_id' => $tiket->id,
        'user_id'   => $user->id,
        'message'   => 'Ini balasan dari pelapor, ada update terbaru tentang masalah ini.',
    ]);
});

test('teknisi dapat membalas tiket yang ditugaskan padanya', function () {
    $user = buatUserTiket(User::ROLE_USER);
    $teknisi = buatUserTiket(User::ROLE_TEKNISI);
    $tiket = buatTiket($user->id, Ticket::STATUS_KE_TEKNISI);
    $tiket->technician_id = $teknisi->id;
    $tiket->save();

    $response = $this->actingAs($teknisi)
        ->withSession(['active_role_id' => User::ROLE_TEKNISI])
        ->post("/tickets/{$tiket->id}/reply", [
            'message' => 'Sedang dalam proses perbaikan, estimasi selesai 2 jam lagi.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('user lain tidak dapat membalas tiket orang lain', function () {
    $user1 = buatUserTiket(User::ROLE_USER);
    $user2 = buatUserTiket(User::ROLE_USER);
    $tiket = buatTiket($user1->id);

    $response = $this->actingAs($user2)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post("/tickets/{$tiket->id}/reply", [
            'message' => 'Coba reply tiket orang lain',
        ]);

    $response->assertStatus(403);
});

test('admin dapat mengubah status tiket ke Diteruskan ke Ketua Tim', function () {
    $user = buatUserTiket(User::ROLE_USER);
    $admin = buatUserTiket(User::ROLE_ADMIN);
    $tiket = buatTiket($user->id);

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->patch("/tickets/{$tiket->id}/status", [
            'status' => Ticket::STATUS_KE_KETUA_TIM,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('tickets', [
        'id'     => $tiket->id,
        'status' => Ticket::STATUS_KE_KETUA_TIM,
    ]);
});

test('ketua tim dapat menugaskan teknisi ke tiket', function () {
    $user = buatUserTiket(User::ROLE_USER);
    $ketuaTim = buatUserTiket(User::ROLE_KETUA_TIM);
    $teknisi = buatUserTiket(User::ROLE_TEKNISI);
    $tiket = buatTiket($user->id, Ticket::STATUS_KE_KETUA_TIM);

    $response = $this->actingAs($ketuaTim)
        ->withSession(['active_role_id' => User::ROLE_KETUA_TIM])
        ->patch("/tickets/{$tiket->id}/status", [
            'status'        => Ticket::STATUS_KE_TEKNISI,
            'technician_id' => $teknisi->id,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('tickets', [
        'id'            => $tiket->id,
        'status'        => Ticket::STATUS_KE_TEKNISI,
        'technician_id' => $teknisi->id,
    ]);
});

test('teknisi dapat mengubah status tiket ke In Progress', function () {
    $user = buatUserTiket(User::ROLE_USER);
    $teknisi = buatUserTiket(User::ROLE_TEKNISI);
    $tiket = buatTiket($user->id, Ticket::STATUS_KE_TEKNISI);
    $tiket->technician_id = $teknisi->id;
    $tiket->save();

    $response = $this->actingAs($teknisi)
        ->withSession(['active_role_id' => User::ROLE_TEKNISI])
        ->patch("/tickets/{$tiket->id}/status", [
            'status' => Ticket::STATUS_IN_PROGRESS,
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('tickets', [
        'id'     => $tiket->id,
        'status' => Ticket::STATUS_IN_PROGRESS,
    ]);
});

test('tiket yang selesai mencatat waktu resolved_at', function () {
    $user = buatUserTiket(User::ROLE_USER);
    $admin = buatUserTiket(User::ROLE_ADMIN);
    $tiket = buatTiket($user->id, Ticket::STATUS_IN_PROGRESS);

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->patch("/tickets/{$tiket->id}/status", [
            'status' => Ticket::STATUS_SELESAI,
        ]);

    $response->assertRedirect();

    $tiket->refresh();
    $this->assertNotNull($tiket->resolved_at);
});

test('pic ruangan dapat mengakses form laporan ruangan', function () {
    $picUser = buatUserTiket(User::ROLE_PIC_RUANGAN);

    $response = $this->actingAs($picUser)
        ->withSession(['active_role_id' => User::ROLE_PIC_RUANGAN])
        ->get('/ruangan/lapor');

    $response->assertStatus(200);
});

test('pic ruangan dapat melaporkan kerusakan aset di ruangannya', function () {
    $picUser = buatUserTiket(User::ROLE_PIC_RUANGAN);
    $room = buatRuanganTiket($picUser->id);
    $aset = buatAsetTiket($room->id);

    $response = $this->actingAs($picUser)
        ->withSession(['active_role_id' => User::ROLE_PIC_RUANGAN])
        ->post('/ruangan/lapor', [
            'asset_id'    => $aset->id,
            'title'       => 'Monitor ruangan rusak',
            'description' => 'Layar monitor di ruangan ini tiba-tiba mati dan tidak bisa dinyalakan kembali',
            'priority'    => 'Sedang',
            'category'    => 'Service',
        ]);

    $response->assertRedirect(route('tickets.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('tickets', [
        'asset_id'    => $aset->id,
        'reported_by' => $picUser->id,
    ]);
});

test('pic ruangan tidak bisa melaporkan aset dari ruangan orang lain', function () {
    $pic1 = buatUserTiket(User::ROLE_PIC_RUANGAN);
    $pic2 = buatUserTiket(User::ROLE_PIC_RUANGAN);
    $ruangan2 = buatRuanganTiket($pic2->id);
    $asetDiRuangan2 = buatAsetTiket($ruangan2->id);

    $response = $this->actingAs($pic1)
        ->withSession(['active_role_id' => User::ROLE_PIC_RUANGAN])
        ->post('/ruangan/lapor', [
            'asset_id'    => $asetDiRuangan2->id,
            'title'       => 'Coba lapor aset orang lain',
            'description' => 'Ini deskripsi yang cukup panjang untuk lolos validasi',
            'priority'    => 'Rendah',
            'category'    => 'Service',
        ]);

    $response->assertStatus(403);
});
